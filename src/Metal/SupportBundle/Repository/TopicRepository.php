<?php

namespace Metal\SupportBundle\Repository;

use Doctrine\DBAL\Connection;
use Doctrine\ORM\EntityRepository;
use Metal\UsersBundle\Entity\User;

class TopicRepository extends EntityRepository
{
    public function getUnviewedTopicsCount(User $user)
    {
        return $this->getTopicsForUserQb($user)
            ->select('COUNT(t.id) as _count')
            ->andWhere('t.viewedAt IS NULL')
            ->getQuery()
            ->getSingleScalarResult();
    }

    public function getTopicsForUserQb(User $user)
    {
        $qb = $this
            ->createQueryBuilder('t')
            ->andWhere('t.receiver = :user')
            ->setParameter('user', $user)
            ->addOrderBy('t.unreadAnswersCount', 'DESC')
            ->addOrderBy('t.createdAt', 'DESC')
        ;

        return $qb;
    }

    public function updateAnswersCount(array $criteria = array())
    {
        $connection = $this->_em->getConnection();
        $resetQb = $connection->createQueryBuilder()
            ->update('support_topic', 'st')
            ->set('st.answers_count', 0)
            ->set('st.unread_answers_count', 0);

        if (isset($criteria['topics_ids'])) {
            $resetQb
                ->andWhere('id IN (:topics_ids)')
                ->setParameter('topics_ids', $criteria['topics_ids'], Connection::PARAM_INT_ARRAY);
        }

        $resetQb
            ->execute();

        $sqParams = $sqParamsTypes = array();
        $sqQb = $connection->createQueryBuilder()
            ->select('sa.topic_id AS topic_id')
            ->addSelect('COUNT(sa.id) AS answers_count')
            // в непрочитанные записываем только ответы от модератора
            ->addSelect('COUNT(IF(sa.viewed_at IS NULL AND u.additional_role_id > 0, 1, NULL)) AS unread_answers_count')
            ->from('support_answer', 'sa')
            ->join('sa', 'User', 'u', 'sa.author_id = u.User_ID')
            ->groupBy('sa.topic_id');

        if (isset($criteria['topics_ids'])) {
            $sqQb
                ->andWhere('sa.topic_id IN (:topics_ids)');

            $sqParams['topics_ids'] = $criteria['topics_ids'];
            $sqParamsTypes['topics_ids'] = Connection::PARAM_INT_ARRAY;
        }

        $connection->executeUpdate(
            'UPDATE support_topic st JOIN ('.$sqQb->getSQL().') AS sq ON sq.topic_id = st.id'
            .' SET st.answers_count = sq.answers_count, st.unread_answers_count = sq.unread_answers_count',
            $sqParams,
            $sqParamsTypes
        );
    }
}
