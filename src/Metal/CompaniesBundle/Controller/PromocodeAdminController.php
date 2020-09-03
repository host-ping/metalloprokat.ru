<?php

namespace Metal\CompaniesBundle\Controller;

use Sonata\AdminBundle\Controller\CRUDController;
use Symfony\Component\HttpFoundation\BinaryFileResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\ResponseHeaderBag;
use Symfony\Component\Security\Core\Exception\AccessDeniedException;

class PromocodeAdminController extends CRUDController
{
    public function exportAction(Request $request)
    {
        if (false === $this->admin->isGranted('EXPORT')) {
            throw new AccessDeniedException();
        }

        set_time_limit(600);

        $format = $request->get('format');

        $allowedExportFormats = (array)$this->admin->getExportFormats();

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

        $qb = $this->admin->getDatagrid()->getQuery()->getQueryBuilder();
        $qb->setMaxResults(null)->setFirstResult(null);

        $qb->select('o.code');

        $results = $qb->getQuery()->getResult();

        $dir = $this->container->getParameter('upload_dir');
        $promocodeDir = $dir.'/promocode-export/';
        $fileName = 'promocodes'.'-'.date('Y-m-d_H-i-s').'.txt';

        if (!is_dir($promocodeDir)) {
            mkdir($promocodeDir);
        }

        foreach ($results as $result) {
            file_put_contents($promocodeDir.$fileName, $result['code']."\n", FILE_APPEND);
        }

        $response = new BinaryFileResponse($promocodeDir.$fileName);
        $response->headers->set('Content-Type', 'text');

        $response->setContentDisposition(
            ResponseHeaderBag::DISPOSITION_ATTACHMENT,
            $fileName
        );

        return $response;
    }
}
