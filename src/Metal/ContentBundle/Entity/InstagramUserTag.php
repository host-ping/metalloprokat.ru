<?php

namespace Metal\ContentBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity()
 * @ORM\Table(name="instagram_user_tag")
 */
class InstagramUserTag
{
    /**
     * @ORM\Id
     * @ORM\GeneratedValue
     * @ORM\Column(type="integer", name="id")
     */
    protected $id;

    /**
     * @ORM\ManyToOne(targetEntity="InstagramUser")
     * @ORM\JoinColumn(name="user_id", referencedColumnName="id")
     *
     * @var InstagramUser
     */
    protected $user;

    /**
     * @ORM\Column(length=255, name="title", nullable=false)
     */
    protected $title;

    public function getId()
    {
        return $this->id;
    }

    /**
     * @return InstagramUser
     */
    public function getUser()
    {
        return $this->user;
    }

    /**
     * @param InstagramUser $user
     */
    public function setUser(InstagramUser $user)
    {
        $this->user = $user;
    }

    public function getTitle()
    {
        return $this->title;
    }

    public function setTitle($title)
    {
        $this->title = (string)$title;
    }
}