#!/bin/bash

COMMAND="/var/backup/bin/db.sh && curl http://www.cronrat.com/r/7n9GvvYu/BASEDB_backup "; 
FILE="/tmp/$(basename $0).$RANDOM.txt"; 
FILE2="/tmp/$(basename $0).$RANDOM.txt"; 
ENTRY="0 12,15,18 * * 1,2,3,4,5 bash $COMMAND"; 
crontab -l > $FILE 2> /dev/null; 
cat $FILE | grep -v "$COMMAND" > $FILE2; 
echo "$ENTRY" >> $FILE2; 
crontab $FILE2;
rm $FILE $FILE2

COMMAND="bash /var/backup/bin/_logmonitors/monitorlog.sh && curl http://www.cronrat.com/r/7n9GvvYu/basemonitorlog?activeon=1111100"; 
FILE="/tmp/$(basename $0).$RANDOM.txt"; 
FILE2="/tmp/$(basename $0).$RANDOM.txt"; 
ENTRY="0 1 * * 1,2,3,4,5 $COMMAND"; 
crontab -l > $FILE 2> /dev/null; 
cat $FILE | grep -v "$COMMAND" > $FILE2; 
echo "$ENTRY" >> $FILE2; 
crontab $FILE2;
rm $FILE $FILE2

COMMAND="php /var/www/base/artisan command:qaemd --env=production && curl http://www.cronrat.com/r/7n9GvvYu/qaemd?activeon=1111100"; 
FILE="/tmp/$(basename $0).$RANDOM.txt"; 
FILE2="/tmp/$(basename $0).$RANDOM.txt"; 
ENTRY="0 1 * * 1,2,3,4,5 $COMMAND"; 
crontab -l > $FILE 2> /dev/null; 
cat $FILE | grep -v "$COMMAND" > $FILE2; 
echo "$ENTRY" >> $FILE2; 
crontab $FILE2;
rm $FILE $FILE2

COMMAND="php /var/www/base/artisan command:moveemdbilltrac all --env=production && curl http://www.cronrat.com/r/7n9GvvYu/moveemdbilltrac?activeon=1111100"; 
FILE="/tmp/$(basename $0).$RANDOM.txt"; 
FILE2="/tmp/$(basename $0).$RANDOM.txt"; 
ENTRY="0 1 * * 1,2,3,4,5 $COMMAND"; 
crontab -l > $FILE 2> /dev/null; 
cat $FILE | grep -v "$COMMAND" > $FILE2; 
echo "$ENTRY" >> $FILE2; 
crontab $FILE2;
rm $FILE $FILE2

COMMAND="php /var/www/base/artisan command:moveemdnetsuite all --env=production && curl http://www.cronrat.com/r/7n9GvvYu/moveemdnetuite?activeon=1111100"; 
FILE="/tmp/$(basename $0).$RANDOM.txt"; 
FILE2="/tmp/$(basename $0).$RANDOM.txt"; 
ENTRY="0 1 * * 1,2,3,4,5 $COMMAND"; 
crontab -l > $FILE 2> /dev/null; 
cat $FILE | grep -v "$COMMAND" > $FILE2; 
echo "$ENTRY" >> $FILE2; 
crontab $FILE2;
rm $FILE $FILE2

COMMAND="php /var/www/base/artisan command:dayemd --env=production && curl http://www.cronrat.com/r/7n9GvvYu/dayemd?activeon=1111100"; 
FILE="/tmp/$(basename $0).$RANDOM.txt"; 
FILE2="/tmp/$(basename $0).$RANDOM.txt"; 
ENTRY="0 1 * * 1,2,3,4,5 $COMMAND"; 
crontab -l > $FILE 2> /dev/null; 
cat $FILE | grep -v "$COMMAND" > $FILE2; 
echo "$ENTRY" >> $FILE2; 
crontab $FILE2;
rm $FILE $FILE2
