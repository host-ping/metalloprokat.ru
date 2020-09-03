<?php

namespace Metal\ContentBundle\Controller;

use Metal\ContentBundle\DataFetching\Spec\ContentEntryFilteringSpec;
use Metal\ContentBundle\DataFetching\Spec\ContentEntryLoadingSpec;
use Metal\ContentBundle\DataFetching\Spec\ContentEntryOrderingSpec;
use Metal\ContentBundle\Entity\Tag;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\ParamConverter;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;

class TagController extends Controller
{
    /**
     * @ParamConverter("tag", class="MetalContentBundle:Tag")
     */
    public function viewAction(Request $request, Tag $tag)
    {
        $page = $request->query->get('page', 1);
        $perPage = 10;

        $specification = ContentEntryFilteringSpec::createFromRequest($request)
            ->tags(array($tag));

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
                    'showEntityType' => true,
                )
            );
        }

        return $this->render(
            '@MetalContent/Tag/view.html.twig',
            array(
                'pagerfanta' => $pagerfanta,
                'tag' => $tag,
                'showEntityType' => true,
            )
        );
    }
}
