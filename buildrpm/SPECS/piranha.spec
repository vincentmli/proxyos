Summary: Cluster administation tools  
Name: piranha
Version: 0.8.6
Release: 4%{?dist}.2
URL: http://git.fedoraproject.org/git/piranha.git
License: GPLv2+
Group: System Environment/Base 

Source0: piranha-%{version}.tar.gz

Buildroot: %{_tmppath}/%{name}-%{version}-%{release}-root-%(%{__id_u} -n)


#Requires: ipvsadm >= 1.14, httpd, php, logrotate

Requires(post): chkconfig
Requires(preun): chkconfig initscripts
Requires(postun): initscripts

%description 
Various tools to administer and configure the Linux Virtual Server as well as
heartbeating and failover components.  The LVS is a dynamically adjusted
kernel routing mechanism that provides load balancing primarily for web
and ftp servers though other services are supported.


%prep
%setup -q -n piranha

%build

%install
rm -rf $RPM_BUILD_ROOT
make DESTDIR=$RPM_BUILD_ROOT LIBDIR=%{_libdir} install

%clean
rm -rf $RPM_BUILD_ROOT


%pre
/usr/sbin/groupadd -g 60 -r -f piranha >/dev/null 2>&1 ||:
/usr/sbin/useradd -u 60 -g 60 -d /etc/sysconfig/ha -r   \
        -s /sbin/nologin piranha >/dev/null 2>&1 ||:

%preun
if [ $1 = 0 ]; then
        /sbin/service piranha-gui stop >/dev/null 2>&1
        /sbin/chkconfig --del piranha-gui
fi

%post 
/sbin/chkconfig --add piranha-gui
#chown root.piranha %{_sysconfdir}/sysconfig/ha/lvs.cf
#chown root.piranha %{_sysconfdir}/sysconfig/ha/nginx.conf

%postun
if [ $1 = 1 ] ; then
        /sbin/service piranha-gui condrestart >/dev/null 2>&1
fi


