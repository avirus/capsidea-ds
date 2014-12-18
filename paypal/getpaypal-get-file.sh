#!/bin/sh

mkdir /tmp/paypal
export SSHPASS=$2
cd /tmp/paypal
sshpass -e sftp -oBatchMode=no  -o UserKnownHostsFile=/dev/null -o StrictHostKeyChecking=no -b - $1 << !
get $3
bye
!
#gzip -d $3
#grep \"CH\", 8 >tmp
#grep \"SB\", $3 >>tmp
#rm $3
#mv tmp $3
