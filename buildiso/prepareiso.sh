#!/bin/bash

#mount centos iso dvd
mount -o <path-to-DVD1-iso> DVD1_mount
mount -o <path-to-DVD2-iso> DVD2_mount

cp -R DVD1_mount/isolinux/* kickstart_build_6.6/isolinux
cp DVD1_mount/.discinfo kickstart_build_6.6/
cp -R DVD1_mount/images/* kickstart_build_6.6/images/
chmod 664 ./kickstart_build_6.6/isolinux/isolinux.bin
chmod 664 ./kickstart_build_6.6/isolinux/isolinux.cfg
chmod 664 ./kickstart_build_6.6/ks/ks.cfg
cp DVD1_mount/Packages/*.rpm kickstart_build_6.6/CentOS/
cp DVD2_mount/Packages/*.rpm kickstart_build_6.6/CentOS/