%files
%defattr(-,root,root)
%doc sample.cf AUTHORS COPYING Changelog README docs
%{_sbindir}/*
%{_initddir}/*

%dir %{_sysconfdir}/sysconfig/ha
%config(noreplace) %{_sysconfdir}/sysconfig/ha/conf
%config(noreplace) %{_sysconfdir}/sysconfig/ha/logs
%config(noreplace) %{_sysconfdir}/sysconfig/ha/modules
%config(noreplace) %{_sysconfdir}/sysconfig/ha/web

%attr(0660,root,piranha)
%config(noreplace) %{_sysconfdir}/sysconfig/ha/lvs.cf
%config(noreplace) %{_sysconfdir}/sysconfig/ha/nginx.conf
%config(noreplace) %{_sysconfdir}/sysconfig/ha/iptables

%attr(0660,root,piranha) %{_sysconfdir}/sysconfig/ha/lvs.cf 
%attr(0660,root,piranha) %{_sysconfdir}/sysconfig/ha/nginx.conf 
%attr(0660,root,piranha) %{_sysconfdir}/sysconfig/ha/iptables

%config(noreplace) %{_sysconfdir}/logrotate.d/piranha

%dir %{_localstatedir}/log/piranha
%attr(0755,piranha,root) %{_localstatedir}/log/piranha


%changelog
* Thu Feb 06 2014 Ryan O'Hara <rohara@redhat.com> 0.8.6-4.2
- Resolves: #1061905 - require authentication for all HTTP methods (CVE-2013-6492)

* Thu Jan 23 2014 Ryan O'Hara <rohara@redhat.com> 0.8.6-4.1
- Resolves: #1055709 - ignore EINTR from sem_timedwait

* Mon Jul 15 2013 Ryan O'Hara <rohara@redhat.com> 0.8.6-4
- Resolves: #980169 - add POSIX semaphore for pulse/lvsd synchronization

* Tue Feb 19 2013 Ryan O'Hara <rohara@redhat.com> 0.8.6-3
- Resolves: #903711 - fix sorry server

* Fri Sep 28 2012 Ryan O'Hara <rohara@redhat.com> 0.8.6-2
- Resolves: #860924 - fix virtual server interface in piranha-gui

* Mon Sep 17 2012 Ryan O'Hara <rohara@redhat.com> 0.8.6-1
- Resolves: #846035 - rebase to upstream version 0.8.6
- Resolves: #857917 - piranha gui does not set ipvs timeout values

* Tue May 01 2012 Ryan O'Hara <rohara@redhat.com> 0.8.5-19
- Resolves: #813906 - add SIGCHLD handler to pulse for lvs mode

* Wed Apr 25 2012 Ryan O'Hara <rohara@redhat.com> 0.8.5-18
- Resolves: #815887 - call openlog whenever LVS_FLAG_SYSLOG is set

* Tue Apr 24 2012 Ryan O'Hara <rohara@redhat.com> 0.8.5-17
- Resolves: #815887 - nanny doesn't write to syslog when --nodaemon is used

* Mon Apr 23 2012 Ryan O'Hara <rohara@redhat.com> 0.8.5-16
- Resolves: #745271 - remove changes to debugfiles.list and debuglinks.list

* Tue Feb 28 2012 Ryan O'Hara <rohara@redhat.com> 0.8.5-15
- Resolves: #798362 - echo newline after init script reload command

* Tue Feb 28 2012 Ryan O'Hara <rohara@redhat.com> 0.8.5-14
- Resolves: #745271 - add options for ipvs timeouts

* Fri Feb 17 2012 Ryan O'Hara <rohara@redhat.com> 0.8.5-13
- Resolves: #788541 - add option for sync daemon mcast interface
- Resolves: #717556 - add option for sync daemon id

* Wed Feb 15 2012 Ryan O'Hara <rohara@redhat.com> 0.8.5-12
- Resolves: #785720 - reread config can cause lvsd to segfault

* Tue Feb 14 2012 Ryan O'Hara <rohara@redhat.com> 0.8.5-11
- Resolves: #747300 - fix file descriptor leak in pulse

* Mon Feb 13 2012 Ryan O'Hara <rohara@redhat.com> 0.8.5-10
- Resolves: #749594 - fix ipvsadm --stop-daemon syntax

* Thu Aug 11 2011 Ryan O'Hara <rohara@redhat.com> 0.8.5-9
- Resolves: #628872 - init script searches cwd which can cause SELinux denials
- Resolves: #706881 - killing nanny/lvsd process should cause pulse to exit
- Resolves: #708036 - too many VIPs can causes HTTP 414 error
- Resolves: #729828 - add CIDR /23 netmask to drop-down menus

* Fri Jul 08 2011 Marek Grac <mgrac@redhat.com> 0.8.5-8
- "service pulse reload" is resulting in lvsd segfaulting
  Resolves: rhbz#703146
- a failure to start a single nanny kills off *all* running nannys
  Resolves: rhbz#593728

* Wed Jul 14 2010 Marek Grac <mgrac@redhat.com> 0.8.5-7
- Resolves: #613920 - init status and stop actions do not work

* Fri Jun 18 2010 Marek Grac <mgrac@redhat.com> 0.8.5-6
- Resolves: #604741 - piranha does not work with SELinux
- Resolves: #593730 - LVS rebooted simultaneously cause problems

* Wed Feb 17 2010 Marek Grac <mgrac@redhat.com> 0.8.5-4
- Resolves: #566141 - Sorry server not functional

* Tue Feb 16 2010 Marek Grac <mgrac@redhat.com> 0.8.5-3
- Resolves: #565813 - problem with rport 

* Fri Jan 22 2010 Marek Grac <mgrac@redhat.com> 0.8.5-2
- piranha fails to parse load average with ruptime (upstream patch)

* Thu Jan 14 2010 Marek Grac <mgrac@redhat.com> 0.8.5-1
- New upstream version 0.8.5

* Fri Dec 22 2009 Marek Grac <mgrac@redhat.com> 0.8.4-15
- Resolves: #522230 - 'service pulse stop' does not kill nanny process
- Resolves: #533113 - piranha-0.8.4-13.el5 breaks LVS 

* Mon Jun 15 2009 Marek Grac <mgrac@redhat.com> 0.8.4-13
- Resolves: #500909 - problem when attempting to load balance using port in lvs.cf

* Mon May 18 2009 Marek Grac <mgrac@redhat.com> 0.8.4-12
- Resolves: #500909 - pulse errors parsing port in lvs.cf 'unknown command'

* Mon May 04 2009 Marek Grac <mgrac@redhat.com>
- Resolves: #495005 -  nanny does not default to webservice query string 

* Mon Apr 27 2009 Marek Grac <mgrac@redhat.com>
- Resolves: #495102  - piranha removes slashes from monitoring script send commands 

* Wed Feb 04 2009 Marek Grac <mgrac@redhat.com>
- Resolves: #483602 -  logrotate default configuration, use of wildcard ' * '

* Tue Sep 20 2008 Lon Hohberger <lhh@redhat.com> 0.8.4-11
- Resolves: #238498  - need fallback service
- Resolves: #243908  - nanny blocks signals in child processes

* Thu Aug 28 2008 Marek Grac <mgrac@redhat.com> 0.8.4-10
- Resolves: #457567  - pulse not support monitor link on bond
                       interface

* Wed Jul 09 2008 Marek Grac <mgrac@redhata.com>
- Resolves: #446802 - segfault if syslog message is longer 
                      than 80 characters

* Thu Jun 05 2008 Marek Grac <mgrac@redhat.com>
- Resolves: #439814 - minor defect in nanny

* Wed Apr 02 2008 Marek Grac <mgrac@redhat.com>
- Resolves: #391131 - pulse cannot bind to port 539 after a restart

* Thu Jan 31 2008 Marek Grac <mgrac@redhat.com>
- Resolves: #250888 - add support for startup options

* Tue Jan 28 2008 Marek Grac <mgrac@redhat.com>
- Resolves: #429864 - support for remote port in piranha-gui

* Wed Jan 23 2008 Marek Grac <mgrac@redhat.com> 0.8.4-9.3
- Resolves: #243278 - Fixes wrong init script

* Mon Jan 21 2008 Lon Hohberger <lhh@redhat.com> 0.8.4-9.2
- Resolves: #202465 - Pulse does not properly parse lvs.cf

* Thu Oct 18 2007 Marek Grac <mgrac@redhat.com> 0.8.4-8
- Resolves: #243278 - Fixes wrong init scripts
- Resolves: #249312 - VIP doesn't go down
- Resolves: #245788 - send_arp & incorrectly-formed ARP
- Resolves: #338101 - php errors in piranha-gui

* Mon Aug 23 2006 Stanko Kupcevic <kupcevic@redhat.com> 0.8.4-7
- Version bump

* Mon Aug 21 2006 Stanko Kupcevic <kupcevic@redhat.com> 0.8.4-6
- Changed group ownership of /etc/sysconfig/ha to root 
   (except /etc/sysconfig/ha/lvs.cf)

* Mon Aug 21 2006 Stanko Kupcevic <kupcevic@redhat.com> 0.8.4-5
- Add condrestart to pulse initscript
- Add status and condrestart to piranha-gui initscript

* Wed Aug 16 2006 Stanko Kupcevic <kupcevic@redhat.com> 0.8.4-4
- Removed /etc/sysconfig/ha/example_script, as equal example is
   already in /usr/share/doc/piranha/docs/script_templates/* 
- Update apache's modules, use libphp5 (instead of libphp4)

* Wed Aug 16 2006 Stanko Kupcevic <kupcevic@redhat.com> 0.8.4-3
- Removed .htaccess file, as equal configuration 
   already in /etc/sysconfig/ha/conf/httpd.conf
- Added logrotate conf file

* Wed Aug 16 2006 Stanko Kupcevic <kupcevic@redhat.com> 0.8.4-2
- Version Bump

* Mon Jun 12 2006 Stanko Kupcevic <kupcevic@redhat.com> 0.8.4-1
- Fixed bz191787 - monitor script hangs

* Mon May 15 2006 Stanko Kupcevic <kupcevic@redhat.com> 0.8.3-1
- Document send_program in lvs.cf manpage (bz190022)
- Fix lvsd where it segfaults when nanny dies (bz176913)

* Wed Jan 11 2006 Lon Hohberger <lhh@redhat.com> 0.8.2-1
- Fix DOS-mangled config file problem
- Fix memory leak in nanny.c 

* Thu Jul 28 2005 Lon Hohberger <lhh@redhat.com> 0.8.1-1
- Add internal NIC link monitoring option (does not work
with bonding driver)

* Tue Feb 07 2005 Lon Hohberger <lhh@redhat.com> 0.8.0-1
- Build for RHCS-4
- Lots of php warning cleanups
- rename lvs to lvsd so as not to conflict with 'lvs' command

* Thu Nov 11 2004 Lon Hohberger <lhh@redhat.com> 0.7.10-1
- Fix segfault on empty lvs.cf on x86_64

* Mon Nov 08 2004 Lon Hohberger <lhh@redhat.com> 0.7.9-1
- Force sending of last heartbeat before shutdown of backup
so that the master re-arps the VIPs (#134007)

* Wed Oct 27 2004 Lon Hohberger <lhh@redhat.com> 0.7.8-1
- Add active/inactive commands, patch from Sébastien Bonnet.

* Thu Sep 02 2004 Lon Hohberger <lhh@redhat.com> 0.7.7-1
- Fix misc. bugzillas

* Thu Aug 28 2003 Mike McLean <mikem@redhat.com>
- Changed shell for user piranha to /sbin/nologin
- fixed %%post script

* Thu Jul 31 2003 Mike McLean <mikem@redhat.com>
- Passing LIBDIR to 'make install'.

* Wed Mar 12 2003 Mike McLean <mikem@redhat.com>
- Escaped all %% characters in %%changelog.

* Wed Oct 16 2002 Mike McLean <mikem@redhat.com>
- Added /etc/sysconfig/ha/logs/README to file list

* Fri Apr 26 2002 Philip Copeland <bryce@redhat.com>
- Final checkover and commit for AS
- bumped release number and added in the man pages

* Thu Feb 28 2002 Philip Copeland <bryce@redhat.com>
- Fixed up piranha-passwd (#60440)
- Updated the LVS-HOWTO docs (#60204)
- fixed the silly misname of a directory in the httpd.conf (#60529)

* Thu Jan 31 2002 Philip Copeland <bryce@redhat.com>
- pulled in Wensong's memory leak patchs
- Dropped the shared scsi portion in the web interface
  Note: FOS will vanish in piranha 0.7.0's interface, in
  theory that functionality will be taken over by clumanager
  which is part of the advanced server offering.
  In the future clumanager/piranha will merge (so I'm told)

* Fri Jan 18 2002 Philip Copeland <bryce@redhat.com>
- Add in missing lvs.c code that was in fos.c (re pzb's patch)

* Fri Jan 18 2002 Philip Copeland <bryce@redhat.com>
- send_arp,c, changed the
  sock=socket(AF_INET,SOCK_RAW,htons(ETH_P_RARP));
  back to
  sock=socket(AF_INET,SOCK_PACKET,htons(ETH_P_RARP));
  as I got reports that this wasn't working.
  It will work fine, just there will be noise that it's
  an obsolete interface

* Thu Jan 17 2002 Philip Copeland <bryce@redhat.com>
- Finally someone spotted the reason why the children don't die
  much happiness spread by Sebastien <sebastien.bonnet@experian.fr>
- Added in additional patch from Sebastien for bugzilla #57654
  which I think is also a solution for #15911

* Thu Dec 13 2001 Philip Copeland <bryce@redhat.com>
- Added in a patch I got from pzb for to be a good deal more sensible
  about how we detect if we're using FOS mode or LVS mode other than looking
  for a start or stop script (shudder)

* Thu Nov 29 2001 Philip Copeland <bryce@redhat.com>
- Merged in Wensongs quiesce server patch for nanny
- Updated the web interface to also know about the quiesce option

* Mon Nov 26 2001 Philip Copeland <bryce@redhat.com>
- removal of unnecessary usleep() call in nanny.c

* Wed Nov 21 2001 Philip Copeland <bryce@redhat.com>
- Merged in a patch for nanny.c from Wensong <wensong@gnuchina.org>
  which cleans up the run() function by splitting out two new
  funcions, adjustWeight(), and checkState().
- I thought I'd removed the portion in the Makefile that tweaks with
  /etc/httpd/conf/httpd.conf but it was still there when I looked.
  Removed it.

* Thu Nov 20 2001 Philip Copeland <bryce@redhat.com>
- control.php3 has for ages been calling /usr/sbin/ipvsadm.
  Unfortunately that changed to /sbin and was not updated
  https://bugzilla.redhat.com/bugzilla/show_bug.cgi?id=56497
- In the pulse.c code, too few elements were allocated
  for the arguments causeing an occasional segv. 
  
* Thu Oct 11 2001 Philip Copeland <bryce@redhat.com>
- Forgot to bump the release number for the build system

* Thu Oct 11 2001 Philip Copeland <bryce@redhat.com>
- Added in a switchover for fos (virt/real address swapover
  to prevent the fos call to nanny checking the real IP
  instead of the VIP

* Thu Oct 11 2001 Tim Waugh <twaugh@redhat.com>
- Fix segfault when not using multipath heartbeat (bug #54506).

* Mon Oct 8 2001 Philip Copeland <bryce@redhat.com>
- Bundled in some very simple template scripts with the
  documentation. Dropped in an example_script with the
  main dist. Bumped the version number (0.6.0-10)

* Mon Oct 2 2001 Philip Copeland <bryce@redhat.com>
- Minor changelog entries and bumped version number

* Sun Sep 30 2001 Philip Copeland <bryce@redhat.com>
- Removed nasty httpd.conf modifications in the
  rpm spec file. (An artifact of the old install
  system)
- Dropped in an example script for use with the
  send_program function. Probably need to spend a
  week cooking up a library of 'common' scripts
  for use.

* Fri Sep 28 2001 Philip Copeland <bryce@redhat.com>
- dropped in the changes required for usage of
  send_program in the FOS enviroment. (untested)

* Fri Sep 28 2001 Philip Copeland <bryce@redhat.com>
- Added in the send_program feature to LVS
  Should be forward portable to FOS quite easily

* Sat Sep 22 2001 Philip Copeland <bryce@redhat.com>
- Fixed parse.php3 so that it wouldn't issue the persistance option
  twice in the lvs.cf configuration file

* Tue Sep 7 2001 Philip Copeland <bryce@redhat.com>
- made minor cast for fwmark change

* Tue Aug 28 2001 Philip Copeland <bryce@redhat.com>
- Reformatted the entire src tree into GNU standard formatting

* Mon Aug 27 2001 Philip Copeland <bryce@redhat.com>
- Merged in Wensongs fwmark changes
- Moved piranha-passwd to /usr/sbin per requests

* Fri Aug 10 2001 Philip Copeland <bryce@redhat.com>
- Various tidyups/rpm fiddling in an effort to allow for building standalone
  outside of the buildsystem.

* Thu Aug 2  2001 Philip Copeland <bryce@redhat.com>
- spec file keeps missing off the modules -> /usr/lib/apache link
  added explicit rule in %%files

* Mon Jul 30 2001 Philip Copeland <bryce@redhat.com>
- Humm, killed off the piranha.desktop file as the GUI is web based not X11
  and this makes no sense to try and start from a menu item. A bookmark maybe.

* Tue Jul 24 2001 Philip Copeland <bryce@redhat.com>
- Directives from der Boss, we must be able to run rpm -V successfully
- Collapsed piranha piranha-gui piranha-docs into a single rpm  

* Thu Jun 14 2001 Philip Copeland <bryce@redhat.com>
- Made fast changes to the hard coded /usr/sbin/ipvsadm
  to be /sbin/ipvsadm. This should be in a define in a header file
  however because of time pressure I'm doing it this way first and
  fix it up next week.

* Thu May 31 2001 Keith Barrett <kbarrett@redhat.com>
- Corrected netmask logic so it does not ref gethost call and is only
  used when value is non-zero.
- Added netmask error code and return string
- Fixed pulse's use of netmasks in ifconfig and sendarp calls
- Put missing "if debug" blocks around debug output
- If using FOS, assume active if no backup node specified in lvs.cf
- Correct inconsistent use of --test-start, --nofork, --nodaemon, and
  --norun between pulse, nanny, fos, and lvs. Updated man pages too.
- Removed ipvsadm build from Makefile
- piranha requires ipvsadm 1.17+
- parser now handles trailing \r chars (customer problem)

* Mon May 24 2001 Keith Barrett <kbarrett@redhat.com>
- Updated documentation and authors
- Created new documentation system and GUI links to it

* Wed May 16 2001 Keith Barrett <kbarrett@redhat.com>
- Fixed piranha-gui startup script and piranha spec file
- Fixed gui error screen to report correct file permission & ownership

* Tue May 15 2001 Keith Barrett <kbarrett@redhat.com>
- Removed ipvsadm inclusion in piranha RPM
- Changed version to exceed last experiemental release
- Corrected web reference in spec file
- Cleaned up 7.x installationin spec and makefile
- Bumped version (multiple times)

* Mon May 14 2001 Keith Barrett <kbarrett@redhat.com>
- Merged Bryce's ipvsadm changes
- Changed nobody to apache (if apache user apache exists)
- Migrate existing lvs.cf file if appropriate
- bumped version

* Wed Jun 18 2000 Philip Copeland <copeland@redhat.com>
- change the default uptime method to be rup instead of ruptime

* Wed Jun 17 2000 Philip Copeland <copeland@redhat.com>
- reintegrated Keiths changes, tweaked the Makefile,
  updated the comments etc.

* Wed Jun 14 2000 Keith Barrett <kbarrett@redhat.com>
- Backed out some patches to pulse.c, fos.c, lvs.c, and nanny.c
- Updated documentation
- Changed "take control" heartbeat

* Tue Jun 6 2000 Philip Copeland <copeland@redhat.com>
- removed the 'accept' input button in control.php3 to
  better fit the documentation
- Updated the lvserror.php3 to reflect another not so uncommon
  fault that may prevent correct running of the GUI
- Tidied up some eyecandy

* Mon May 31 2000 Philip Copeland <copeland@redhat.com>
- Fixed an incorrect pag redirection in the failover pages
- added two missing subnet masks to the peristance netmask field

* Mon May 22 2000 Philip Copeland <copeland@redhat.com>
- modified the piranha.spec.in file and Makefile to allow for
  release number updates (from editing the Makefile)
- fixed a bug in redundancy.php3 which occurs when no lvs.cf file
  is present
- made ipvsadm popt change (minor patch which shouldn't affect
  native redhat installed systems)

* Sun May 22 2000 Philip Copeland <copeland@redhat.com>
- core item ipvsadm has been updated to use ipvs-0.9.12-2.2.14
- Minor tweeking of directory scruture
- gui files no longer included in rpm builds
- added a load of patches from the bugzilla archives
- made kernel diffs
- made sure the rpms built and installed correctly

* Wed Apr 26 2000 Mike Wangsmo <wanger@redhat.com>
- changed the password updating/setting policy.  Made all password actions
  root operations via a root owned shell tool (piranha-passwd)

* Sun Apr 23 2000 Philip Copeland <copeland@redhat.com>
- Security exposure raised by ISS regarding the passwd.php3
  file fixed by replacing the external call to htpasswd with
  php3s own crypt function (see parse.php3 change_password)
- Rebuilt packages and generated errata.

* Tue Mar 07 2000 Mike Wangsmo <wanger@redhat.com>
- removed killapache
- changed sample httpd shutdown to use proper init scripts
- Keith fixed MORONIC spelling error in pulse.c :)
- moved the GUI README into the %%doc list

* Tue Mar 07 2000 Keith Barrett <kbarrett@redhat.com>
- Improved take control heartbeat login in pulse
- Enhanced old GUI README to notify user of special conditions
- Bumped version

* Mon Mar 06 2000 Keith Barrett <kbarrett@redhat.com>
- Bumped version to reflect changes to fos, pulse, and old GUI

* Sun Mar 05 2000 Mike Wangsmo <wanger@redhat.com>
- added fos to file list
- moved apache & php requires to gui package
- reordered the install of killapache to make strip not puke

* Sat Mar  5 2000 Keith Barrett <kbarrett@redhat.com>
- Corrected signaling, heartbeat, and failover in pulse
- Bumped version to reflect a stable release
- Note: pulse USR1/2 disabled in this checkin

* Thu Mar  4 2000 Keith Barrett <kbarrett@redhat.com>
- Corrected memory errors in parser
- Updated fos
- Improved (but have not finished) pulse changes
- Bumped version to reflect parser change

* Wed Mar  2 2000 Philip Copeland <copeland@redhat.com>
- Added password services to web control
- Altered pulse to provide a signal escalator between nobody root
  as we now have a "use a uid/gid < 100" limitation that
  apache's suexec doesn't cater for
- Added default password to web (null)
- minor spec file changes

* Thu Mar  2 2000 Keith Barrett <kbarrett@redhat.com>
- New version of pulse with enhanced signals
- New service = xxx parameter in config system
- New man pages and doc updates

* Wed Mar  1 2000 Keith Barrett <kbarrett@redhat.com>
- Checked in failover service changes. Not fully
- functional yet.

* Wed Mar  1 2000 Bill Nottingham <notting@redhat.com>
- use a uid/gid < 100

* Mon Feb 28 2000 Mike Wangsmo <wanger@redhat.com>
- fiddled with the /etc/lvs.cf stuff some more

* Fri Feb 25 2000 Mike Wangsmo <wanger@redhat.com>
- *NO* "echo's" in spec files!
- made /etc/lvs.cf a %%config so it'll get backed up on upgrades
- cleaned up some useradd/groupadd errors

* Fri Feb 25 2000 Keith Barrett <kbarrett@redhat.com>
- Fixed parsing of protocol = xxx
- Bumped version (again)

* Tue Feb 22 2000 Keith Barrett <kbarrett@redhat.com>
- Fixed passthrough of routing type options
- Fixed man pages to reflect routing type options
- Changed formatting of sample.cf file and included comments
- Bumped version number

* Mon Feb 21 2000 Keith Barrett <kbarrett@redhat.com>
- Fixed segfault problem in nanny
- Minor changes reporting the phase out of the old GUI
- Bumped version number
- Changed spec file to include web in GUI RPM

* Fri Feb 18 2000 Keith Barrett <kbarrett@redhat.com>
- Bumped version to reflect Bryce's changes to install scripts
- Added persistent netmask to lvsconfig and lvs and sample.cf

* Thu Feb 17 2000  Keith Barrett <kbarrett@redhat.com>
- Bumped version
- Improved send/expect stuff

* Wed Feb 16 2000  Keith Barrett <kbarrett@redhat.com>
- Bumped version to reflect GUI changes

* Tue Feb 15 2000 Keith Barrett <kbarrett@redhat.com>
- Added send/expect parsing to lvs, lvsconfig, and nanny.
- Updated nanny man page and sample.cf file

* Tue Feb 15 2000 Mike Wangsmo <wanger@redhat.com>
- added a groupadd line so useradd wouldn't fail.

* Mon Feb 14 2000 Keith Barrett <kbarrett@redhat.com>
- added generic service parameters to nanny.c and nanny.8

* Fri Feb 11 2000 Mike Wangsmo <wanger@redhat.com>
- added uid/gid piranha to setup (233)
- cleaned up %%install a bit

* Mon Feb 09 2000 Philip Copeland <copeland@redhat.com>
- Added NAT/direct routing/tunneling to the web interface
- fixed up the web documents to tell the web browsers that
  caching them would be a BAD idea
- Added some prettify stuff

* Mon Feb 07 2000 Keith Barrett <kbarrett@redhat.com>
- Changed pulse call to send_arp to /usr/sbin
- Suppress sock error on unconfigured tunl device
- Bumped version

* Sat Feb 05 2000 Mike Wangsmo <wanger@redhat.com>
- removed piranha-web package and put it into main package

* Fri Feb 04 2000 Keith Barrett <kbarrett@redhat.com>
- Added Bryce's README.beta to beta release

* Thu Feb 03 2000 Nalin Dahyabhai <nalin@redhat.com>
- check that RPM_BUILD_ROOT != / when installing
- handle case if httpd not installed during %%install
- handle gzipped man pages

* Thu Feb 03 2000 Mike Wangsmo <wanger@redhat.com>
- tagged the desktop entry as %%config

* Thu Jan 27 2000 Keith Barrett <kbarrett@redhat.com>
- Minor updates to doc files and man pages.
- Added headers and history to some source files
- Created nanny.h file

* Sat Sep 25 1999 Mike Wangsmo <wanger@redhat.com>
- added the HOWTO

* Fri Sep 24 1999 Mike Wangsmo <wanger@redhat.com>
- added KDE desktop entry
- added piranha icon pixmap
- fixed *tons* of lvs <-> gui glue problems

* Tue Sep 21 1999 Mike Wangsmo <wanger@redhat.com>
- added a send_arp tool to the package
- removed the /etc/lvs.cf entry (it was dumb here)
- strip all binaries too
- added nanny to package

* Tue Sep 21 1999 Mike Wangsmo <wanger@redhat.com>
- moved all bins to /usr/sbin
- added pulse init script

* Mon Sep 20 1999 Mike Wangsmo <wanger@redhat.com>
- moved piranha to its own package

* Thu Sep 9 1999 Mike Wangsmo <wanger@redhat.com>
- added the documents package

* Wed Sep 8 1999 Mike Wangsmo <wanger@redhat.com>
- added a few more files to the file list
- relocated some things.
- put in a stub config file

* Tue Sep 7 1999 Mike Wangsmo <wanger@redhat.com>
- restructured the package to come from CVS
- renamed it
- added oot's lvs daemon
- added the perl client_monitor tool from Lars Marowsky-Bree
- put stubs in for the GUI
- buffed up the Makefile

* Sun Jul 25 1999 Mike Wangsmo <wanger@redhat.com>
- initial build
