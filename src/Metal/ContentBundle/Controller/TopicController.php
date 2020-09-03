<?php

namespace Metal\ContentBundle\Controller;

use Metal\ContentBundle\Entity\Category;
use Metal\ContentBundle\Entity\Topic;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\ParamConverter;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;

class TopicController extends Controller
{
    /**
     * @ParamConverter("topic", class="MetalContentBundle:Topic", options={"repository_method"="loadContentEntry"})
     */
    public function viewAction(Topic $topic, Category $content_category)
    {
        if (!in_array($content_category->getId(), $topic->getCategoriesIds())) {
            $url = $this->generateUrl(
                'MetalContentBundle:Topic:view',
                array(
                    'id' => $topic->getId(),
                    'category_slug' => $topic->getCategory()->getSlugCombined()
                )
            );

            return $this->redirect($url, 301);
        }

        return $this->render(
            '@MetalContent/Topic/view.html.twig',
            array(
                'topic' => $topic
            )
        );
    }
}
