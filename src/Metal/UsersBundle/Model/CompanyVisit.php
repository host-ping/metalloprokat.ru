<?php

namespace Metal\UsersBundle\Model;

class CompanyVisit
{
    private $companyId;

    private $visitedAt;

    public function __construct(int $companyId, \DateTime $visitedAt)
    {
        $this->companyId = $companyId;
        $this->visitedAt = $visitedAt;
    }

    public function getCompanyId(): int
    {
        return $this->companyId;
    }

    public function getVisitedAt(): \DateTime
    {
        return $this->visitedAt;
    }
}
