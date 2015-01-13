%bcond_without snmp
%bcond_without vrrp
%bcond_with profile
%bcond_with debug

%define kerneldir /home/centos_rebuild/lvs-tool/kernel 

Name: keepalived
Summary: High Availability monitor built upon LVS, VRRP and service pollers
Version: 1.2.13
Release: 3%{?dist}
License: GPLv2+
URL: http://www.keepalived.org/
Group: System Environment/Daemons

#Source0: http://www.keepalived.org/software/keepalived-%{version}.tar.gz
Source0: keepalived-%{version}.tar.gz
Source1: keepalived.init

Requires(post): /sbin/chkconfig
Requires(preun): /sbin/chkconfig
Requires(preun): /sbin/service
Requires(postun): /sbin/service

BuildRoot: %{_tmppath}/%{name}-%{version}-%{release}-root-%(%{__id_u} -n)
%if %{with snmp}
BuildRequires: net-snmp-devel
%endif
BuildRequires: openssl-devel
BuildRequires: libnl-devel
BuildRequires: popt-devel

%description
Keepalived provides simple and robust facilities for load balancing
and high availability to Linux system and Linux based infrastructures.
The load balancing framework relies on well-known and widely used
Linux Virtual Server (IPVS) kernel module providing Layer4 load
balancing. Keepalived implements a set of checkers to dynamically and
adaptively maintain and	manage load-balanced server pool according
their health. High availability is achieved by VRRP protocol. VRRP is
a fundamental brick for router failover. In addition, keepalived
implements a set of hooks to the VRRP finite state machine providing
low-level and high-speed protocol interactions. Keepalived frameworks
can be used independently or all together to provide resilient
infrastructures.

%prep
%setup -q

%build
./configure \
	--with-kernel-dir=%{kerneldir}
#%configure \
#    %{?with_debug:--enable-debug} \
#    %{?with_profile:--enable-profile} \
#    %{!?with_vrrp:--disable-vrrp} \
#    %{?with_snmp:--enable-snmp}
%{__make} %{?_smp_mflags} STRIP=/bin/true

%install
rm -rf %{buildroot}
make install DESTDIR=%{buildroot}
rm -rf %{buildroot}%{_sysconfdir}/keepalived/samples/
%{__install} -p -m 0755 %{SOURCE1} %{buildroot}%{_initrddir}/%{name}

%if %{with snmp}
mkdir -p %{buildroot}%{_datadir}/snmp/mibs/
%{__install} -p -m 0644 doc/KEEPALIVED-MIB %{buildroot}%{_datadir}/snmp/mibs/KEEPALIVED-MIB.txt
%endif

%clean
rm -rf %{buildroot}

%post
/sbin/chkconfig --add keepalived

%preun
if [ "$1" -eq 0 ]; then
    /sbin/service keepalived stop >/dev/null 2>&1
    /sbin/chkconfig --del keepalived
fi

%postun
if [ "$1" -eq 1 ]; then
    /sbin/service keepalived condrestart >/dev/null 2>&1 || :
fi

%files
%defattr(-,root,root,-)
%attr(0755,root,root) %{_sbindir}/keepalived
%attr(0644,root,root) %{_sysconfdir}/sysconfig/keepalived
%attr(0644,root,root) %{_sysconfdir}/keepalived/keepalived.conf
%doc AUTHOR ChangeLog CONTRIBUTORS COPYING README TODO
%doc doc/keepalived.conf.SYNOPSIS doc/samples/keepalived.conf.*
%dir %{_sysconfdir}/keepalived/
%config(noreplace) %{_sysconfdir}/keepalived/keepalived.conf
%config(noreplace) %{_sysconfdir}/sysconfig/keepalived
%{_sysconfdir}/rc.d/init.d/keepalived
%if %{with snmp}
%{_datadir}/snmp/mibs/KEEPALIVED-MIB.txt
%endif
%{_bindir}/genhash
%{_sbindir}/keepalived
%{_mandir}/man1/genhash.1*
%{_mandir}/man5/keepalived.conf.5*
%{_mandir}/man8/keepalived.8*

%changelog
* Wed Sep 26 2012 Ryan O'Hara <rohara@redhat.com> - 1.2.7-3
- Don't strip binaries at build time.
  Resolves: rhbz#846064

* Fri Sep 21 2012 Ryan O'Hara <rohara@redhat.com> - 1.2.7-2
- Bump release number.
  Resolves: rhbz#846064

* Thu Sep 20 2012 Ryan O'Hara <rohara@redhat.com> - 1.2.7-1
- Initial build.
  Resolves: rhbz#846064
