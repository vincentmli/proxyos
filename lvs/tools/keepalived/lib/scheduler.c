/*
 * Soft:        Keepalived is a failover program for the LVS project
 *              <www.linuxvirtualserver.org>. It monitor & manipulate
 *              a loadbalanced server pool using multi-layer checks.
 *
 * Part:        Scheduling framework. This code is highly inspired from
 *              the thread management routine (thread.c) present in the 
 *              very nice zebra project (http://www.zebra.org).
 *
 * Author:      Alexandre Cassen, <acassen@linux-vs.org>
 *
 *              This program is distributed in the hope that it will be useful, 
 *              but WITHOUT ANY WARRANTY; without even the implied warranty of 
 *              MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  
 *              See the GNU General Public License for more details.
 *
 *              This program is free software; you can redistribute it and/or
 *              modify it under the terms of the GNU General Public License
 *              as published by the Free Software Foundation; either version
 *              2 of the License, or (at your option) any later version.
 *
 * Copyright (C) 2001-2011 Alexandre Cassen, <acassen@linux-vs.org>
 */

#include <signal.h>
#include <sys/wait.h>
#include <sys/select.h>
#include <unistd.h>
#include "scheduler.h"
#include "memory.h"
#include "utils.h"
#include "signals.h"
#include "logger.h"
#include "epollwrapper.h"

/* global vars */
thread_master_t *master = NULL;

/* Make thread master. */
thread_master_t *
thread_make_master(void)
{
	thread_master_t *new;

	new = (thread_master_t *) MALLOC(sizeof (thread_master_t));
	epoll_init(&new->epfd, &new->events);
	return new;
}

/* Add a new thread to the list. */
static void
thread_list_add(thread_list_t * list, thread_t * thread)
{
	thread->next = NULL;
	thread->prev = list->tail;
	if (list->tail)
		list->tail->next = thread;
	else
		list->head = thread;
	list->tail = thread;
	list->count++;
}

/* Add a new thread to the list. */
void
thread_list_add_before(thread_list_t * list, thread_t * point, thread_t * thread)
{
	thread->next = point;
	thread->prev = point->prev;
	if (point->prev)
		point->prev->next = thread;
	else
		list->head = thread;
	point->prev = thread;
	list->count++;
}

/* Add a new thread to the head of list. */
void
thread_list_add_head(thread_list_t * list, thread_t * thread)
{
	thread->prev = NULL;
	thread->next = list->head;
	if (list->head)
		list->head->prev = thread;
	else
		list->tail = thread;
	list->head = thread;
	list->count++;
}

/* Add a new thread to the list. after a point */
void
thread_list_add_after(thread_list_t * list, thread_t * point, thread_t * thread)
{
	thread->prev = point;
	thread->next = point->next;
	if (point->next)
		point->next->prev = thread;
	else
		list->tail = thread;
	point->next = thread;
	list->count++;
}

/* Add a thread in the list sorted by timeval */
void
thread_list_add_timeval(thread_list_t * list, thread_t * thread)
{
	thread_t *tt;

	for (tt = list->tail; tt; tt = tt->prev) {
		if (timer_cmp(thread->sands, tt->sands) >= 0)
			break;
	}

	if (tt)
		thread_list_add_after(list, tt, thread);
	else
		thread_list_add_head(list, thread);
}

/* Delete a thread from the list. */
thread_t *
thread_list_delete(thread_list_t * list, thread_t * thread)
{
	if (thread->next)
		thread->next->prev = thread->prev;
	else
		list->tail = thread->prev;
	if (thread->prev)
		thread->prev->next = thread->next;
	else
		list->head = thread->next;
	thread->next = thread->prev = NULL;
	list->count--;
	return thread;
}

/* Free all unused thread. */
static void
thread_clean_unuse(thread_master_t * m)
{
	thread_t *thread;

	thread = m->unuse.head;
	while (thread) {
		thread_t *t;

		t = thread;
		thread = t->next;

		thread_list_delete(&m->unuse, t);

		/* free the thread */
		FREE(t);
		m->alloc--;
	}
}

/* Move thread to unuse list. */
static void
thread_add_unuse(thread_master_t * m, thread_t * thread)
{
	assert(m != NULL);
	assert(thread->next == NULL);
	assert(thread->prev == NULL);
	assert(thread->type == THREAD_UNUSED);
	thread_list_add(&m->unuse, thread);
}

