<?php

namespace Metal\StatisticBundle\Repository;

use Doctrine\ORM\EntityRepository;
use Metal\StatisticBundle\Entity\StatsElement;

class StatsElementRepository extends EntityRepository
{
    public function insertStatsElement(StatsElement $statsElement, $insertFakeCopy = false)
    {
        $statsElementArray = $statsElement->toArray();

        $this->insert($statsElementArray);

        if ($insertFakeCopy) {
            $statsElementArray['fake'] = 1;
            $this->insert($statsElementArray);
        }
    }

    protected function insert(array $statsElementArray)
    {
        $this->_em->getConnection()->executeUpdate(
            'INSERT INTO stats_element (
                            `city_id`,
                            `user_id`,
                            `action`,
                            `source_type_id`,
                            `company_id`,
                            `ip`,
                            `user_agent`,
                            `referer`,
                            `session_id`,
                            `created_at`,
                            `date_created_at`,
                            `product_id`,
                            `category_id`,
                            `item_hash`,
                            `fake`) VALUES (
                            :city_id,
                            :user_id,
                            :action,
                            :source_type_id,
                            :company_id,
                            :ip,
                            :user_agent,
                            :referer,
                            :session_id,
                            :created_at,
                            :date_created_at,
                            :product_id,
                            :category_id,
                            :item_hash,
                            :fake)',
            $statsElementArray
        );
    }
}
