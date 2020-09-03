DATE=`date +%Y-%m-%d_%H-%M`
DATABASE=metprok
mysqldump -u metprok --password=ugoR5eis -h innodb-db2.katushkin.local \
  --add-drop-table \
  $DATABASE Message159 | gzip > ./$DATABASE-product-attributes-$DATE.sql.gz