/* Move list element to unuse queue */
static void
thread_destroy_list(thread_master_t * m, thread_list_t thread_list)
{
	thread_t *thread;

	thread = thread_list.head;

	while (thread) {
		thread_t *t;

		t = thread;
		thread = t->next;

		if (t->type == THREAD_READY_FD ||
		    t->type == THREAD_READ ||
		    t->type == THREAD_WRITE ||
		    t->type == THREAD_READ_TIMEOUT ||
		    t->type == THREAD_WRITE_TIMEOUT)
			close (t->u.fd);

		thread_list_delete(&thread_list, t);
		t->type = THREAD_UNUSED;
		thread_add_unuse(m, t);
	}
}

/* Cleanup master */
static void
thread_cleanup_master(thread_master_t * m)
{
	/* Unuse current thread lists */
	thread_destroy_list(m, m->read);
	thread_destroy_list(m, m->write);
	thread_destroy_list(m, m->timer);
	thread_destroy_list(m, m->event);
	thread_destroy_list(m, m->ready);
	thread_destroy_list(m, m->child);

	/* Clear epoll resources */
	epoll_cleanup(&m->epfd, &m->events);

	/* Clean garbage */
	thread_clean_unuse(m);
}

/* Stop thread scheduler. */
void
thread_destroy_master(thread_master_t * m)
{
	thread_cleanup_master(m);
	FREE(m);
}

/* Delete top of the list and return it. */
thread_t *
thread_trim_head(thread_list_t * list)
{
	if (list->head)
		return thread_list_delete(list, list->head);
	return NULL;
}

/* Make new thread. */
thread_t *
thread_new(thread_master_t * m)
{
	thread_t *new;

	/* If one thread is already allocated return it */
	if (m->unuse.head) {
		new = thread_trim_head(&m->unuse);
		memset(new, 0, sizeof (thread_t));
		return new;
	}

	new = (thread_t *) MALLOC(sizeof (thread_t));
	m->alloc++;
	return new;
}

/* Add new read thread. */
thread_t *
thread_add_read(thread_master_t * m, int (*func) (thread_t *)
		, void *arg, int fd, long timer)
{
	thread_t *thread;

	assert(m != NULL);

	if (epoll_fdisset(fd, DIR_RD)) {
		log_message(LOG_WARNING, "There is already read fd [%d]", fd);
		return NULL;
	}

	thread = thread_new(m);
	thread->type = THREAD_READ;
	thread->id = 0;
	thread->master = m;
	thread->func = func;
	thread->arg = arg;
	epoll_set_fd(m->epfd, DIR_RD, fd, thread);
	thread->u.fd = fd;

	/* Compute read timeout value */
	set_time_now();
	thread->sands = timer_add_long(time_now, timer);

	/* Sort the thread. */
	thread_list_add_timeval(&m->read, thread);

	return thread;
}

/* Add new write thread. */
thread_t *
thread_add_write(thread_master_t * m, int (*func) (thread_t *)
		 , void *arg, int fd, long timer)
{
	thread_t *thread;

	assert(m != NULL);

	if (epoll_fdisset(fd, DIR_WR)) {
		log_message(LOG_WARNING, "There is already write fd [%d]", fd);
		return NULL;
	}

	thread = thread_new(m);
	thread->type = THREAD_WRITE;
	thread->id = 0;
	thread->master = m;
	thread->func = func;
	thread->arg = arg;
	epoll_set_fd(m->epfd, DIR_WR, fd, thread);
	thread->u.fd = fd;

	/* Compute write timeout value */
	set_time_now();
	thread->sands = timer_add_long(time_now, timer);

	/* Sort the thread. */
	thread_list_add_timeval(&m->write, thread);

	return thread;
}

/* Add timer event thread. */
thread_t *
thread_add_timer(thread_master_t * m, int (*func) (thread_t *)
		 , void *arg, long timer)
{
	thread_t *thread;

	assert(m != NULL);

	thread = thread_new(m);
	thread->type = THREAD_TIMER;
	thread->id = 0;
	thread->master = m;
	thread->func = func;
	thread->arg = arg;

	/* Do we need jitter here? */
	set_time_now();
	thread->sands = timer_add_long(time_now, timer);

	/* Sort by timeval. */
	thread_list_add_timeval(&m->timer, thread);

	return thread;
}

