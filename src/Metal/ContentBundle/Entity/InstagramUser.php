<?php

namespace Metal\ContentBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity()
 * @ORM\Table(name="instagram_user")
 */
class InstagramUser
{
    /**
     * @ORM\Id
     * @ORM\GeneratedValue
     * @ORM\Column(type="integer", name="id")
     */
    protected $id;

    /**
     * @ORM\Column(length=255, name="name", nullable=false)
     */
    protected $name;

    /** @ORM\Column(type="boolean", name="is_enabled", nullable=false, options={"default":0}) */
    protected $isEnabled;

    public function __construct()
    {
        $this->isEnabled = false;
    }

    public function getId()
    {
        return $this->id;
    }

    public function getName()
    {
        return $this->name;
    }

    public function setName($name)
    {
        $this->name = (string)$name;
    }
    
    public function getIsEnabled()
    {
        return $this->isEnabled;
    }
    
    public function setIsEnabled($isEnabled)
    {
        $this->isEnabled = $isEnabled;
    }
}
