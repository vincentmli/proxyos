Name: ipvsadm
Summary: Utility to administer the Linux Virtual Server
Version: 1.27
Release: 4%{?dist}
License: GPLv2+
URL: http://www.linuxvirtualserver.org/software/ipvs.html
Group: Applications/System

#Source0: http://www.linuxvirtualserver.org/software/kernel-2.6/ipvsadm-%{version}.tar.gz
Source0: ipvsadm-%{version}.tar.gz
Source1: ipvsadm.init
Source2: ipvsadm-config

Requires(post): /sbin/chkconfig
Requires(preun): /sbin/chkconfig

BuildRoot: %{_tmppath}/%{name}-%{version}-%{release}-root

Buildrequires: libnl-devel
Buildrequires: popt-devel

%description
ipvsadm is a utility to administer the IP Virtual Server services
offered by the Linux kernel.

%prep
%setup -q

%build
# Don't use _smp_mflags as it makes the build fail (1.2.4)
CFLAGS="%{optflags}" make

%install
rm -rf %{buildroot}
mkdir -p %{buildroot}/etc/rc.d/init.d
make install BUILD_ROOT=%{buildroot} MANDIR=%{_mandir}
# Overwrite the provided init script with our own (mostly) LSB compliant one
install -p -m 0755 %{SOURCE1} %{buildroot}/etc/rc.d/init.d/ipvsadm
install -d -m 755 %{buildroot}/etc/sysconfig
install -p -m 0600 %{SOURCE2} %{buildroot}/etc/sysconfig/ipvsadm-config

%clean
rm -rf %{buildroot}

%post
/sbin/chkconfig --add ipvsadm

%preun
if [ $1 -eq 0 ]; then
    /sbin/chkconfig --del ipvsadm
fi

%files
%defattr(-,root,root)
%doc README
/etc/rc.d/init.d/ipvsadm
%config(noreplace) %attr(0600,root,root) /etc/sysconfig/ipvsadm-config
/sbin/ipvsadm
/sbin/ipvsadm-restore
/sbin/ipvsadm-save
%{_mandir}/man8/ipvsadm.8*
%{_mandir}/man8/ipvsadm-restore.8*
%{_mandir}/man8/ipvsadm-save.8*

