DATE=`date +%Y-%m-%d_%H-%M`
DATABASE=metal_metalloprokat2
mysqldump -u m_metalloprokat --password=uVei7wieluoW -h db-rw \
  --add-drop-table \
  $DATABASE menu_item menu_item_closure Message73 category_closure | gzip > ./$DATABASE-menu-$DATE.sql.gz
