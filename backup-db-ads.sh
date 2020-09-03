DATE=`date +%Y-%m-%d_%H-%M`
DATABASE=ads
mysqldump -u metalloprokat --password=Degh8see -h innodb-db2.katushkin.local \
  --add-drop-table \
  $DATABASE | gzip > ./$DATABASE-$DATE.sql.gz
