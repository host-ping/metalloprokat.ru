<?php

namespace Metal\PrivateOfficeBundle\Controller;

use Doctrine\ORM\EntityManager;
use Metal\ServicesBundle\Entity\AgreementTemplate;
use Metal\UsersBundle\Entity\User;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Security;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Response;

class PrivateAgreementController extends Controller
{
    /**
     * @Security("has_role('ROLE_SUPPLIER')")
     */
    public function showAction()
    {
        $em = $this->getDoctrine()->getManager();
        /* @var $em EntityManager */

        $user = $this->getUser();
        /* @var $user User */

        $company = $user->getCompany();

        $arrayMatch = $this->get('brouzie.helper_factory')->get('MetalServicesBundle:Agreement')->getMatches($company);

        $agreementTemplate = $em->getRepository('MetalServicesBundle:AgreementTemplate')->find(AgreementTemplate::DEFAULT_AGREEMENT_ID);

        $twigTemplate = $this->get('twig')->createTemplate($agreementTemplate->getContent());

        $agreementHtml = $twigTemplate->render(array(
            'company' => $arrayMatch['company'],
            'paymentDetails' => $arrayMatch['paymentDetails'],
        ));

        return $this->render('MetalPrivateOfficeBundle:AgreementTemplate:show.html.twig', array(
            'agreementHtml' => $agreementHtml,
            'company' => $company,
            'paymentDetails' => $company->getPaymentDetails(),
        ));
    }

    /**
     * @Security("has_role('ROLE_SUPPLIER')")
     */
    public function downloadPdfAction()
    {
        $em = $this->getDoctrine()->getManager();
        /* @var $em EntityManager */

        $user = $this->getUser();
        /* @var $user User */

        $company = $user->getCompany();

        $arrayMatch = $this->get('brouzie.helper_factory')->get('MetalServicesBundle:Agreement')->getMatches($company);

        $agreementTemplate = $em->getRepository('MetalServicesBundle:AgreementTemplate')->find(AgreementTemplate::DEFAULT_AGREEMENT_ID);
        $twigTemplate = $this->get('twig')->createTemplate($agreementTemplate->getContent());

        $agreementHtml = $twigTemplate->render(array(
            'company' => $arrayMatch['company'],
            'paymentDetails' => $arrayMatch['paymentDetails'],
        ));

        $agreementHtmlTemplate = <<<HTML
<!DOCTYPE html PUBLIC "-//W3C//DTD HTML 4.01 Transitional//EN" "http://www.w3.org/TR/html4/loose.dtd">
<html>
<head>
    <meta http-equiv="Content-Type" content="text/html; charset=utf-8"/>
    <style type="text/css">* { font-family: "DejaVu Sans", sans-serif; }</style>
</head>
<body>
    %s
</body>
</html>
HTML;
        $agreementHtml = sprintf($agreementHtmlTemplate, $agreementHtml);

        $dompdf = new \DOMPDF();
        $dompdf->load_html($agreementHtml, 'UTF-8');
        $dompdf->set_paper('A4');
        $dompdf->render();
        $agreementPdf = $dompdf->output();

        return new Response($agreementPdf,
            200,
            array(
                'Content-Type' => 'application/pdf',
                'Content-Disposition' => 'inline; filename="out.pdf"'
            )
        );
    }
}
