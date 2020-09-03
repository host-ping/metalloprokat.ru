<?php

namespace Metal\CategoriesBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity(repositoryClass="Metal\CategoriesBundle\Repository\ParameterRepository")
 * @ORM\Table(name="Message162")
 */
class Parameter
{
    /**
     * @ORM\Id
     * @ORM\GeneratedValue
     * @ORM\Column(type="integer", name="Message_ID")
     */
    protected $id;

    /** @ORM\Column(type="integer", name="PriorityShow") */
    protected $priority;

    /** @ORM\Column(type="text", name="text") */
    protected $text;

    /**
     * @ORM\ManyToOne(targetEntity="ParameterGroup", inversedBy="parameters")
     * @ORM\JoinColumn(name="Parent_Razd", referencedColumnName="Message_ID", nullable=false)
     *
     * @var ParameterGroup
     */
    protected $parameterGroup;

    /**
     * @ORM\ManyToOne(targetEntity="ParameterOption")
     * @ORM\JoinColumn(name="Par_ID", referencedColumnName="Message_ID", nullable=true)
     *
     * @var ParameterOption
     */
    protected $parameterOption;

    /** @ORM\Column(type="text", name="FriendIDs") */
    protected $friendIds;

    /** @ORM\Column(type="text", name="FriendsType") */
    protected $friendsFlags;

    /** @ORM\Column(type="text", name="FriendCatIDs") */
    protected $friendCategoryIds;

    /** @ORM\Column(type="text", name="FriendsCatType") */
    protected $friendsCategoryFlags;

    /**
     * @return mixed
     */
    public function getId()
    {
        return $this->id;
    }

    /**
     * @param ParameterGroup $parameterGroup
     */
    public function setParameterGroup(ParameterGroup $parameterGroup)
    {
        $this->parameterGroup = $parameterGroup;
    }

    /**
     * @return ParameterGroup
     */
    public function getParameterGroup()
    {
        return $this->parameterGroup;
    }

    /**
     * @param ParameterOption $parameterOption
     */
    public function setParameterOption(ParameterOption $parameterOption)
    {
        $this->parameterOption = $parameterOption;
    }

    /**
     * @return ParameterOption
     */
    public function getParameterOption()
    {
        return $this->parameterOption;
    }

    /**
     * @param mixed $priority
     */
    public function setPriority($priority)
    {
        $this->priority = $priority;
    }

    /**
     * @return mixed
     */
    public function getPriority()
    {
        return $this->priority;
    }

    /**
     * @param mixed $text
     */
    public function setText($text)
    {
        $this->text = $text;
    }

    /**
     * @return mixed
     */
    public function getText()
    {
        return $this->text;
    }

    /**
     * @param mixed $friendIds
     */
    public function setFriendIds($friendIds)
    {
        $this->friendIds = $friendIds;
    }

    /**
     * @return mixed
     */
    public function getFriendIds()
    {
        return $this->friendIds;
    }

    /**
     * @param mixed $friendsFlags
     */
    public function setFriendsFlags($friendsFlags)
    {
        $this->friendsFlags = $friendsFlags;
    }

    /**
     * @return mixed
     */
    public function getFriendsFlags()
    {
        return $this->friendsFlags;
    }

    /**
     * @param mixed $friendCategoryIds
     */
    public function setFriendCategoryIds($friendCategoryIds)
    {
        $this->friendCategoryIds = $friendCategoryIds;
    }

    /**
     * @return mixed
     */
    public function getFriendCategoryIds()
    {
        return $this->friendCategoryIds;
    }

    /**
     * @param mixed $friendsCategoryFlags
     */
    public function setFriendsCategoryFlags($friendsCategoryFlags)
    {
        $this->friendsCategoryFlags = $friendsCategoryFlags;
    }

    /**
     * @return mixed
     */
    public function getFriendsCategoryFlags()
    {
        return $this->friendsCategoryFlags;
    }

    public function getSlugCombined()
    {
        return $this->parameterGroup->getCategory()->getSlugCombined().'/'.$this->parameterOption->getSlug();
    }

}
