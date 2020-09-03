DATE=`date +%Y-%m-%d_%H-%M`
DATABASE=metal_metalloprokat2
mysqldump -u m_metalloprokat --password=uVei7wieluoW -h db-rw \
  --add-drop-table --verbose \
  $DATABASE stats_product_changes | gzip > ./$DATABASE-stats_product_changes-$DATE.sql.gz
