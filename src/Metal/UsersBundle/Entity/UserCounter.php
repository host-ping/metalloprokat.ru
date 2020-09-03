<?php

namespace Metal\UsersBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use Metal\ProjectBundle\Entity\Behavior\Updateable;

/**
 * @ORM\Entity(repositoryClass="Metal\UsersBundle\Repository\UserCounterRepository")
 * @ORM\Table(name="user_counter")
 */
class UserCounter
{
    /**
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="NONE")
     * @ORM\Column(type="integer", name="id")
     */
    protected $id;

    /**
     * @ORM\OneToOne(targetEntity="Metal\UsersBundle\Entity\User")
     * @ORM\JoinColumn(name="user_id", referencedColumnName="User_ID", nullable=false)
     *
     * @var User
     */
    protected $user;

    /**
     * Кол-во компаний в избранном
     *
     * @ORM\Column(type="integer", name="favorite_company_count", nullable=false, options={"default":0})
     */
    protected $favoriteCompaniesCount;

    /**
     * Кол-во продуктов в избранном
     *
     * @ORM\Column(type="integer", name="favorite_product_count", nullable=false, options={"default":0})
     */
    protected $favoriteProductsCount;

    /**
     * Кол-во заявок в избранном
     *
     * @ORM\Column(type="integer", name="favorite_demand_count", nullable=false, options={"default":0})
     */
    protected $favoriteDemandsCount;

    /**
     * Кол-во новых ответов модератора
     *
     * @ORM\Column(type="integer", name="new_moderator_answers", nullable=false, options={"default":0})
     */
    protected $newModeratorAnswersCount;

    use Updateable;

    public function __construct()
    {
        $this->updatedAt = new \DateTime();
        $this->favoriteCompaniesCount = 0;
        $this->favoriteDemandsCount = 0;
        $this->favoriteProductsCount = 0;
        $this->newModeratorAnswersCount = 0;
    }

    public function getId()
    {
        return $this->id;
    }

    public function setFavoriteCompaniesCount($favoriteCompaniesCount)
    {
        $this->favoriteCompaniesCount = $favoriteCompaniesCount;
    }

    public function getFavoriteCompaniesCount()
    {
        return $this->favoriteCompaniesCount;
    }

    public function setFavoriteDemandsCount($favoriteDemandsCount)
    {
        $this->favoriteDemandsCount = $favoriteDemandsCount;
    }

    public function getFavoriteDemandsCount()
    {
        return $this->favoriteDemandsCount;
    }

    public function setFavoriteProductsCount($favoriteProductsCount)
    {
        $this->favoriteProductsCount = $favoriteProductsCount;
    }

    public function getFavoriteProductsCount()
    {
        return $this->favoriteProductsCount;
    }

    /**
     * @param User $user
     */
    public function setUser(User $user)
    {
        $this->user = $user;
        $this->id = $user->getId();
    }

    /**
     * @return User
     */
    public function getUser()
    {
        return $this->user;
    }

    public function setNewModeratorAnswersCount($newModeratorAnswersCount)
    {
        $this->newModeratorAnswersCount = $newModeratorAnswersCount;
    }

    public function getNewModeratorAnswersCount()
    {
        return $this->newModeratorAnswersCount;
    }

}
