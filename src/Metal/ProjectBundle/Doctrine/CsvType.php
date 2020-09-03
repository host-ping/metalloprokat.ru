<?php

namespace Metal\ProjectBundle\Doctrine;

use Doctrine\DBAL\Types\Type;
use Doctrine\DBAL\Platforms\AbstractPlatform;

class CsvType extends Type
{
    const TYPE = 'csv';
    const DELIMITER = ',';
    const ENCLOSURE = '"';

    /**
     * {@inheritdoc}
     */
    public function getSQLDeclaration(array $fieldDeclaration, AbstractPlatform $platform)
    {
        return $platform->getClobTypeDeclarationSQL($fieldDeclaration);
    }

    /**
     * {@inheritdoc}
     */
    public function convertToDatabaseValue($value, AbstractPlatform $platform)
    {
        if ($value === null || $value === '' || $value === array()) {
            return null;
        }

        $output = fopen('php://output', 'w');
        ob_start();
        fputcsv($output, $value, self::DELIMITER, self::ENCLOSURE);
        $string = ob_get_clean();

        return $string;
    }

    /**
     * {@inheritdoc}
     */
    public function convertToPHPValue($value, AbstractPlatform $platform)
    {
        if ($value === null || $value === '') {
            return array();
        }

        $value = (is_resource($value)) ? stream_get_contents($value) : $value;

        return str_getcsv($value, self::DELIMITER, self::ENCLOSURE);
    }

    /**
     * {@inheritdoc}
     */
    public function getName()
    {
        return self::TYPE;
    }

    /**
     * {@inheritdoc}
     */
    public function requiresSQLCommentHint(AbstractPlatform $platform)
    {
        return true;
    }
}
