<?php

namespace Metal\TerritorialBundle\Controller;

use Doctrine\ORM\EntityManager;
use Metal\TerritorialBundle\Entity\City;
use Metal\TerritorialBundle\Form\CityInlineEditType;
use Sonata\AdminBundle\Controller\CRUDController;
use Symfony\Component\HttpFoundation\JsonResponse;

class CityAdminController extends CRUDController
{
    public function editInlineAction()
    {
        $request = $this->admin->getRequest();

        $em = $this->getDoctrine()->getManager();
        /* @var $em EntityManager */

        $city = $this->admin->getSubject();
        /* @var $city City */

        if (!$city) {
            return JsonResponse::create(array('errors' => 'Город не найден.'));
        }

        $form = $this->container->get('form.factory')->createNamed(
            CityInlineEditType::getNameForCity($city),
            new CityInlineEditType(),
            $city
        );

        $form->handleRequest($request);

        if (!$form->isValid()) {
            $errors = $this->get('metal.project.form_helper')->getFormErrorMessages($form);

            return JsonResponse::create(
                array(
                    'errors' => $errors
                )
            );
        }

        $em->flush();

        return JsonResponse::create(
            array(
                'status' => 'success'
            )
        );
    }
}
