DATE=`date +%Y-%m-%d_%H-%M`
DATABASE=metal_metalloprokat2
mysqldump -u m_metalloprokat --password=uVei7wieluoW -h db-ro \
  --add-drop-table \
  --ignore-table=$DATABASE.Stats_Attendance \
  --ignore-table=$DATABASE.Stats_Browser \
  --ignore-table=$DATABASE.Stats_Geo \
  --ignore-table=$DATABASE.Stats_IP \
  --ignore-table=$DATABASE.Stats_IP2Country \
  --ignore-table=$DATABASE.Stats_Log \
  --ignore-table=$DATABASE.Stats_OS \
  --ignore-table=$DATABASE.Stats_Phrases \
  --ignore-table=$DATABASE.Stats_Popularity \
  --ignore-table=$DATABASE.Stats_Referer \
  --ignore-table=$DATABASE.SrchBann_Shows \
  --ignore-table=$DATABASE.SrchBann_Shows_o \
  --ignore-table=$DATABASE.statEstimate \
  --ignore-table=$DATABASE.Objava_Stat_Total \
  --ignore-table=$DATABASE.stats4comp_city \
  --ignore-table=$DATABASE.stats4comp_daily \
  --ignore-table=$DATABASE.stats4price_cat \
  --ignore-table=$DATABASE.stats4price_daily \
  --ignore-table=$DATABASE.stats_product_changes \
  --ignore-table=$DATABASE.announcement_stats_element \
  --ignore-table=$DATABASE.stats_element \
  --ignore-table=$DATABASE.ban_request \
  $DATABASE | gzip > ./$DATABASE-$DATE.sql.gz
