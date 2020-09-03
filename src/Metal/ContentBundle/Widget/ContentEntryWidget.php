<?php

namespace Metal\ContentBundle\Widget;

use Brouzie\Bundle\WidgetsBundle\Widget\WidgetAbstract;
use Metal\ContentBundle\DataFetching\Spec\ContentEntryFilteringSpec;
use Metal\ContentBundle\DataFetching\Spec\ContentEntryOrderingSpec;

class ContentEntryWidget extends WidgetAbstract
{
    protected function setDefaultOptions()
    {
        parent::setDefaultOptions();

        $this->optionsResolver
            ->setRequired(array('entry_type'))
            ->setAllowedValues('entry_type', array('ENTRY_TYPE_TOPIC', 'ENTRY_TYPE_QUESTION'))
            ->setDefaults(
                array(
                    'tags' => null,
                    'limit' => 5,
                )
            );
    }

    protected function getParametersToRender()
    {
        $tags = $this->options['tags'];

        $specification = ContentEntryFilteringSpec::createFromRequest($this->getRequest());
        $specification->entryType($this->options['entry_type']);

        $orderBy = new ContentEntryOrderingSpec();
        if ($tags) {
            $orderBy->tagsMatching($tags);
        }
        $orderBy->createdAt();

        $contentDataFetcher = $this->container->get('metal.content.data_fetcher');
        $contentEntityLoader = $this->container->get('metal.content.entity_loader');

        $pagerfanta = $contentDataFetcher->getPagerfantaByCriteria($specification, $orderBy, $this->options['limit']);
        $pagerfanta = $contentEntityLoader->getPagerfantaWithEntities($pagerfanta);

        return compact('pagerfanta');
    }
}