/* Add a child thread. */
thread_t *
thread_add_child(thread_master_t * m, int (*func) (thread_t *)
		 , void * arg, pid_t pid, long timer)
{
	thread_t *thread;

	assert(m != NULL);

	thread = thread_new(m);
	thread->type = THREAD_CHILD;
	thread->id = 0;
	thread->master = m;
	thread->func = func;
	thread->arg = arg;
	thread->u.c.pid = pid;
	thread->u.c.status = 0;

	/* Compute write timeout value */
	set_time_now();
	thread->sands = timer_add_long(time_now, timer);

	/* Sort by timeval. */
	thread_list_add_timeval(&m->child, thread);

	return thread;
}

/* Add simple event thread. */
thread_t *
thread_add_event(thread_master_t * m, int (*func) (thread_t *)
		 , void *arg, int val)
{
	thread_t *thread;

	assert(m != NULL);

	thread = thread_new(m);
	thread->type = THREAD_EVENT;
	thread->id = 0;
	thread->master = m;
	thread->func = func;
	thread->arg = arg;
	thread->u.val = val;
	thread_list_add(&m->event, thread);

	return thread;
}

/* Add simple event thread. */
thread_t *
thread_add_terminate_event(thread_master_t * m)
{
	thread_t *thread;

	assert(m != NULL);

	thread = thread_new(m);
	thread->type = THREAD_TERMINATE;
	thread->id = 0;
	thread->master = m;
	thread->func = NULL;
	thread->arg = NULL;
	thread->u.val = 0;
	thread_list_add(&m->event, thread);

	return thread;
}

/* Cancel thread from scheduler. */
void
thread_cancel(thread_t * thread)
{
	switch (thread->type) {
	case THREAD_READ:
		assert(epoll_fdisset(thread->u.fd, DIR_RD));
		epoll_clear_fd(thread->master->epfd, DIR_RD, thread->u.fd);
		thread_list_delete(&thread->master->read, thread);
		break;
	case THREAD_WRITE:
		assert(epoll_fdisset(thread->u.fd, DIR_WR));
		epoll_clear_fd(thread->master->epfd, DIR_WR, thread->u.fd);
		thread_list_delete(&thread->master->write, thread);
		break;
	case THREAD_TIMER:
		thread_list_delete(&thread->master->timer, thread);
		break;
	case THREAD_CHILD:
		/* Does this need to kill the child, or is that the
		 * caller's job?
		 * This function is currently unused, so leave it for now.
		 */
		thread_list_delete(&thread->master->child, thread);
		break;
	case THREAD_EVENT:
		thread_list_delete(&thread->master->event, thread);
		break;
	case THREAD_READY:
	case THREAD_READY_FD:
		thread_list_delete(&thread->master->ready, thread);
		break;
	default:
		break;
	}

	thread->type = THREAD_UNUSED;
	thread_add_unuse(thread->master, thread);
}

/* Delete all events which has argument value arg. */
void
thread_cancel_event(thread_master_t * m, void *arg)
{
	thread_t *thread;

	thread = m->event.head;
	while (thread) {
		thread_t *t;

		t = thread;
		thread = t->next;

		if (t->arg == arg) {
			thread_list_delete(&m->event, t);
			t->type = THREAD_UNUSED;
			thread_add_unuse(m, t);
		}
	}
}

/* Update timer value */
static void
thread_update_timer(thread_list_t *list, TIMEVAL *timer_min)
{
	if (list->head) {
		if (!TIMER_ISNULL(*timer_min)) {
			if (timer_cmp(list->head->sands, *timer_min) <= 0) {
				*timer_min = list->head->sands;
			}
		} else {
			*timer_min = list->head->sands;
		}
	}
}

/* Compute the wait timer. Take care of timeouted fd */
static void
thread_compute_timer(thread_master_t * m, TIMEVAL * timer_wait)
{
	TIMEVAL timer_min;

	/* Prepare timer */
	TIMER_RESET(timer_min);
	thread_update_timer(&m->timer, &timer_min);
	thread_update_timer(&m->write, &timer_min);
	thread_update_timer(&m->read, &timer_min);
	thread_update_timer(&m->child, &timer_min);

	/* Take care about monothonic clock */
	if (!TIMER_ISNULL(timer_min)) {
		timer_min = timer_sub(timer_min, time_now);
		if (timer_min.tv_sec < 0) {
			timer_min.tv_sec = timer_min.tv_usec = 0;
		} else if (timer_min.tv_sec >= 1) {
			timer_min.tv_sec = 1;
			timer_min.tv_usec = 0;
		}

		timer_wait->tv_sec = timer_min.tv_sec;
		timer_wait->tv_usec = timer_min.tv_usec;
	} else {
		timer_wait->tv_sec = 1;
		timer_wait->tv_usec = 0;
	}
}

