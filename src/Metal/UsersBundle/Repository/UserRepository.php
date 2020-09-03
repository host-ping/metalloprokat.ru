<?php

namespace Metal\UsersBundle\Repository;

use Doctrine\ORM\EntityRepository;
use Metal\CompaniesBundle\Entity\Company;
use Metal\ComplaintsBundle\Entity\ValueObject\ComplaintTypeProvider;
use Metal\TerritorialBundle\Entity\City;
use Metal\TerritorialBundle\Entity\Region;
use Metal\TerritorialBundle\Entity\TerritoryInterface;
use Metal\UsersBundle\Entity\User;
use Symfony\Component\Security\Core\Exception\UsernameNotFoundException;
use Symfony\Component\Security\Core\User\UserInterface;
use Symfony\Component\Security\Core\User\UserProviderInterface;

class UserRepository extends EntityRepository implements UserProviderInterface
{
    /**
     * @param User $user
     */
    public function attachHistoryToUser(User $user)
    {
        $user->setAttribute('author_history', array());
        $user->setAttribute('user_history', array());

        $authorByCompanyHistories = $this->_em->createQueryBuilder()
            ->select('ch')
            ->from('MetalCompaniesBundle:CompanyHistory', 'ch')
            ->where('ch.author = :user_id')
            ->setParameter('user_id', $user->getId())
            ->orderBy('ch.createdAt', 'DESC')
            ->getQuery()
            ->getResult();

        $authorByUserHistories = $this->_em->createQueryBuilder()
            ->select('uh')
            ->from('MetalUsersBundle:UserHistory', 'uh')
            ->where('uh.author = :user_id')
            ->setParameter('user_id', $user->getId())
            ->orderBy('uh.createdAt', 'DESC')
            ->getQuery()
            ->getResult();

        $user->setAttribute('author_history', array_merge($authorByCompanyHistories, $authorByUserHistories));

        $userByCompanyHistories = $this->_em->createQueryBuilder()
            ->select('ch')
            ->from('MetalCompaniesBundle:CompanyHistory', 'ch')
            ->where('ch.user = :user_id')
            ->setParameter('user_id', $user->getId())
            ->orderBy('ch.createdAt', 'DESC')
            ->getQuery()
            ->getResult();

        $userByUserHistories = $this->_em->createQueryBuilder()
            ->select('uh')
            ->from('MetalUsersBundle:UserHistory', 'uh')
            ->where('uh.user = :user_id')
            ->setParameter('user_id', $user->getId())
            ->orderBy('uh.createdAt', 'DESC')
            ->getQuery()
            ->getResult();

        $user->setAttribute('user_history', array_merge($userByCompanyHistories, $userByUserHistories));
    }

    /**
     * @param User $user
     * @param $demandId
     */
    public function attachComplaintDemandsIdsToUser(User $user, $demandId = null)
    {
        $user->setAttribute('complaint_demands_ids', array());

        $complaintDemandsIdsByUserQb =
            $this->_em->createQueryBuilder()
                ->select('dc.id')
                ->addSelect('d.id as demandId')
                ->from('MetalComplaintsBundle:DemandComplaint', 'dc')
                ->join('dc.demand', 'd')
                ->where('d.user = :user_id')
                ->andWhere('dc.complaintTypeId = :type_id')
                ->setParameter('user_id', $user->getId())
                ->setParameter('type_id', ComplaintTypeProvider::SPAM_COMPLAINT)
            ;

            if ($demandId) {
                $complaintDemandsIdsByUserQb
                    ->andWhere('d.id <> :demand_id')
                    ->setParameter('demand_id', $demandId)
                ;
            }

        $complaintDemandsIdsByUser = array_column(
            $complaintDemandsIdsByUserQb
                ->getQuery()
                ->getResult(),
            'demandId'
        );

        $user->setAttribute('complaint_demands_ids', $complaintDemandsIdsByUser);
    }

