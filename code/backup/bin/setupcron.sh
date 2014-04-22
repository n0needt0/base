#!/bin/bash

COMMAND="/var/backup/bin/db.sh && curl \"http://www.cronrat.com/r/7n9GvvYu/BASEDB_backup?crontab=0+12%2C15%2C18+%2A+%2A+1%2C2%2C3%2C4%2C5&toutc=-7\""; 
FILE="/tmp/$(basename $0).$RANDOM.txt"; 
FILE2="/tmp/$(basename $0).$RANDOM.txt"; 
ENTRY="0 12,15,18 * * 1,2,3,4,5 bash $COMMAND"; 
crontab -l > $FILE 2> /dev/null; 
cat $FILE | grep -v "$COMMAND" > $FILE2; 
echo "$ENTRY" >> $FILE2; 
crontab $FILE2;
rm $FILE $FILE2

COMMAND="bash /var/backup/bin/_logmonitors/monitorlog.sh && curl \"http://www.cronrat.com/r/7n9GvvYu/basemonitorlog?crontab=0+1+%2A+%2A+1%2C2%2C3%2C4%2C5&toutc=-7\""; 
FILE="/tmp/$(basename $0).$RANDOM.txt"; 
FILE2="/tmp/$(basename $0).$RANDOM.txt"; 
ENTRY="0 1 * * 1,2,3,4,5 $COMMAND"; 
crontab -l > $FILE 2> /dev/null; 
cat $FILE | grep -v "$COMMAND" > $FILE2; 
echo "$ENTRY" >> $FILE2; 
crontab $FILE2;
rm $FILE $FILE2

COMMAND="php /var/www/base/artisan command:qaemd --env=production && curl \"http://www.cronrat.com/r/7n9GvvYu/qaemd?crontab=0+1+%2A+%2A+1%2C2%2C3%2C4%2C5&toutc=-7\""; 
FILE="/tmp/$(basename $0).$RANDOM.txt"; 
FILE2="/tmp/$(basename $0).$RANDOM.txt"; 
ENTRY="0 1 * * 1,2,3,4,5 $COMMAND"; 
crontab -l > $FILE 2> /dev/null; 
cat $FILE | grep -v "$COMMAND" > $FILE2; 
echo "$ENTRY" >> $FILE2; 
crontab $FILE2;
rm $FILE $FILE2

COMMAND="php /var/www/base/artisan command:moveemdbilltrac all --env=production && curl \"http://www.cronrat.com/r/7n9GvvYu/moveemdbilltrac?crontab=0+1+%2A+%2A+1%2C2%2C3%2C4%2C5&toutc=-7\""; 
FILE="/tmp/$(basename $0).$RANDOM.txt"; 
FILE2="/tmp/$(basename $0).$RANDOM.txt"; 
ENTRY="0 1 * * 1,2,3,4,5 $COMMAND"; 
crontab -l > $FILE 2> /dev/null; 
cat $FILE | grep -v "$COMMAND" > $FILE2; 
echo "$ENTRY" >> $FILE2; 
crontab $FILE2;
rm $FILE $FILE2

COMMAND="php /var/www/base/artisan command:moveemdnetsuite all --env=production && curl \"http://www.cronrat.com/r/7n9GvvYu/moveemdnetuite?crontab=0+1+%2A+%2A+1%2C2%2C3%2C4%2C5&toutc=-7\""; 
FILE="/tmp/$(basename $0).$RANDOM.txt"; 
FILE2="/tmp/$(basename $0).$RANDOM.txt"; 
ENTRY="0 1 * * 1,2,3,4,5 $COMMAND"; 
crontab -l > $FILE 2> /dev/null; 
cat $FILE | grep -v "$COMMAND" > $FILE2; 
echo "$ENTRY" >> $FILE2; 
crontab $FILE2;
rm $FILE $FILE2

COMMAND="php /var/www/base/artisan command:dayemd --env=production && curl \"http://www.cronrat.com/r/7n9GvvYu/dayemd?crontab=0+1+%2A+%2A+1%2C2%2C3%2C4%2C5&toutc=-7\""; 
FILE="/tmp/$(basename $0).$RANDOM.txt"; 
FILE2="/tmp/$(basename $0).$RANDOM.txt"; 
ENTRY="0 1 * * 1,2,3,4,5 $COMMAND"; 
crontab -l > $FILE 2> /dev/null; 
cat $FILE | grep -v "$COMMAND" > $FILE2; 
echo "$ENTRY" >> $FILE2; 
crontab $FILE2;
rm $FILE $FILE2
