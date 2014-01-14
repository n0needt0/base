#!/bin/bash

COMMAND="/var/backup/bin/db.sh"; 
FILE="/tmp/$(basename $0).$RANDOM.txt"; 
FILE2="/tmp/$(basename $0).$RANDOM.txt"; 
ENTRY="0 12,15,18 * * 1,2,3,4,5 bash $COMMAND"; 
crontab -l > $FILE 2> /dev/null; 
cat $FILE | grep -v "$COMMAND" > $FILE2; 
echo "$ENTRY" >> $FILE2; 
crontab $FILE2;
rm $FILE $FILE2

COMMAND="bash /var/backup/bin/_logmonitors/monitorlog.sh"; 
FILE="/tmp/$(basename $0).$RANDOM.txt"; 
FILE2="/tmp/$(basename $0).$RANDOM.txt"; 
ENTRY="0 1 * * 1,2,3,4,5 $COMMAND"; 
crontab -l > $FILE 2> /dev/null; 
cat $FILE | grep -v "$COMMAND" > $FILE2; 
echo "$ENTRY" >> $FILE2; 
crontab $FILE2;
rm $FILE $FILE2

COMMAND="php /var/www/base/artisan command:qaemd --env=production"; 
FILE="/tmp/$(basename $0).$RANDOM.txt"; 
FILE2="/tmp/$(basename $0).$RANDOM.txt"; 
ENTRY="0 1 * * 1,2,3,4,5 $COMMAND"; 
crontab -l > $FILE 2> /dev/null; 
cat $FILE | grep -v "$COMMAND" > $FILE2; 
echo "$ENTRY" >> $FILE2; 
crontab $FILE2;
rm $FILE $FILE2

COMMAND="php /var/www/base/artisan command:moveemdbilltrac all --env=production"; 
FILE="/tmp/$(basename $0).$RANDOM.txt"; 
FILE2="/tmp/$(basename $0).$RANDOM.txt"; 
ENTRY="0 1 * * 1,2,3,4,5 $COMMAND"; 
crontab -l > $FILE 2> /dev/null; 
cat $FILE | grep -v "$COMMAND" > $FILE2; 
echo "$ENTRY" >> $FILE2; 
crontab $FILE2;
rm $FILE $FILE2

COMMAND="php /var/www/base/artisan command:moveemdnetsuite all --env=production"; 
FILE="/tmp/$(basename $0).$RANDOM.txt"; 
FILE2="/tmp/$(basename $0).$RANDOM.txt"; 
ENTRY="0 1 * * 1,2,3,4,5 $COMMAND"; 
crontab -l > $FILE 2> /dev/null; 
cat $FILE | grep -v "$COMMAND" > $FILE2; 
echo "$ENTRY" >> $FILE2; 
crontab $FILE2;
rm $FILE $FILE2
