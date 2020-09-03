<?php

namespace Metal\UsersBundle\Service;

use Metal\UsersBundle\Entity\User;
use Metal\UsersBundle\Model\CompanyVisit;
use Metal\UsersBundle\Model\UserVisit;
use Predis\ClientInterface;

class OnlineTracker
{
    private const PREFIX_USER = 'user_last_visit';

    private const PREFIX_COMPANY = 'company_last_visit';

    private const DELIMITER = ':';

    private $redis;

    public function __construct(ClientInterface $redis)
    {
        $this->redis = $redis;
    }

    public function trackUserOnline(User $user, string $clientIp): void
    {
        $company = $user->getCompany();
        if ($company) {
            $this->redis->set(self::PREFIX_COMPANY.self::DELIMITER.$company->getId(), time());
        }

        $data = json_encode(
            [
                'date' => time(),
                'client_ip' => $clientIp,
                'company_id' => $company ? $company->getId() : null,
            ]
        );

        $this->redis->set(self::PREFIX_USER.self::DELIMITER.$user->getId(), $data);
    }

    /**
     * @return iterable|UserVisit[]
     */
    public function getUsersOnline(): iterable
    {
        //TODO: imple,ent
    }

    /**
     * @return iterable|CompanyVisit[]
     */
    public function getCompaniesOnline(): iterable
    {
        $keys = $this->redis->keys(self::PREFIX_COMPANY.self::DELIMITER.'*');

        foreach ($keys as $key) {
            $companyId = substr($key, strlen(self::PREFIX_COMPANY.self::DELIMITER));
            $visitAtTs = $this->redis->get($key);
            $visitedAt = new \DateTime();
            $visitedAt->setTimestamp($visitAtTs);

            yield new CompanyVisit($companyId, $visitedAt);
        }
    }

    public function invalidateCompanyIdOnline(int $companyId): void
    {
        $this->redis->del([self::PREFIX_COMPANY.self::DELIMITER.$companyId]);
    }
}
