<?php

namespace Metal\ContentBundle\Controller;

use Metal\ContentBundle\DataFetching\Spec\ContentEntryFilteringSpec;
use Metal\ContentBundle\DataFetching\Spec\ContentEntryLoadingSpec;
use Metal\ContentBundle\DataFetching\Spec\ContentEntryOrderingSpec;
use Metal\ContentBundle\Entity\AbstractContentEntry;
use Metal\ContentBundle\Entity\Category;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;

class TopicsController extends Controller
{
    public function listAction(Request $request, Category $content_category = null)
    {
        $page = $request->query->get('page', 1);
        $perPage = 10;

        $specification = ContentEntryFilteringSpec::createFromRequest($request)
            ->entryType(AbstractContentEntry::ENTRY_TYPE_TOPIC);

        $loaderOpts = new ContentEntryLoadingSpec();
        $orderBy = new ContentEntryOrderingSpec();

        if (!$orderBy->applyFromRequest($request)) {
            $orderBy->createdAt();
        }

        $contentDataFetcher = $this->get('metal.content.data_fetcher');
        $contentEntityLoader = $this->get('metal.content.entity_loader');

        $pagerfanta = $contentDataFetcher->getPagerfantaByCriteria($specification, $orderBy, $perPage, $page);
        $pagerfanta = $contentEntityLoader->getPagerfantaWithEntities($pagerfanta, $loaderOpts);


        if ($request->isXmlHttpRequest()) {
            return $this->render(
                '@MetalContent/partial/list_content_entries.html.twig',
                array(
                    'pagerfanta' => $pagerfanta,
                )
            );
        }

        return $this->render(
            '@MetalContent/Topics/list.html.twig',
            array(
                'pagerfanta' => $pagerfanta,
                'category' => $content_category,
            )
        );
    }
}
