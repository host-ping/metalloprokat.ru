<?php

namespace Metal\ProjectBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;

/**
 * @ORM\Embeddable
 */
class Metadata
{
    /**
     * @ORM\Column(length=255, name="meta_title", nullable=true)
     * @Assert\Length(max="255")
     */
    protected $title;

    /**
     * @ORM\Column(length=255, name="h1_title", nullable=true)
     * @Assert\Length(max="255")
     */
    protected $h1Title;

    /**
     * @ORM\Column(length=1000, name="meta_description", nullable=true)
     * @Assert\Length(max="1000")
     */
    protected $description;

    /**
     * @ORM\Column(length=255, name="meta_keywords", nullable=true)
     * @Assert\Length(max="255")
     */
    protected $keywords;

    public function getTitle()
    {
        return $this->title;
    }

    public function setTitle($title)
    {
        $this->title = $title;
    }

    public function getDescription()
    {
        return $this->description;
    }

    public function setDescription($description)
    {
        $this->description = $description;
    }

    public function getKeywords()
    {
        return $this->keywords;
    }

    public function setKeywords($keywords)
    {
        $this->keywords = $keywords;
    }

    public function getH1Title()
    {
        return $this->h1Title;
    }

    public function setH1Title($h1Title)
    {
        $this->h1Title = $h1Title;
    }
}
