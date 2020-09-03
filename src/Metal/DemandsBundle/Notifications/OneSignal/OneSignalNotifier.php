<?php

namespace Metal\DemandsBundle\Notifications\OneSignal;

use Metal\DemandsBundle\Notifications\Notifier;
use Metal\DemandsBundle\Notifications\OneSignal\MessageFormatter;
use Metal\DemandsBundle\Notifications\DataProvider\DemandInfoProvider;

class OneSignalNotifier implements Notifier
{
    private $messageFormatter;

    private $demandInfoProvider;

    public function __construct(
        MessageFormatter $messageFormatter,
        DemandInfoProvider $demandInfoProvider
    ) {
        $this->messageFormatter = $messageFormatter;
        $this->demandInfoProvider = $demandInfoProvider;
    }

    public function notifyOnNewDemand(int $demandId): void
    {
        $demandInfo = $this->demandInfoProvider->get($demandId);

        if (!$demandInfo->isPublic()) {
            // уведомляем только о публичных заявках
            return;
        }

        echo $this->sendMessage(
            $this->messageFormatter->getTitle($demandInfo),
            $this->messageFormatter->getMessage($demandInfo),
            $demandInfo->getViewUrl(),
            'e08e530b-9e69-454a-8d54-e673200262a5',
            'Yzg5MWU2M2EtNTUzMi00ZTUxLWEwYTQtMDkwZmY1NjJkMWM3',
            $this->messageFormatter->getCategoryFilters($demandInfo)
        );
    }

    private function sendMessage($title, $content,$url, $app_id, $app_key, $filters)
    {
        $fields = array(
            'app_id' => $app_id,
            'include_player_ids' => array('8952a65a-d4d3-471a-a1ec-df8e4005b467'),
            'filters' => $filters,
            'contents' => array("en" => $content),
            'headings' => array("en" => $title),
            'url' => $url,
            'chrome_web_icon' => 'https://assets.metalloprokat.test/bundles/metalproject/img/metalloprokat/logo.png'
        );

        $fields = json_encode($fields);

        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, "https://onesignal.com/api/v1/notifications");
        curl_setopt($ch, CURLOPT_HTTPHEADER, array('Content-Type: application/json; charset=utf-8',
            'Authorization: Basic '.$app_key));
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, TRUE);
        curl_setopt($ch, CURLOPT_HEADER, FALSE);
        curl_setopt($ch, CURLOPT_POST, TRUE);
        curl_setopt($ch, CURLOPT_POSTFIELDS, $fields);
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, FALSE);

        $response = curl_exec($ch);
        curl_close($ch);

        return $response;
    }

}