<?php

namespace Metal\ContentBundle\Controller;

use Metal\ContentBundle\DataFetching\Spec\ContentEntryFilteringSpec;
use Metal\ContentBundle\DataFetching\Spec\ContentEntryLoadingSpec;
use Metal\ContentBundle\DataFetching\Spec\ContentEntryOrderingSpec;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;

class SearchController extends Controller
{
    public function searchAction(Request $request)
    {
        $page = $request->query->get('page', 1);
        $perPage = 10;

        $specification = ContentEntryFilteringSpec::createFromRequest($request);

        $loaderOpts = new ContentEntryLoadingSpec();
        $orderBy = new ContentEntryOrderingSpec();

        if (!$orderBy->applyFromRequest($request)) {
            $orderBy->relevancy();
        }

        $contentDataFetcher = $this->get('metal.content.data_fetcher');
        $contentEntityLoader = $this->get('metal.content.entity_loader');

        $pagerfanta = $contentDataFetcher->getPagerfantaByCriteria($specification, $orderBy, $perPage, $page);
        $pagerfanta = $contentEntityLoader->getPagerfantaWithEntities($pagerfanta, $loaderOpts);

        //TODO: highlight results using call snippets('Говядина тушеная в/с 325г. Халяль из говядины', 'products', 'говядина');

        if ($request->isXmlHttpRequest()) {
            return $this->render(
                '@MetalContent/partial/list_content_entries.html.twig',
                array(
                    'pagerfanta' => $pagerfanta,
                    'showEntityType' => true
                )
            );
        }

        //FIXME: рендерить какой-то другой лейаут
        return $this->render(
            '@MetalContent/Search/list.html.twig',
            array(
                'pagerfanta' => $pagerfanta,
                'showEntityType' => true
            )
        );
    }
}