    /**
     * @param User $user
     */
    public function attachIpsToUser(User $user)
    {
        $user->setAttribute('user_ips', array());

        $userIps =
            $this->_em->createQueryBuilder()
                ->select('ci.ip')
                ->from('MetalProjectBundle:ClientIp', 'ci', 'ci.ip')
                ->where('ci.user = :user_id')
                ->setParameter('user_id', $user->getId())
                ->getQuery()
                ->getResult();

        $user->setAttribute('user_ips', array_keys($userIps));
    }

    /**
     * {@inheritdoc}
     */
    public function loadUserByUsername($username)
    {
        $user = $this
            ->createQueryBuilder('u')
            ->leftJoin('u.counter', 'uc')
            ->addSelect('uc')
            ->leftJoin('u.city', 'userCity')
            ->addSelect('userCity')
            ->leftJoin('u.company', 'c')
            ->addSelect('c')
            ->leftJoin('c.counter', 'cc')
            ->addSelect('cc')
            ->leftJoin('c.country', 'country')
            ->addSelect('country')
            ->where('u.email = :username')
            ->andWhere('u.isEnabled = true')
            ->setMaxResults(1)
            ->setParameter('username', $username)
            ->getQuery()
            ->getOneOrNullResult()
        ;

        if (null === $user) {
            throw new UsernameNotFoundException();
        }

        return $user;
    }

    /**
     * {@inheritdoc}
     */
    public function refreshUser(UserInterface $user)
    {
        return $this->loadUserByUsername($user->getUsername());
    }

    /**
     * {@inheritdoc}
     */
    public function supportsClass($class)
    {
        throw new \BadMethodCallException('This method never should be called. See Symfony\Bridge\Doctrine\Security\User\EntityUserProvider');
    }

    /**
     * @param Company $company
     *
     * @return User[]
     */
    public function getApprovedUsers(Company $company)
    {
        return $this
            ->createQueryBuilder('u')
            ->where('u.company = :company')
            ->andWhere('u.isEnabled = true')
            ->andWhere('u.approvedAt IS NOT NULL')
            ->setParameter('company', $company)
            ->getQuery()
            ->getResult();
    }

    /**
     * @param Company $company
     *
     * @return User[]
     */
    public function getVisibleOnMiniSiteUsers(Company $company)
    {
        return $this
            ->createQueryBuilder('u')
            ->where('u.company = :company')
            ->andWhere('u.approvedAt IS NOT NULL')
            ->andWhere('u.isEnabled = true')
            ->andWhere('u.displayInContacts = true')
            ->orderBy('u.displayPosition', 'ASC')
            ->setParameter('company', $company)
            ->getQuery()
            ->getResult();
    }

    /**
     * @param User[] $users
     */
    public function attachSubscriberToUsers(array $users)
    {
        if (!$users) {
            return;
        }

        $usersDirected = array();
        foreach ($users as $user) {
            $usersDirected[$user->getId()] = $user;
        }

        $subscribers = $this->_em->createQueryBuilder()
            ->select('s AS subscriber, IDENTITY(s.user) AS userId')
            ->from('MetalNewsletterBundle:Subscriber', 's')
            ->andWhere('s.user IN (:users_ids)')
            ->setParameter('users_ids', $users)
            ->getQuery()
            ->getResult();

        $subscriberToUser = array();
        foreach ($subscribers as $subscriber) {
            $subscriberToUser[$subscriber['userId']] = $subscriber['subscriber'];
        }

        foreach ($subscriberToUser as $userId => $subscriber) {
            $usersDirected[$userId]->setAttribute('subscriber', $subscriber);
        }
    }

    /**
     * @return User[]
     */
    public function getModerators()
    {
        return $this
            ->createQueryBuilder('u')
            ->andWhere('u.additionalRoleId > 0')
            ->andWhere('u.isEnabled = true')
            ->addOrderBy('u.firstName')
            ->getQuery()
            ->getResult();
    }

    public function getSimpleModerators()
    {
        $users = $this->getModerators();

        $simpleUsers = array();
        foreach ($users as $user) {
            $simpleUsers[$user->getId()] =  $user->getFullName();
        }

        return $simpleUsers;
    }

