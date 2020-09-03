<?php

namespace Metal\ProductsBundle\Indexer\Operation;

use Brouzie\Components\Indexer\Operation\AbstractChangeSet;

class ProductChangeSet extends AbstractChangeSet
{
    public const COMPANY_RATING = 'companyRating';

    public const COMPANY_LAST_VISITED_AT = 'companyLastVisitedAt';

    public const UPDATED_AT = 'updatedAt';

    public const IS_HOT_OFFER = 'isHotOffer';

    public const IS_SPECIAL_OFFER = 'isSpecialOffer';

    public function getIsSpecialOffer(): bool
    {
        return $this->getChange(self::IS_SPECIAL_OFFER);
    }

    public function setIsSpecialOffer(bool $isSpecialOffer): void
    {
        $this->setChange(self::IS_SPECIAL_OFFER, $isSpecialOffer);
    }

    public function getIsHotOffer(): bool
    {
        return $this->getChange(self::IS_HOT_OFFER);
    }

    public function setIsHotOffer(bool $isHotOffer): void
    {
        $this->setChange(self::IS_HOT_OFFER, $isHotOffer);
    }

    public function getUpdatedAt(): \DateTime
    {
        return $this->getChange(self::UPDATED_AT);
    }

    public function setUpdatedAt(\DateTime $updatedAt): void
    {
        $this->setChange(self::UPDATED_AT, $updatedAt);
    }

    public function getCompanyLastVisitedAt(): \DateTime
    {
        return $this->getChange(self::COMPANY_LAST_VISITED_AT);
    }

    public function setCompanyLastVisitedAt(\DateTime $companyLastVisitedAt): void
    {
        $this->setChange(self::COMPANY_LAST_VISITED_AT, $companyLastVisitedAt);
    }

    public function getCompanyRating(): int
    {
        return $this->getChange(self::COMPANY_RATING);
    }

    public function setCompanyRating(int $companyRating): void
    {
        $this->setChange(self::COMPANY_RATING, $companyRating);
    }
}