/* Fetch next ready thread. */
thread_t *
thread_fetch(thread_master_t * m, thread_t * fetch)
{
	int ret, old_errno;
	thread_t *thread;
	TIMEVAL timer_wait;
	int signal_fd;
	int i;

	assert(m != NULL);

	/* Timer initialization */
	memset(&timer_wait, 0, sizeof (TIMEVAL));

retry:	/* When thread can't fetch try to find next thread again. */

	/* If there is event process it first. */
	while ((thread = thread_trim_head(&m->event))) {
		*fetch = *thread;

		/* If daemon hanging event is received return NULL pointer */
		if (thread->type == THREAD_TERMINATE) {
			thread->type = THREAD_UNUSED;
			thread_add_unuse(m, thread);
			return NULL;
		}
		thread->type = THREAD_UNUSED;
		thread_add_unuse(m, thread);
		return fetch;
	}

	/* If there is ready threads process them */
	while ((thread = thread_trim_head(&m->ready))) {
		*fetch = *thread;
		thread->type = THREAD_UNUSED;
		thread_add_unuse(m, thread);
		return fetch;
	}

	/*
	 * Re-read the current time to get the maximum accuracy.
	 * Calculate select wait timer. Take care of timeouted fd.
	 */
	set_time_now();
	thread_compute_timer(m, &timer_wait);

	/* Call epoll function. */
	signal_fd = signal_rfd();
	epoll_set_fd(m->epfd, DIR_RD, signal_fd, NULL);

	ret = epoll_handler(m->epfd, m->events, &timer_wait);

	/* we have to save errno here because the next syscalls will set it */
	old_errno = errno;

	/* Update current time */
	set_time_now();

	if (ret < 0) {
		if (old_errno == EINTR)
			goto retry;
		/* Real error. */
		DBG("epoll error: %s", strerror(old_errno));
		assert(0);
	}

	for (i = 0; i < ret; i++) {
		int fd;
		thread_t *t;

		fd = m->events[i].data.fd;
		if (signal_fd == fd) {
			epoll_clear_fd(m->epfd, DIR_RD, fd);
			signal_run_callback();
			continue;
		}

		/* process read fd */
		if ((m->events[i].events & (EPOLLIN|EPOLLERR|EPOLLHUP)) && 
						epoll_fdisset(fd, DIR_RD)) {
			t = (thread_t *)get_data_by_fd(fd, DIR_RD);
			if (t != NULL) {
				epoll_clear_fd(m->epfd, DIR_RD, fd); 
				thread_list_delete(&m->read, t);
				thread_list_add(&m->ready, t);
				t->type = THREAD_READY_FD;
			}
		}

		/* process write fd */
		if ((m->events[i].events & (EPOLLOUT|EPOLLERR|EPOLLHUP)) &&
						epoll_fdisset(fd, DIR_WR)) {
			t = (thread_t *)get_data_by_fd(fd, DIR_WR);
			if (t != NULL) {
				epoll_clear_fd(m->epfd, DIR_WR, fd);
				thread_list_delete(&m->write, t);
				thread_list_add(&m->ready, t);
				t->type = THREAD_READY_FD;
			}
		}

		/* other fd */
		/* ... */
	}

	/* Timeout children */
	thread = m->child.head;
	while (thread) {
		thread_t *t;

		t = thread;
		thread = t->next;

		if (timer_cmp(time_now, t->sands) >= 0) {
			thread_list_delete(&m->child, t);
			thread_list_add(&m->ready, t);
			t->type = THREAD_CHILD_TIMEOUT;
		} else {
			break;
		}
	}

	/* Read thead. */
	thread = m->read.head;
	while (thread) {
		thread_t *t;

		t = thread;
		thread = t->next;

		if (timer_cmp(time_now, t->sands) >= 0) {
			epoll_clear_fd(m->epfd, DIR_RD, t->u.fd);
			thread_list_delete(&m->read, t);
			thread_list_add(&m->ready, t);
			t->type = THREAD_READ_TIMEOUT;
		} else {
			break;
		}
	}

	/* Write thead. */
	thread = m->write.head;
	while (thread) {
		thread_t *t;

		t = thread;
		thread = t->next;

		if (timer_cmp(time_now, t->sands) >= 0) {
			epoll_clear_fd(m->epfd, DIR_WR, t->u.fd);
			thread_list_delete(&m->write, t);
			thread_list_add(&m->ready, t);
			t->type = THREAD_WRITE_TIMEOUT;
		} else {
			break;
		}
	}

#if 0	/* select */

	/* Read thead. */
	thread = m->read.head;
	while (thread) {
		thread_t *t;

		t = thread;
		thread = t->next;

		if (FD_ISSET(t->u.fd, &readfd)) {
			assert(FD_ISSET(t->u.fd, &m->readfd));
			FD_CLR(t->u.fd, &m->readfd);
			thread_list_delete(&m->read, t);
			thread_list_add(&m->ready, t);
			t->type = THREAD_READY_FD;
		} else {
			if (timer_cmp(time_now, t->sands) >= 0) {
				FD_CLR(t->u.fd, &m->readfd);
				thread_list_delete(&m->read, t);
				thread_list_add(&m->ready, t);
				t->type = THREAD_READ_TIMEOUT;
			}
		}
	}

	/* Write thead. */
	thread = m->write.head;
	while (thread) {
		thread_t *t;

		t = thread;
		thread = t->next;

		if (FD_ISSET(t->u.fd, &writefd)) {
			assert(FD_ISSET(t->u.fd, &writefd));
			FD_CLR(t->u.fd, &m->writefd);
			thread_list_delete(&m->write, t);
			thread_list_add(&m->ready, t);
			t->type = THREAD_READY_FD;
		} else {
			if (timer_cmp(time_now, t->sands) >= 0) {
				FD_CLR(t->u.fd, &m->writefd);
				thread_list_delete(&m->write, t);
				thread_list_add(&m->ready, t);
				t->type = THREAD_WRITE_TIMEOUT;
			}
		}
	}
	/* Exception thead. */
	/*... */

#endif	/* select */

	/* Timer update. */
	thread = m->timer.head;
	while (thread) {
		thread_t *t;

		t = thread;
		thread = t->next;

		if (timer_cmp(time_now, t->sands) >= 0) {
			thread_list_delete(&m->timer, t);
			thread_list_add(&m->ready, t);
			t->type = THREAD_READY;
		} else {
			break;
		}
	}

	/* Return one event. */
	thread = thread_trim_head(&m->ready);

	/* There is no ready thread. */
	if (!thread)
		goto retry;

	*fetch = *thread;
	thread->type = THREAD_UNUSED;
	thread_add_unuse(m, thread);

	return fetch;
}

