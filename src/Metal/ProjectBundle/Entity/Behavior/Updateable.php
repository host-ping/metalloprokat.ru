<?php

namespace Metal\ProjectBundle\Entity\Behavior;

use Doctrine\ORM\Mapping as ORM;

trait Updateable
{
    /**
     * @ORM\Column(type="datetime", name="updated_at", nullable=true)
     *
     * @var \DateTime
     */
    protected $updatedAt;

    /**
     * @return \DateTime|null
     */
    public function getUpdatedAt()
    {
        return $this->updatedAt;
    }

    /**
     * @param \DateTime|null $updatedAt
     */
    public function setUpdatedAt(\DateTime $updatedAt = null)
    {
        $this->updatedAt = $updatedAt;
    }

    public function setUpdated($updated = true)
    {
        $this->setUpdatedAt($updated ? new \DateTime() : null);
    }
}
