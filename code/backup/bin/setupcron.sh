#!/bin/bash
FILE="/tmp/$(basename $0).$RANDOM.txt"; 

COMMAND="/var/backup/bin/db.sh && curl \"http://www.cronrat.com/r/7n9GvvYu\" --data-urlencode \"rat=BASEDB_backup\" --data-urlencode \"crontab=0 12,15,18 * * 1,2,3,4,5\" --data-urlencode \"toutc=-7\""; 
ENTRY="0 12,15,18 * * 1,2,3,4,5 bash $COMMAND"; 
echo "$ENTRY" >> $FILE; 

COMMAND="bash /var/backup/bin/_logmonitors/monitorlog.sh && curl \"http://www.cronrat.com/r/7n9GvvYu\" --data-urlencode \"rat=basemonitorlog\" --data-urlencode \"crontab=0 1 * * 1,2,3,4,5\" --data-urlencode \"toutc=-7\""; 
ENTRY="0 1 * * 1,2,3,4,5 $COMMAND"; 
echo "$ENTRY" >> $FILE; 

COMMAND="php /var/www/base/artisan command:qaemd --env=production && curl \"http://www.cronrat.com/r/7n9GvvYu\" --data-urlencode \"rat=qaemd\" --data-urlencode \"crontab=0 1 * * 1,2,3,4,5\" --data-urlencode \"toutc=-7\""; 
ENTRY="0 1 * * 1,2,3,4,5 $COMMAND"; 
echo "$ENTRY" >> $FILE; 

COMMAND="php /var/www/base/artisan command:moveemdbilltrac all --env=production && curl \"http://www.cronrat.com/r/7n9GvvYu\" --data-urlencode \"rat=moveemdbilltrac\" --data-urlencode \"crontab=0 1 * * 1,2,3,4,5\" --data-urlencode \"toutc=-7\""; 
ENTRY="0 1 * * 1,2,3,4,5 $COMMAND"; 
echo "$ENTRY" >> $FILE; 

COMMAND="php /var/www/base/artisan command:moveemdnetsuite all --env=production && curl \"http://www.cronrat.com/r/7n9GvvYu\" --data-urlencode \"rat=moveemdnetuite\" --data-urlencode \"crontab=0 1 * * 1,2,3,4,5\" --data-urlencode \"toutc=-7\""; 
ENTRY="0 1 * * 1,2,3,4,5 $COMMAND"; 
echo "$ENTRY" >> $FILE; 

COMMAND="php /var/www/base/artisan command:dayemd --env=production && curl \"http://www.cronrat.com/r/7n9GvvYu\" --data-urlencode \"rat=dayemd\" --data-urlencode \"crontab=0 1 * * 1,2,3,4,5\" --data-urlencode \"toutc=-7\""; 
ENTRY="0 1 * * 1,2,3,4,5 $COMMAND"; 
echo "$ENTRY" >> $FILE; 

COMMAND="php /var/www/base/artisan command:dayees --env=production && curl \"http://www.cronrat.com/r/7n9GvvYu\" --data-urlencode \"rat=dayes\" --data-urlencode \"crontab=0 1 * * 3\" --data-urlencode \"toutc=-7\""; 
ENTRY="0 1 * * 3 $COMMAND"; 
echo "$ENTRY" >> $FILE; 

COMMAND="php /var/www/base/artisan command:emdcalendar --env=production && curl \"http://www.cronrat.com/r/7n9GvvYu\" --data-urlencode \"rat=emdcalendar\" --data-urlencode \"crontab=*/5 * * * *\" --data-urlencode \"toutc=-7\""; 
ENTRY="*/5 * * * * $COMMAND"; 
echo "$ENTRY" >> $FILE; 

crontab $FILE;
rm $FILE
