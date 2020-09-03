<?php

namespace Metal\ContentBundle\Widget;

use Brouzie\Bundle\WidgetsBundle\Widget\WidgetAbstract;

class InstagramPhotosWidget extends WidgetAbstract
{
    public function setDefaultOptions()
    {
        parent::setDefaultOptions();

        $this->optionsResolver
            ->setDefaults(
                array(
                    'limit' => 5,
                )
            );
    }

    protected function getParametersToRender()
    {
        $em = $this->container->get('doctrine.orm.default_entity_manager');

        $photos = $em->getRepository('MetalContentBundle:InstagramPhoto')->createQueryBuilder('p')
            ->select('p')
            ->orderBy('p.createdAt', 'DESC')
            ->setMaxResults($this->options['limit'])
            ->getQuery()
            ->getResult();

        return compact('photos');
    }
}