%changelog
* Fri May 23 2014 Ryan O'Hara <rohara@redhat.com> 1.26-4
- Fix list_daemon to show backup daemon (#1099687)

* Fri May 23 2014 Ryan O'Hara <rohara@redhat.com> 1.26-3
- Fix svc->pe_name-conditional (#1026518)

* Fri Jul 26 2013 Ryan O'Hara <rohara@redhat.com> 1.26-2
- Include upstream patches with rebase to version 1.26 (#986189)

* Fri Jul 26 2013 Ryan O'Hara <rohara@redhat.com> 1.26-1
- Rebase to upstream version 1.26 (#986189)

* Fri Feb 17 2012 Ryan O'Hara <rohara@redhat.com> 1.25-10
- Fix list_daemon to not assume sync daemon status is ordered (#788529)

* Wed Jul 07 2010 Jan Friesse <jfriesse@redhat.com> 1.25-9
- Add man page for One-Packet Scheduler (#573112).

* Fri Jul 02 2010 Jan Friesse <jfriesse@redhat.com> 1.25-8
- Add suport for One-Packet Scheduler (#573112).

* Tue May 18 2010 Jan Friesse <jfriesse@redhat.com> 1.25-7
- Update init script to be more iptables like (#593279).

* Mon May 03 2010 Jan Friesse <jfriesse@redhat.com> - 1.25-6
- Don't report persistentconns as activeconns (#587302).

* Wed Jan 13 2010 Marek Grac <mgrac@redhat.com> - 1.25-5
- RHEL 6.0 placeholder bug (#543948)

* Fri Jul 24 2009 Fedora Release Engineering <rel-eng@lists.fedoraproject.org> - 1.25-4
- Rebuilt for https://fedoraproject.org/wiki/Fedora_12_Mass_Rebuild

* Wed Feb 25 2009 Fedora Release Engineering <rel-eng@lists.fedoraproject.org> - 1.25-3
- Rebuilt for https://fedoraproject.org/wiki/Fedora_11_Mass_Rebuild

* Wed Dec 24 2008 Matthias Saou <http://freshrpms.net/> 1.25-2
- Fork the included init script to be (mostly) LSB compliant (#246955).

* Mon Dec 22 2008 Matthias Saou <http://freshrpms.net/> 1.25-1
- Prepare update to 1.25 for when devel will update to kernel 2.6.28.
- Build require libnl-devel and popt-devel (+ patch to fix popt detection).

* Tue Feb 19 2008 Fedora Release Engineering <rel-eng@fedoraproject.org>
- Autorebuild for GCC 4.3

* Mon Oct 22 2007 Matthias Saou <http://freshrpms.net/> 1.24-10
- Update to latest upstream sources. Same filename, but updated content!
- Update kernhdr patch for it to still apply, update ip_vs.h from 1.2.0 to
  1.2.1 from kernel 2.6.23.1.

* Fri Aug 24 2007 Matthias Saou <http://freshrpms.net/> 1.24-9
- Spec file cleanup.
- Update License field.
- Don't "chkconfig --del" upon update.
- Add missing kernel-headers build requirement.
- Update URL and Source locations.
- Remove outdated piranha obsoletes, it has never been part of any Fedora.
- No longer mark init script as config.
- Include Makefile patch to prevent stripping and install init script.
- The init script could use a rewrite... leave that one for later.

* Wed Jul 12 2006 Jesse Keating <jkeating@redhat.com> - 1.24-8.1
- rebuild

* Mon May 15 2006 Phil Knirsch <pknirsch@redhat.com> - 1.24-8
- Added missing prereq to chkconfig

* Fri Feb 10 2006 Jesse Keating <jkeating@redhat.com> - 1.24-7.2.1
- bump again for double-long bug on ppc(64)

* Tue Feb 07 2006 Jesse Keating <jkeating@redhat.com> - 1.24-7.2
- rebuilt for new gcc4.1 snapshot and glibc changes

* Fri Dec 09 2005 Jesse Keating <jkeating@redhat.com>
- rebuilt

* Mon Mar 14 2005 Lon Hohberger <lhh@redhat.com> 1.24-7
- rebuilt

* Tue Jun 15 2004 Elliot Lee <sopwith@redhat.com>
- rebuilt

* Tue Mar 16 2004 Mike McLean <mikem@redhat.com> 1.24-4.2.ipvs120
- bump release

* Tue Mar 02 2004 Mike McLean <mikem@redhat.com> 1.24-4.1.ipvs120
- update to new version for 2.6 kernel

* Thu Jan 08 2004 Mike McLean <mikem@redhat.com> 1.21-10.ipvs108
- fixing a minor bug/typo in output format processing

* Wed Aug 06 2003 Mike McLean <mikem@redhat.com> 1.21-9.ipvs108
- Dropping kernel-source BuildRequires and including a local copy of 
  net/ip_vs.h to compensate.
- Incorporating some upstream changes, most notably the --sort option.

* Fri Jun 13 2003 Mike McLean <mikem@redhat.com> 1.21-8
- dropping ppc from excluded arches

* Thu Apr 4 2003 Mike McLean <mikem@redhat.com> 1.21-7
- changing %%ExcludeArch

* Thu Apr 4 2003 Mike McLean <mikem@redhat.com> 1.21-6
- added BuildRequires: kernel-source
- escaped all %% characters in %%changelog

* Mon Dec 2 2002 Mike McLean <mikem@redhat.com> 1.21-5
- Improved the description in the ipvsadm initscript.
- fixed Buildroot to use _tmppath

* Wed Aug 21 2002 Philip Copeland <bryce@redhat.com> 1.21-4
- Argh,.. %%docdir was defined which overrode what I'd
  intended to happen

* Tue Aug 1 2002 Philip Copeland <bryce@redhat.com>
- Ah... the manuals were being pushed into /usr/man
  instead of /usr/share/man. Fixed.

* Tue Jul 16 2002 Philip Copeland <bryce@redhat.com>
- Minor Makefile tweak so that we do a minimal hunt for to find
  the ip_vs.h file location

* Thu Dec 16 2001 Wensong Zhang <wensong@linuxvirtualserver.org>
- Changed to install ipvsadm man pages according to the %%{_mandir}

* Thu Dec 30 2000 Wensong Zhang <wensong@linuxvirtualserver.org>
- update the %%file section

* Thu Dec 17 2000 Wensong Zhang <wensong@linuxvirtualserver.org>
- Added a if-condition to keep both new or old rpm utility building
  the package happily.

* Tue Dec 12 2000 P.opeland <bryce@redhat.com>
- Small modifications to make the compiler happy in RH7 and the Alpha
- Fixed the documentation file that got missed off in building
  the rpm
- Made a number of -pedantic mods though popt will not compile with
  -pedantic

* Wed Aug 9 2000 Horms <horms@vergenet.net>
- Removed Obseletes tag as ipvsadm is back in /sbin where it belongs 
  as it is more or less analogous to both route and ipchains both of
  which reside in /sbin.
- Create directory to install init script into. Init scripts won't install
  into build directory unless this is done

* Thu Jul  6 2000 Wensong Zhang <wensong@linuxvirtualserver.org>
- Changed to build rpms on the ipvsadm tar ball directly

* Wed Jun 21 2000 P.Copeland <copeland@redhat.com>
- fixed silly install permission settings

* Mon Jun 19 2000 P.Copeland <copeland@redhat.com>
- Added 'dist' and 'rpms' to the Makefile
- Added Obsoletes tag since there were early versions
  of ipvsadm-*.rpm that installed in /sbin
- Obsolete tag was a bit vicious re: piranha

* Mon Apr 10 2000 Horms <horms@vergenet.net>
- created for version 1.9

