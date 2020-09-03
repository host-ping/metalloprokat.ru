<?php

namespace Metal\CompaniesBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity
 * @ORM\Table(name="normalized_company_url",
 *  indexes={
 *      @ORM\Index(name="IDX_url_as_string", columns={"url_as_string"})
 *  }
 * )
 */
class NormalizedCompanyUrl
{
    /**
     * @ORM\Id
     * @ORM\GeneratedValue
     * @ORM\Column(type="integer", name="id")
     */
    protected $id;

    /**
     * @ORM\Column(length=255, name="url_as_string", nullable=false, options={"default": ""})
     */
    protected $urlAsString;

    /**
     * @ORM\ManyToOne(targetEntity="Metal\CompaniesBundle\Entity\Company")
     * @ORM\JoinColumn(name="company_id", referencedColumnName="Message_ID", onDelete="CASCADE")
     *
     * @var Company
     */
    protected $company;

    public function __construct()
    {
        $this->urlAsString = '';
    }

    public function getId()
    {
        return $this->id;
    }

    public function setId($id)
    {
        $this->id = $id;
    }

    public function getUrlAsString()
    {
        return $this->urlAsString;
    }

    public function setUrlAsString($urlAsString)
    {
        $this->urlAsString = (string)$urlAsString;
    }

    /**
     * @return Company
     */
    public function getCompany()
    {
        return $this->company;
    }

    /**
     * @param Company $company
     */
    public function setCompany(Company $company)
    {
        $this->company = $company;
    }
}