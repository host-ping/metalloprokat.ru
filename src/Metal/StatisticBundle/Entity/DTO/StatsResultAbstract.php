<?php

namespace Metal\StatisticBundle\Entity\DTO;

abstract class StatsResultAbstract
{
    /**
     * @var static
     */
    public $previousEntry;

    public $hasData = false;

    public function __construct()
    {
        $this->setData(func_get_args());
    }

    /**
     * @return array
     */
    abstract public function getFields();

    public function getDefaultOrder()
    {
        $fields = $this->getFields();
        reset($fields);

        return key($fields);
    }

    public function getDefaultOrderDirection($order)
    {
        return 'desc';
    }

    public function getDefaultGrouping()
    {
        return null;
    }

    protected function setData(array $data)
    {
        if (array() === $data) {
            return;
        }

        $index = 0;
        foreach ($this->getFields() as $field => $options) {
            $value = $data[$index];
            switch ($options['cast']) {
                case 'int':
                    $value = (int)$value;
                    $this->hasData = $this->hasData || $value > 0;
                    break;

                case 'string':
                    $value = (string)$value;
                    break;

                case 'datetime':
                    $value = $value instanceof \DateTime ? $value : new \DateTime($value);
                    break;

                default:
                    throw new \InvalidArgumentException(sprintf('Unknown cast "%s"', $options['cast']));
            }
            $this->$field = $value;
            $index++;
        }
    }
}
