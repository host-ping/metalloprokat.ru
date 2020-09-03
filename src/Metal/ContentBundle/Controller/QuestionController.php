<?php

namespace Metal\ContentBundle\Controller;

use Metal\ContentBundle\Entity\Category;
use Metal\ContentBundle\Entity\Question;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\ParamConverter;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;

class QuestionController extends Controller
{
    /**
     * @ParamConverter("question", class="MetalContentBundle:Question", options={"repository_method"="loadContentEntry"})
     */
    public function viewAction(Question $question, Category $content_category)
    {
        if (!in_array($content_category->getId(), $question->getCategoriesIds())) {
            $url = $this->generateUrl(
                'MetalContentBundle:Question:view',
                array(
                    'id' => $question->getId(),
                    'category_slug' => $question->getCategory()->getSlugCombined()
                )
            );

            return $this->redirect($url, 301);
        }

        return $this->render(
            '@MetalContent/Question/view.html.twig',
            array(
                'question' => $question
            )
        );
    }
}
