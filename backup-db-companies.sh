DATE=`date +%Y-%m-%d_%H-%M`
DATABASE=metal_metalloprokat2
mysqldump -u m_metalloprokat --password=uVei7wieluoW -h db-rw \
  --add-drop-table \
  $DATABASE Message75 company_payment_details | gzip > ./$DATABASE-companies-$DATE.sql.gz
