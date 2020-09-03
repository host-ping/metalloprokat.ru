<?php

namespace Metal\AttributesBundle\Entity\DTO;

use Metal\AttributesBundle\Entity\AttributeValue as BaseAttributeValue;

class LightAttributeValue extends BaseAttributeValue
{
    /**
     * @readonly
     * @var int
     */
    public $attributeId;

    public function __construct($id, $attributeId, $value, $valueAccusative, $slug, $urlPriority)
    {
        parent::__construct();

        $this->id = $id;
        $this->attributeId = $attributeId;
        $this->value = $value;
        $this->valueAccusative = $valueAccusative;
        $this->slug = $slug;
        $this->urlPriority = $urlPriority;
    }

    public static function getCreateDQL($attributeValueAlias = 'av', $resultAlias = 'attributeValue')
    {
        $columns = array(
            '{alias}.id',
            'IDENTITY({alias}.attribute)',
            '{alias}.value',
            '{alias}.valueAccusative',
            '{alias}.slug',
            '{alias}.urlPriority',
        );

        return strtr(
            'NEW MetalAttributesBundle:DTO\LightAttributeValue('.implode(', ', $columns).') AS {result}',
            array('{alias}' => $attributeValueAlias, '{result}' => $resultAlias)
        );
    }
}
