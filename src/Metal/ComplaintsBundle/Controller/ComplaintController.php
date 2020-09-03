<?php

namespace Metal\ComplaintsBundle\Controller;

use Metal\CategoriesBundle\Entity\Category;
use Metal\ComplaintsBundle\Entity\AbstractComplaint;
use Metal\ComplaintsBundle\Form\ComplaintType;
use Metal\TerritorialBundle\Entity\City;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;

class ComplaintController extends Controller
{
    public function saveComplaintAction(Request $request, $id, $kind, City $city = null)
    {
        $em = $this->getDoctrine()->getManager();
        $categoryId = null;

        if ($kind == 'demand') {
            $object = $em->getRepository('MetalDemandsBundle:AbstractDemand')->find($id);
            if (!$city) {
                $city = $object->getCity();
            }
        } elseif($kind == 'product') {
            $object = $em->getRepository('MetalProductsBundle:Product')->find($id);
            if (!$city) {
                $city = $object->getCompany()->getCity();
            }
        } elseif($kind == 'company') {
            $object = $em->getRepository('MetalCompaniesBundle:Company')->find($id);
            $categoryId = $request->query->get('category');

            if (!$city) {
                $city = $object->getCity();
            }
        } else {
            throw $this->createNotFoundException('Wrong kind');
        }

        if (!$object) {
            throw $this->createNotFoundException('Object not found');
        }

        $user = null;
        if ($this->isGranted('ROLE_USER')) {
            $user = $this->getUser();
        }

        $complaint = AbstractComplaint::factory($kind);
        $form = $this->createForm(new ComplaintType(), $complaint, array('kind' => $kind));
        $form->handleRequest($request);

        if (!$form->isValid()) {
            $errors = $this->get('metal.project.form_helper')->getFormErrorMessages($form);

            return JsonResponse::create(array(
                    'errors' => $errors,
                ));
        }

        if ($user) {
            $complaint->setUser($user);
        }
        $complaint->setObject($object);
        if ($kind === 'product') {
            $complaint->setCategory($object->getCategory());
        } elseif ($kind === 'demand') {
            if ($object->getCategory()) {
                $complaint->setCategory($object->getCategory());
            }
        } elseif ($kind === 'company') {
            if ($categoryId) {
               $category = $em->getRepository('MetalCategoriesBundle:Category')->find($categoryId);
               $complaint->setCategory($category);
            }
        }
        $complaint->setIp($request->getClientIp());
        $complaint->setUserAgent($request->headers->get('USER_AGENT'));
        $complaint->setReferer($request->headers->get('REFERER'));
        $complaint->setCity($city);

        $em->persist($complaint);
        $em->flush();

        if ($complaint->getCompany()) {
            $em->getRepository('MetalCompaniesBundle:CompanyCounter')->changeCounter($complaint->getCompany(), array('newComplaintsCount'), true);
        }

        return JsonResponse::create(array(
            'status' => 'success',
        ));
    }
}