    /**
     *
     * @param Company $company
     * @param TerritoryInterface $territory
     *
     * @return User[]
     */
    public function getEmployeesForTerritory(Company $company, TerritoryInterface $territory)
    {
        if (!$company->getHasTerritorialRules()) {
            return $this->getVisibleOnMiniSiteUsers($company);
        }

        // должны выполняться правила
        // 1. Сотрудник виден в определенных городах
        // 2. Сотрудник виден во всех городах страны кроме каких-то
        // 3. Сотрудник виден в определенных регионах
        // 4. Сотрудника можно исключить из определенных регионов
        // 5. Сотрудник виден в определенных странах
        // 6. Сотрудника можно исключить из определенных стран

        $city = null;
        $region = null;
        $country = null;
        if ($territory instanceof City) {
            $city = $territory;
            $region = $territory->getRegion();
            $country = $territory->getCountry();
        } elseif ($territory instanceof Region) {
            $region = $territory;
            $country = $territory->getCountry();
        } else {
            $country = $territory;
        }

        $qb = $this->createQueryBuilder('u')
            ->select('u AS user')
            ->addSelect('u.id AS id')
            ->addSelect('u.displayOnlyInSpecifiedCities AS displayOnlyInSpecifiedCities')
            ->andWhere('u.company = :company')
            ->setParameter('company', $company)
            ->andWhere('u.approvedAt IS NOT NULL')
            ->andWhere('u.isEnabled = true')
            //TODO: условие ниже - под вопросом
//            ->andWhere('u.displayInContacts = true')
            ->orderBy('u.displayPosition', 'ASC');

        if ($city) {
            $qb
                ->addSelect('userCities.isExcluded AS isExcludedCity')
                ->addSelect('userCities.phone AS phone_city')
                ->leftJoin('u.userCities', 'userCities', 'WITH', 'userCities.city = :city')
                ->setParameter('city', $city);
        }

        if ($region) {
            $qb
                ->addSelect('userRegions.isExcluded AS isExcludedRegion')
                ->addSelect('userRegions.phone AS phone_region')
                ->leftJoin('u.userCities', 'userRegions', 'WITH', 'userRegions.region = :region')
                ->setParameter('region', $region);
        }

        $users = $qb
            ->addSelect('userCountries.isExcluded AS isExcludedCountry')
            ->addSelect('userCountries.phone AS phone_country')
            ->leftJoin('u.userCities', 'userCountries', 'WITH', 'userCountries.country = :country')
            ->setParameter('country', $country)
            ->getQuery()
            ->getResult();

        $attachPhone = function (User $user, $phone) {
            if ($phone && !$user->hasAttribute('territorial_phone')) {
                $user->setAttribute('territorial_phone', $phone);
            }
        };

        $allUsers = $includedUsers = $excludedUsers = array();
        foreach ($users as $i => $userRow) {
            $user = $userRow['user'];
            $userId = $userRow['id'];
            if (!$userRow['displayOnlyInSpecifiedCities']) {
                $allUsers[$userId] = $user;
            }

            if ($city) {
                if ($userRow['isExcludedCity']) {
                    $excludedUsers[$userId] = $user;
                } elseif (null !== $userRow['isExcludedCity']) {
                    $attachPhone($user, $userRow['phone_city']);
                    $includedUsers[$userId] = $user;
                }
            }

            if ($region) {
                if ($userRow['isExcludedRegion']) {
                    $excludedUsers[$userId] = $user;
                } elseif (null !== $userRow['isExcludedRegion']) {
                    $attachPhone($user, $userRow['phone_region']);
                    $includedUsers[$userId] = $user;
                }
            }

            if ($userRow['isExcludedCountry']) {
                $excludedUsers[$userId] = $user;
            } elseif (null !== $userRow['isExcludedCountry']) {
                $attachPhone($user, $userRow['phone_country']);
                $includedUsers[$userId] = $user;
            }
        }

        foreach ($excludedUsers as $userId => $excludedUser) {
            unset($includedUsers[$userId], $allUsers[$userId]);
        }

        if ($includedUsers) { // если есть явно включенные пользователи в городе - отдаем только их
            return array_values($includedUsers);
        }

        // отдаем всех пользователей без исключенных
        return array_values($allUsers);
    }
}
