<?php

namespace Metal\ProductsBundle\Indexer\Operation;

use Brouzie\Components\Indexer\Operation\Criteria;

class ProductsCriteria implements Criteria
{
    private $ids = [];

    private $companyId;

    public function getIds(): array
    {
        return $this->ids;
    }

    public function addId(int $id): void
    {
        $this->ids[] = $id;
    }

    public function setIds(array $ids): void
    {
        $this->ids = [];
        foreach ($ids as $id) {
            $this->addId($id);
        }
    }

    public function getCompanyId(): ?int
    {
        return $this->companyId;
    }

    public function setCompanyId(int $companyId): void
    {
        $this->companyId = $companyId;
    }

    public function serialize()
    {
        return serialize([$this->ids, $this->companyId]);
    }

    public function unserialize($serialized)
    {
        list($this->ids, $this->companyId) = unserialize($serialized, ['allowed_classes' => false]);
    }
}
