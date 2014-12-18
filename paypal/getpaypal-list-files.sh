#!/bin/sh

export SSHPASS=$2
sshpass -e sftp -oBatchMode=no  -o UserKnownHostsFile=/dev/null -o StrictHostKeyChecking=no -b - $1 << !
ls -1 /ppreports/outgoing
bye
!