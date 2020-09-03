<?php

namespace Metal\CompaniesBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use Metal\CategoriesBundle\Entity\CategoryAbstract;
use Metal\ProjectBundle\Helper\TextHelperStatic;
use Symfony\Component\Validator\Constraints as Assert;

use Gedmo\Mapping\Annotation as Gedmo;

/**
 * @ORM\Entity(repositoryClass="Metal\CompaniesBundle\Repository\CustomCompanyCategoryRepository")
 * @ORM\Table(name="custom_company_category")
 *
 * @Gedmo\Tree(type="closure")
 * @Gedmo\TreeClosure(class="Metal\CompaniesBundle\Entity\CustomCategoryClosure")
 */
class CustomCompanyCategory extends CategoryAbstract
{
    const DEFAULT_DISPLAY_POSITION = 100;

    /**
     * @ORM\Id
     * @ORM\GeneratedValue
     * @ORM\Column(type="integer", name="id")
     */
    protected $id;

    /**
     * @Gedmo\TreeParent
     * @ORM\ManyToOne(targetEntity="CustomCompanyCategory", cascade={"persist"})
     * @ORM\JoinColumn(name="parent_id", referencedColumnName="id", nullable=true, onDelete="CASCADE")
     *
     * @var CustomCompanyCategory
     */
    protected $parent;

    /**
     * @ORM\Column(length=255, name="title")
     * @Assert\NotBlank()
     */
    protected $title;

    //FIXME: скорее всего в родительских классах нужно будет отказаться от priority и везде сделать $displayPosition

    /**
     * @ORM\Column(type="smallint", name="display_position", nullable=false, options={"default":100})
     * @Assert\GreaterThanOrEqual(1)
     * @Assert\Type(type="integer")
     */
    protected $displayPosition;

    /**
     * @ORM\ManyToOne(targetEntity="Company")
     * @ORM\JoinColumn(name="company_id", referencedColumnName="Message_ID", nullable=false, onDelete="CASCADE")
     *
     * @var Company
     */
    protected $company;

    public static function getClosureTableName()
    {
        return 'custom_category_closure';
    }

    public static function getSlugColumnName()
    {
        return 'id';
    }

    public function __construct()
    {
        parent::__construct();

        $this->displayPosition = self::DEFAULT_DISPLAY_POSITION;
    }

    public function getDisplayPosition()
    {
        return $this->displayPosition;
    }

    public function setDisplayPosition($displayPosition)
    {
        $this->displayPosition = $displayPosition;
    }

    /**
     * @param Company $company
     */
    public function setCompany(Company $company)
    {
        $this->company = $company;
    }

    /**
     * @return Company
     */
    public function getCompany()
    {
        return $this->company;
    }

    //TODO: Может перенести вверх по иерархии, отличия только в получении slugCombined ?
    public function getUrl($slugs)
    {
        $slugParts = array();
        $slugParts[] = $this->getSlugCombined();
        if ($slugs && is_string($slugs)) {
            $slugParts[] = $slugs;
        }

        return implode('/', $slugParts);
    }

    public function getSlug()
    {
        return $this->id;
    }

    public function getSlugCombined()
    {
        return $this->id;
    }

    public function getTitleAccusativeForEmbed()
    {
        return TextHelperStatic::normalizeTitleForEmbed($this->getTitle());
    }

    public function getAllowProducts()
    {
        return true;
    }
}
