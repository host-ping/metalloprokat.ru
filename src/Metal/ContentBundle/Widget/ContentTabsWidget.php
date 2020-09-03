<?php

namespace Metal\ContentBundle\Widget;

use Brouzie\Bundle\WidgetsBundle\Widget\WidgetAbstract;
use Metal\ContentBundle\DataFetching\Spec\ContentEntryFilteringSpec;
use Metal\ContentBundle\DataFetching\Spec\ContentEntryLoadingSpec;
use Metal\ContentBundle\DataFetching\Spec\ContentEntryOrderingSpec;
use Metal\ContentBundle\Entity\AbstractContentEntry;
use Metal\ContentBundle\Entity\Question;

class ContentTabsWidget extends WidgetAbstract
{
    protected function setDefaultOptions()
    {
        parent::setDefaultOptions();

        $this->optionsResolver
            ->setRequired(array('content_entry'))
            ->setAllowedTypes('content_entry', array(AbstractContentEntry::class))
            ->setDefaults(
                array(
                    'active_tab' => null,
                    'available_tabs' => array('comments-tab', 'similar-topics-tab'),
                )
            );
    }

    protected function getParametersToRender()
    {
        $comments = $this->getComments();
        $similarEntries = $this->getSimilarEntries();

        if (!empty($comments)) {
            $this->options['active_tab'] = 'comments-tab';
        } elseif (!empty($similarEntries)) {
            $this->options['active_tab'] = 'similar-topics-tab';
        }

        return array(
            'comments' => $comments,
            'similarEntries' => $similarEntries,
        );
    }

    public function getComments()
    {
        $contentEntry = $this->options['content_entry'];
        /* @var $contentEntry AbstractContentEntry */

        $em = $this->container->get('doctrine.orm.default_entity_manager');

        return $em->getRepository('MetalContentBundle:Comment')->getCommentsByObject($contentEntry);
    }

    public function getSimilarEntries()
    {
        $contentEntry = $this->options['content_entry'];
        /* @var $contentEntry AbstractContentEntry */

        $limit = 10;

        $entryType = AbstractContentEntry::ENTRY_TYPE_TOPIC;
        if ($contentEntry instanceof Question) {
            $entryType = AbstractContentEntry::ENTRY_TYPE_QUESTION;
        }

        $specification = ContentEntryFilteringSpec::createFromRequest($this->getRequest())
            ->subjectTypeId($contentEntry->getSubjectTypeId())
            ->entryType($entryType)
            ->exceptEntryId($contentEntry->getContentEntryId());

        if ($tags = $contentEntry->getAttribute('content_tags')) {
            $specification->tags($tags);
        }

        $loaderOpts = new ContentEntryLoadingSpec();
        $orderBy = (new ContentEntryOrderingSpec())
            ->random($contentEntry->getContentEntryId());

        $contentDataFetcher = $this->container->get('metal.content.data_fetcher');
        $contentEntityLoader = $this->container->get('metal.content.entity_loader');

        $pagerfanta = $contentDataFetcher->getPagerfantaByCriteria($specification, $orderBy, $limit);
        $pagerfanta = $contentEntityLoader->getPagerfantaWithEntities($pagerfanta, $loaderOpts);

        return $pagerfanta->getCurrentPageResults();
    }
}
