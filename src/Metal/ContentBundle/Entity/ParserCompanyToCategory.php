<?php

namespace Metal\ContentBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

use Metal\CompaniesBundle\Entity\Company;

/**
 * @ORM\Entity
 * @ORM\Table(name="parser_company_to_category")
 */
class ParserCompanyToCategory
{
    /**
     * @ORM\Id
     * @ORM\GeneratedValue
     * @ORM\Column(type="integer", name="id")
     */
    protected $id;

    /**
     * @ORM\ManyToOne(targetEntity="Metal\CompaniesBundle\Entity\Company")
     * @ORM\JoinColumn(name="company_id", referencedColumnName="Message_ID")
     *
     * @var Company
     */
    protected $company;

    /**
     * @ORM\ManyToOne(targetEntity="Metal\ContentBundle\Entity\ParserCategoryAssociate")
     * @ORM\JoinColumn(name="parsed_category_id", referencedColumnName="parser_category_id", nullable=false, onDelete="CASCADE")
     *
     * @var ParserCategoryAssociate
     */
    protected $parsedCategory;

    /** 
     * @ORM\Column(type="boolean", name="matched", options={"default":0}) 
     */
    protected $matched;

    public function __construct()
    {
        $this->matched = false;
    }
    
    public function getId()
    {
        return $this->id;
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

    /**
     * @return ParserCategoryAssociate
     */
    public function getParsedCategory()
    {
        return $this->parsedCategory;
    }

    /**
     * @param ParserCategoryAssociate $parsedCategory
     */
    public function setParsedCategory(ParserCategoryAssociate $parsedCategory)
    {
        $this->parsedCategory = $parsedCategory;
    }

    public function getMatched()
    {
        return $this->matched;
    }

    public function setMatched($matched)
    {
        $this->matched = $matched;
    }
}