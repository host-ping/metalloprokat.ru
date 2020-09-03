<?php

namespace Metal\ProjectBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;

class RedirectController extends Controller
{
    public function redirectAction(Request $request)
    {
        $redirectTo = $request->query->get('url');
        if (!$redirectTo) {
            throw new NotFoundHttpException('Wrong parameter');
        }

        return new RedirectResponse($redirectTo, 301);

    }
}
