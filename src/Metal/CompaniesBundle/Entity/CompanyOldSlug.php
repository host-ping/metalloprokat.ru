<?php

namespace Metal\CompaniesBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity(repositoryClass="Metal\CompaniesBundle\Repository\CompanyOldSlugRepository")
 * @ORM\Table(
 *      name="company_old_slug",
 *      uniqueConstraints={
 *          @ORM\UniqueConstraint(name="UNIQ_old_slug", columns={"old_slug"})
 *      }
 * )
 */
class CompanyOldSlug
{
    /**
     * @ORM\Id
     * @ORM\OneToOne(targetEntity="Company")
     * @ORM\JoinColumn(name="company_id", referencedColumnName="Message_ID", onDelete="CASCADE")
     *
     * @var Company
     */
    protected $company;

    /**
     * @ORM\Column(length=255, name="old_slug", nullable=true)
     */
    protected $oldSlug;

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

    public function setOldSlug($oldSlug)
    {
        $this->oldSlug = $oldSlug;
    }

    public function getOldSlug()
    {
        return $this->oldSlug;
    }
}
