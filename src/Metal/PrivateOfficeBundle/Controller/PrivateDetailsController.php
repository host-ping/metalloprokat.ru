<?php

namespace Metal\PrivateOfficeBundle\Controller;

use Doctrine\ORM\EntityManager;
use Metal\CompaniesBundle\Entity\Company;
use Metal\CompaniesBundle\Entity\PaymentDetails;
use Metal\CompaniesBundle\Form\PaymentDetailsType;
use Metal\ServicesBundle\Entity\Payment;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\ParamConverter;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Security;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

class PrivateDetailsController extends Controller
{
    /**
     * @Security("has_role('ROLE_SUPPLIER') and user.getHasEditPermission() and (not request.isMethod('POST') or has_role('ROLE_CONFIRMED_EMAIL'))")
     */
    public function editAction(Request $request)
    {
        $em = $this->getDoctrine()->getManager();
        /* @var $em EntityManager */

        $company = $this->getUser()->getCompany();
        /* @var $company Company */
        $paymentDetails = $company->getPaymentDetails();

        $form = $this->createForm(new PaymentDetailsType(), $paymentDetails, array('validation_groups' => 'company_details'));

        if ($request->isMethod('POST')) {

            $form->handleRequest($request);
            if (!$form->isValid()) {
                $errors = $this->get('metal.project.form_helper')->getFormErrorMessages($form);

                return JsonResponse::create(array(
                        'errors' => $errors
                        // set Content-Type header for IE
                    ), 200, array('Content-Type' => 'text/plain'));
            }

            $paymentDetails->setUpdated();
            $em->flush();

            $attachment = null;
            if ($originalFileName = $paymentDetails->getFile()->getOriginalName()) {
                $attachment = array('name' => $originalFileName);
            }

            return JsonResponse::create(
                array(
                    'status' => 'success',
                    'attachment' => $attachment,
                    'updatedAt' => $this->get('brouzie.helper_factory')
                        ->get('MetalProjectBundle:Formatting')
                        ->formatDateTime($paymentDetails->getUpdatedAt())
                ),
                200,
                // set Content-Type header for IE
                array('Content-Type' => 'text/plain')
            );
        }

        return $this->render('MetalPrivateOfficeBundle:PrivateDetails:view.html.twig', array(
                'form' => $form->createView(),
                'paymentDetails' => $paymentDetails,
            ));
    }

    /**
     * @Security("has_role('ROLE_SUPPLIER') and user.getHasEditPermission()")
     */
    public function downloadDocumentAction()
    {
        $company = $this->getUser()->getCompany();
        /* @var $company Company */
        $paymentDetail = $company->getPaymentDetails();
        /* @var $paymentDetail PaymentDetails */

        if (!$paymentDetail->getFile()->getName()) {
            throw $this->createNotFoundException('Файл не загружен');
        }

        return $this->get('vich_uploader.download_handler')
            ->downloadObject($paymentDetail, 'uploadedFile', null, true);
    }

    /**
     * @Security("has_role('ROLE_SUPPLIER') and user.getHasEditPermission()")
     */
    public function paymentAction(Request $request)
    {
        $company = $this->getUser()->getCompany();
        /* @var $company Company */

        $em = $this->getDoctrine()->getManager();
        /* @var $em EntityManager */

        $payments = $em->getRepository('MetalServicesBundle:Payment')->findBy(array('company' => $company, 'deletedAt' => null), array('id' => 'DESC'));

        if ($request->query->get('show_flash')) {
            $this->addFlash('success', 'Вы оплатили пакет услуг');
        }

        return $this->render(
            'MetalPrivateOfficeBundle:PrivateDetails:view_payment.html.twig',
            array('payments' => $payments)
        );
    }

    /**
     * @ParamConverter("payment", class="MetalServicesBundle:Payment")
     * @Security("has_role('ROLE_SUPPLIER') and user.getHasEditPermission() and payment.getCompany().getId() == user.getCompany().getId()")
     */
    public function deletePaymentAction(Payment $payment)
    {
        $em = $this->getDoctrine()->getManager();
        /* @var $em EntityManager */

        if (!$payment->isPayed()) {
            $payment->setDeleted();
            $em->flush($payment);
        }

        return $this->redirectToRoute('MetalPrivateOfficeBundle:Details:payment');
    }

    /**
     * @ParamConverter("payment", class="MetalServicesBundle:Payment")
     * @Security("is_granted('CAN_VIEW_PAYMENT', payment)")
     */
    public function printPaymentAction(Payment $payment)
    {
        return $this->render('MetalPrivateOfficeBundle:PrivateDetails:receipt_sberbank.html.twig', array('payment' => $payment));
    }

    /**
     * @ParamConverter("payment", class="MetalServicesBundle:Payment")
     * @Security("is_granted('CAN_VIEW_PAYMENT', payment)")
     */
    public function downloadPaymentAction(Payment $payment)
    {
        $paymentHtml = $this->renderView('@MetalPrivateOffice/PrivateDetails/receipt_sberbank.html.twig', array('payment' => $payment));

        $dompdf = new \DOMPDF();
        $dompdf->load_html($paymentHtml, 'UTF-8');
        $dompdf->set_paper('A4', 'landscape');
        $dompdf->render();

        return new Response($dompdf->output(),
            200,
            array(
                'Content-Type' => 'application/pdf',
                'Content-Disposition' => sprintf('inline; filename="payment-%d.pdf"', $payment->getId())
            )
        );
    }
}
