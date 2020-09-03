<?php

namespace Metal\ContentBundle\Widget;

use Brouzie\Bundle\WidgetsBundle\Widget\WidgetAbstract;
use Metal\ContentBundle\Entity\Question;
use Metal\ContentBundle\Entity\Topic;
use Metal\ContentBundle\Form\ContentEntryType;
use Symfony\Component\Validator\Constraint;

class AddContentEntryFormWidget extends WidgetAbstract
{
    protected function setDefaultOptions()
    {
        parent::setDefaultOptions();

        $this->optionsResolver
            ->setRequired(array('entry_type'))
            ->setAllowedValues('entry_type', array('ENTRY_TYPE_TOPIC', 'ENTRY_TYPE_QUESTION'));
    }

    protected function getParametersToRender()
    {
        $user = null;
        $entry = null;
        $entryType = $this->options['entry_type'];
        if ($this->isGranted('ROLE_USER')) {
            $user = $this->getUser();
        }

        if ($entryType === 'ENTRY_TYPE_TOPIC') {
            $entry = new Topic();
        } elseif ($entryType === 'ENTRY_TYPE_QUESTION') {
            $entry = new Question();
        }

        $form = $this->createForm(
            new ContentEntryType(),
            $entry,
            array(
                'is_authenticated' => $user !== null,
                'validation_groups' => $user !== null ?
                    array(Constraint::DEFAULT_GROUP, 'authenticated') :
                    array(Constraint::DEFAULT_GROUP, 'anonymous'),
            )
        );

        return array(
            'form' => $form->createView(),
        );
    }
}