/* Synchronous signal handler to reap child processes */
void
thread_child_handler(void * v, int sig)
{
	thread_master_t * m = v;

	/*
	 * This is O(n^2), but there will only be a few entries on
	 * this list.
	 */
	thread_t *thread;
	pid_t pid;
	int status = 77;
	while ((pid = waitpid(-1, &status, WNOHANG))) {
		if (pid == -1) {
			if (errno == ECHILD)
				return;
			DBG("waitpid error: %s", strerror(errno));
			assert(0);
		} else {
			thread = m->child.head;
			while (thread) {
				thread_t *t;
				t = thread;
				thread = t->next;
				if (pid == t->u.c.pid) {
					thread_list_delete(&m->child, t);
					thread_list_add(&m->ready, t);
					t->u.c.status = status;
					t->type = THREAD_READY;
					break;
				}
			}
		}
	}
}


/* Make unique thread id for non pthread version of thread manager. */
unsigned long int
thread_get_id(void)
{
	static unsigned long int counter = 0;
	return ++counter;
}

/* Call thread ! */
void
thread_call(thread_t * thread)
{
	thread->id = thread_get_id();
	(*thread->func) (thread);
}

/* Our infinite scheduling loop */
void
launch_scheduler(void)
{
	thread_t thread;

	signal_set(SIGCHLD, thread_child_handler, master);

	/*
	 * Processing the master thread queues,
	 * return and execute one ready thread.
	 */
	while (thread_fetch(master, &thread)) {
		/* Run until error, used for debuging only */
#ifdef _DEBUG_
		if ((debug & 520) == 520) {
			debug &= ~520;
			thread_add_terminate_event(master);
		}
#endif
		thread_call(&thread);
	}
}
