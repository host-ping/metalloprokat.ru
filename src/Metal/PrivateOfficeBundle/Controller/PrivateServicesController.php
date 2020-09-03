<?php

namespace Metal\PrivateOfficeBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;

class PrivateServicesController extends Controller
{
    public function editAction(Request $request)
    {
        return $this->render('MetalPrivateOfficeBundle:PrivateServices:view.html.twig');
    }
}
