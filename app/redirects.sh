#!/usr/bin/env bash

#need run    $ sudo chmod +x  %PATH%/redirects.sh

wget -SO- -T 1 -t 1 --spider http://chel.metalloprokat.ru/metiz/setka/armaturnaya_kladochnaya/ 2>&1 | grep "http://chel.metalloprokat.ru/metiz/setka/armaturnaya/kladochnaya/$"
wget -SO- -T 1 -t 1 --spider http://chel.metalloprokat.ru/sort/armatura/a3/35gs/10/ 2>&1 | grep "http://chel.metalloprokat.ru/sort/armatura/10/a3/35gs/$"
wget -SO- -T 1 -t 1 --spider http://chel.metalloprokat.ru/sort/armatura/10/999/888/ 2>&1 | grep "http://chel.metalloprokat.ru/sort/armatura/10/$"
wget -SO- -T 1 -t 1 --spider http://vladivostok.metalloprokat.ru/sort_nerz/kvadrat_nerz/12/03h18n12-vi_03h18n12-vi/ 2>&1 | grep "http://vladivostok.metalloprokat.ru/sort_nerz/kvadrat_nerz/12/03h18n12-vi/$"
wget -SO- -T 1 -t 1 --spider http://vladivostok.metalloprokat.ru/sort_nerz/kvadrat_nerz/.12/ 2>&1 | grep "http://vladivostok.metalloprokat.ru/sort_nerz/kvadrat_nerz/12/$"
wget -SO- -T 1 -t 1 --spider http://vladivostok.metalloprokat.ru/sort_nerz/kvadrat_nerz/12 2>&1 | grep "http://vladivostok.metalloprokat.ru/sort_nerz/kvadrat_nerz/12/$"
wget -SO- -T 1 -t 1 --spider http://vladivostok.metalloprokat.ru/kinmet/sort_nerz/kvadrat_nerz/12/123123123/ 2>&1 | grep "http://vladivostok.metalloprokat.ru/kinmet/sort_nerz/kvadrat_nerz/12/$"
wget -SO- -T 1 -t 1 --spider http://kinmet.metalloprokat.ru/sort_nerz/kvadrat_nerz/12/123123123/ 2>&1 | grep "http://kinmet.metalloprokat.ru/sort_nerz/kvadrat_nerz/12/$"
wget -SO- -T 1 -t 1 --spider http://ufa.product.ru/napitki/krepkie-spirtnye/vodka/laminariia/ 2>&1 | grep "http://ufa.product.ru/napitki/krepkie-spirtnye/vodka/laminariya/$"
