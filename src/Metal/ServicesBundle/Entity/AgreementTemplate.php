<?php

namespace Metal\ServicesBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity(repositoryClass="Metal\ServicesBundle\Repository\AgreementTemplateRepository")
 * @ORM\Table(name="agreement_template")
 */
class AgreementTemplate
{
    const DEFAULT_AGREEMENT_ID = 1;
    const AGREEMENT = 2;
    const CORP_ABOUT_COMPANY = 3;
    const CORP_CONTACTS = 4;
    const MEDIA_RECLAME = 5;

    /**
     * @ORM\Id
     * @ORM\GeneratedValue
     * @ORM\Column(type="integer", name="id")
     */
    protected $id;

    /** @ORM\Column(length=255, name="title") */
    protected $title;

    /** @ORM\Column(type="text", name="content") */
    protected $content;

    /** @ORM\Column(type="text", name="replacements") */
    protected $replacements;

    public function getId()
    {
        return $this->id;
    }

    public function setContent($content)
    {
        $this->content = $content;
    }

    public function getContent()
    {
        return $this->content;
    }

    public function setTitle($title)
    {
        $this->title = $title;
    }

    public function getTitle()
    {
        return $this->title;
    }

    public function setReplacements($replacements)
    {
        $this->replacements = $replacements;
    }

    public function getReplacements()
    {
        return $this->replacements;
    }
}