<?php

namespace Metal\CategoriesBundle\Controller;

use Sonata\AdminBundle\Controller\CRUDController;
use Symfony\Component\HttpFoundation\BinaryFileResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\ResponseHeaderBag;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use Symfony\Component\Security\Core\Exception\AccessDeniedException;
use Symfony\Component\Translation\Exception\NotFoundResourceException;

class LandingPageAdminController extends CRUDController
{
    public function exportAction(Request $request)
    {
        if (!$this->admin->isGranted('EXPORT')) {
            throw new AccessDeniedException();
        }

        $format = $request->get('format');

        $allowedExportFormats = (array) $this->admin->getExportFormats();

        if (!in_array($format, $allowedExportFormats)) {
            throw new \RuntimeException(
                sprintf(
                    'Export in format `%s` is not allowed for class: `%s`. Allowed formats are: `%s`',
                    $format,
                    $this->admin->getClass(),
                    implode(', ', $allowedExportFormats)
                )
            );
        }


        $dir = $this->container->getParameter('web_dir').'/';
        $fileName = 'landing_page.csv';

        if (!file_exists($dir.$fileName)) {
            throw new NotFoundResourceException(sprintf('File "%s" not found.', $dir.$fileName));
        }

        $response = new BinaryFileResponse($dir.$fileName);
        $response->headers->set('Content-Type', 'text/CSV');

        $response->setContentDisposition(
            ResponseHeaderBag::DISPOSITION_ATTACHMENT,
            $fileName
        );

        return $response;
    }

    public function showResultsCountAction()
    {
        $request = $this->getRequest();
        $id = $request->get($this->admin->getIdParameter());

        $object = $this->admin->getObject($id);

        if (!$object) {
            throw new NotFoundHttpException(sprintf('unable to find the object with id : %s', $id));
        }

        if (false === $this->admin->isGranted('VIEW', $object)) {
            throw new AccessDeniedException();
        }

        $this->admin->setSubject($object);

        $resultsByCities = $this->getDoctrine()->getManager()->getRepository('MetalCategoriesBundle:LandingPageCityCount')
            ->findBy(array('landingPage' => $object), array('city' => 'ASC'));

        $resultsByCountries = $this->getDoctrine()->getManager()->getRepository('MetalCategoriesBundle:LandingPageCountryCount')
            ->findBy(array('landingPage' => $object), array('country' => 'ASC'));

        return $this->render(
            '@MetalCategories/LandingPageAdmin/show_counts.html.twig',
            array(
                'resultsByCities' => $resultsByCities,
                'resultsByCountries' => $resultsByCountries,
                'landingPage' => $object,
                'admin_pool' => $this->container->get('sonata.admin.pool'),
            )
        );
    }
}
