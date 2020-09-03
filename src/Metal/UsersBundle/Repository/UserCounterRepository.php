<?php

namespace Metal\UsersBundle\Repository;

use Doctrine\DBAL\Connection;
use Doctrine\ORM\EntityRepository;

class UserCounterRepository extends EntityRepository
{
    public function synchronizeUsersCounters()
    {
        $this->_em->getConnection()->executeQuery('
          INSERT IGNORE INTO user_counter (id, user_id, updated_at)
          SELECT User_ID, User_ID, :now FROM User', array('now' => new \DateTime()), array('now' => 'datetime'));

        $this->_em->getConnection()->executeQuery('UPDATE User SET counter_id = User_ID');

    }

    public function updateUsersCounters(array $usersIds, array $columns = array())
    {
        $connection = $this->_em->getConnection();
        if (!$columns) {
            $columns = array(
                'favorite_company_count',
                'favorite_product_count',
                'favorite_demand_count',
                'new_moderator_answers',
            );
        }
        $columns = array_fill_keys($columns, true);

        $qb = $connection->createQueryBuilder()
            ->update('user_counter', 'uc')
        ;

        if (!empty($columns['favorite_company_count'])) {
            $qb->set('favorite_company_count', 0);
        }

        if (!empty($columns['favorite_product_count'])) {
            $qb->set('favorite_product_count', 0);
        }

        if (!empty($columns['favorite_demand_count'])) {
            $qb->set('favorite_demand_count', 0);
        }

        if (!empty($columns['new_moderator_answers'])) {
            $qb->set('new_moderator_answers', 0);
        }

        if ($usersIds) {
            $qb->andWhere('uc.user_id IN (:users_ids)')
                ->setParameter('users_ids', $usersIds, Connection::PARAM_INT_ARRAY);
        }
        $qb->execute();

        $baseSelectQb = $connection->createQueryBuilder()
            ->from('favorite', 'f')
            ->groupBy('f.user_id');

        if ($usersIds) {
            $baseSelectQb->where('f.user_id in (:users_ids)')
                ->setParameter('users_ids', $usersIds, Connection::PARAM_INT_ARRAY);
        }

        if (!empty($columns['favorite_company_count'])) {
            $qb = $connection->createQueryBuilder();
            $counters = $qb->select('count(f.company_id) as fav_companies_count')
                ->addSelect('f.user_id')
                ->from('favorite_company', 'f')
                ->groupBy('f.user_id')
                ->execute()
                ->fetchAll();

            $qb = $connection->createQueryBuilder()
                ->update('user_counter', 'uc')
                ->where('uc.user_id = :user_id')
                ->set('uc.updated_at', ':now')
                ->setParameter('now', new \DateTime(), 'datetime');
            foreach ($counters as $counter) {
                $qb->set('favorite_company_count', $counter['fav_companies_count'])
                    ->setParameter('user_id', $counter['user_id'])
                    ->execute();
            }
        }

        if (!empty($columns['favorite_product_count'])) {
            $qb = $baseSelectQb;
            $counters = $qb->select('count(f.product_id) as fav_products_count')
                ->addSelect('user_id')
                ->where('f.product_id IS NOT NULL')
                ->execute()
                ->fetchAll();

            $qb = $connection->createQueryBuilder()
                ->update('user_counter', 'uc')
                ->where('uc.user_id = :user_id')
                ->set('uc.updated_at', ':now')
                ->setParameter('now', new \DateTime(), 'datetime');
            foreach ($counters as $counter) {
                $qb->set('favorite_product_count', $counter['fav_products_count'])
                    ->setParameter('user_id', $counter['user_id'])
                    ->execute();
            }
        }

        if (!empty($columns['favorite_demand_count'])) {
            $qb = $baseSelectQb;
            $counters = $qb->select('count(f.demand_id) as fav_demands_count')
                ->addSelect('user_id')
                ->where('f.demand_id IS NOT NULL')
                ->execute()
                ->fetchAll();

            $qb = $connection->createQueryBuilder()
                ->update('user_counter', 'uc')
                ->where('uc.user_id = :user_id')
                ->set('uc.updated_at', ':now')
                ->setParameter('now', new \DateTime(), 'datetime');
            foreach ($counters as $counter) {
                $qb->set('favorite_demand_count', $counter['fav_demands_count'])
                    ->setParameter('user_id', $counter['user_id'])
                    ->execute();
            }
        }

        if (!empty($columns['new_moderator_answers'])) {
            $sqParams = $sqParamsTypes = array();
            $qb = $connection->createQueryBuilder()
                ->select('COUNT(IF(sa.viewed_at IS NULL, 1, NULL)) AS unread_answers')
                ->from('support_answer', 'sa')
                ->join('sa', 'User', 'u', 'sa.author_id = u.User_ID')
                ->join('sa', 'support_topic', 'st', 'st.id = sa.topic_id')
                ->addSelect('st.author_id AS user_id')
                ->andWhere('u.additional_role_id > 0')
                ->groupBy('st.author_id');

            if ($usersIds) {
                $qb->andWhere('st.author_id IN (:users_ids)');
                $sqParams['users_ids'] = $usersIds;
                $sqParamsTypes['users_ids'] = Connection::PARAM_INT_ARRAY;
            }

            $connection->executeUpdate('
                UPDATE user_counter uc JOIN ('.$qb->getSQL().') AS sq ON sq.user_id = uc.user_id
                 SET uc.new_moderator_answers = sq.unread_answers',
                $sqParams,
                $sqParamsTypes
            );
        }
    }

    public function changeCounter($user, $countName, $action)
    {
        if (!in_array($countName, array('favoriteCompaniesCount', 'favoriteProductsCount', 'favoriteDemandsCount', 'newModeratorAnswersCount'))) {
            throw new \InvalidArgumentException('Wrong count name');
        }

        $qb = $this->createQueryBuilder('uc')
            ->update();
        if ($action) {
            $qb->set('uc.'.$countName, 'uc.'.$countName.' + 1');
        } else {
            $qb->set('uc.'.$countName, 'uc.'.$countName.' - 1');
        }

        $qb->set('uc.updatedAt', ':now')
            ->setParameter('now', new \DateTime())
            ->where('uc.user = :user')
            ->setParameter('user', $user)
            ->getQuery()
            ->execute();
    }
}
