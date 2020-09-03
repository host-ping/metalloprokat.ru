<?php

namespace Metal\StatisticBundle\DataFetching;

class UpdateStatsSpec
{
    /**
     * @var array
     */
    public $companiesIds = array();

    /**
     * @var \DateTime|null
     */
    public $dateStart;

    /**
     * @var \DateTime|null
     */
    public $dateFinish;

    /**
     * @var \DateTime
     */
    public $dateFrom;

    /**
     * @var \DateTime
     */
    public $dateTo;

    /**
     * @param array $companiesIds
     *
     * @return $this
     */
    public function setCompaniesIds(array $companiesIds)
    {
        $this->companiesIds = $companiesIds;

        return $this;
    }

    /**
     * @param \DateTime|null $dateStart
     *
     * @return $this
     */
    public function setDateStart(\DateTime $dateStart = null)
    {
        if (null !== $this->dateStart) {
            return $this;
        }

        $this->dateStart = $dateStart;

        if ($this->dateStart instanceof \DateTime) {
            $this->dateFrom = clone $dateStart;
            $this->dateTo = clone $dateStart;
        }

        return $this;
    }

    /**
     * @param \DateTime|null $dateFinish
     *
     * @return $this
     */
    public function setDateFinish(\DateTime $dateFinish)
    {
        $this->dateFinish = $dateFinish;

        return $this;
    }

    /**
     * @param \DateTime|null $dateFrom
     *
     * @return $this
     */
    public function setDateFrom(\DateTime $dateFrom)
    {
        $this->dateFrom = $dateFrom;

        return $this;
    }

    /**
     * @param \DateTime $dateTo
     *
     * @return $this
     */
    public function setDateTo(\DateTime $dateTo)
    {
        $this->dateTo = $dateTo;

        return $this;
    }

    /**
     * @return bool
     */
    public function nexDay()
    {
        if (!$this->dateTo instanceof \DateTime || !$this->dateFrom instanceof \DateTime) {
            throw new \RuntimeException('dateFrom and dateTo must be instanceof \DateTime');
        }

        $this->dateFrom->modify('+1 days')->modify('midnight');
        $this->dateTo->modify('+1 days')->modify('midnight');

        return $this->dateTo <= $this->dateFinish;
    }

    /**
     * @return bool
     */
    public function isReset()
    {
        return null === $this->dateStart;
    }
}
