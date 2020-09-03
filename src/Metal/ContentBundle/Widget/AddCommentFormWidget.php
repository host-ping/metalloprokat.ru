<?php

namespace Metal\ContentBundle\Widget;

use Brouzie\Bundle\WidgetsBundle\Widget\WidgetAbstract;
use Metal\ContentBundle\Entity\Comment;
use Metal\ContentBundle\Form\CommentType;
use Symfony\Component\Validator\Constraint;

class AddCommentFormWidget extends WidgetAbstract
{
    protected function getParametersToRender()
    {
        $user = null;
        if ($this->isGranted('ROLE_USER')) {
            $user = $this->getUser();
        }

        $form = $this->createForm(
            new CommentType(),
            new Comment(),
            array(
                'is_authenticated' => $user !== null,
                'validation_groups' => $user !== null ?
                    array(Constraint::DEFAULT_GROUP, 'authenticated') :
                    array(Constraint::DEFAULT_GROUP, 'anonymous'),
                'user' => $user
            )
        );

        return array(
            'form' => $form->createView(),
            'redirect_url' => $this->getRequest()->getPathInfo()
        );
    }
}
