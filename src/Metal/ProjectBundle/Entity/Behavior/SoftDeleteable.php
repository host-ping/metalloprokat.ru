<?php

namespace Metal\ProjectBundle\Entity\Behavior;

use Doctrine\ORM\Mapping as ORM;

trait SoftDeleteable
{
    /**
     * @ORM\Column(type="datetime", name="deleted_at", nullable=true)
     *
     * @var \DateTime|null
     */
    protected $deletedAt;

    /**
     * @param \DateTime|null $deletedAt
     */
    public function setDeletedAt(\DateTime $deletedAt = null)
    {
        $this->deletedAt = $deletedAt;
    }

    /**
     * @return \DateTime|null
     */
    public function getDeletedAt()
    {
        return $this->deletedAt;
    }

    public function setDeleted($deletedAt = true)
    {
        $this->deletedAt = $deletedAt ? new \DateTime() : null;
    }

    public function isDeleted()
    {
        return $this->deletedAt !== null;
    }
}
