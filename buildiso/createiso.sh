#!/bin/bash

echo "$0 <isoname>"

cd kickstart_build_6.6 

discinfo=`/usr/bin/head -1 .discinfo`
echo $discinfo

#6.4
#createrepo -u "media://1362445555.957609" -g comps.xml  .

createrepo -u "media://$discinfo" -g comps.xml  .

cd ../ 

mkisofs -r -N -L -d -J -T -b isolinux/isolinux.bin -c isolinux/boot.cat -no-emul-boot -V $1 -boot-load-size 4 -boot-info-table -o $1.iso kickstart_build_6.6/

#implantisomd5 $1.iso 
