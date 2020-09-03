<?php

namespace Metal\PrivateOfficeBundle\Controller;

use Doctrine\ORM\EntityManager;

use Metal\CompaniesBundle\Entity\Company;
use Metal\CompaniesBundle\Entity\CompanyFile;
use Metal\CompaniesBundle\Form\CompanyFileEditType;
use Metal\CompaniesBundle\Form\CompanyFileType;
use Metal\PrivateOfficeBundle\Helper\SerializerHelper;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;

use Sensio\Bundle\FrameworkExtraBundle\Configuration\Security;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\ParamConverter;

class PrivateDocumentsManagementController extends Controller
{
    /**
     * @Security("has_role('ROLE_SUPPLIER') and user.getHasEditPermission() and has_role('ROLE_CONFIRMED_EMAIL') and (not request.isMethod('POST') or user.getCompany().getPackageChecker().isDocumentsAllowed())")
     */
    public function listAction(Request $request)
    {
        $em = $this->getDoctrine()->getManager();
        /* @var $em EntityManager */

        $company = $this->getUser()->getCompany();
        /* @var $company Company */

        $documents = $em->getRepository('MetalCompaniesBundle:CompanyFile')->findBy(
            array(
                'company' => $company
            )
        );

        $serializerHelper = $this->get('brouzie.helper_factory')->get('MetalPrivateOfficeBundle:Serializer');
        /* @var $serializerHelper SerializerHelper */

        $documentsArray = array();
        foreach ($documents as $document) {
            $documentsArray[] = $serializerHelper->serializeCompanyDocument($document);
        }

        $companyFile = new CompanyFile();
        $form = $this->createForm(new CompanyFileType(), $companyFile);

        $editForm = $this->createForm(new CompanyFileEditType());

        if ($request->isMethod('POST')) {
            $form->handleRequest($request);
            if (!$form->isValid()) {
                $errors = $this->get('metal.project.form_helper')->getFormErrorMessages($form);

                return JsonResponse::create(
                    array(
                        'errors' => $errors,
                    )
                );
            }

            $companyFile->setCompany($company);
            $em->persist($companyFile);
            $em->flush();

            return JsonResponse::create(
                array(
                    'status' => 'success',
                    'document' => $serializerHelper->serializeCompanyDocument($companyFile)
                )
            );
        }

        return $this->render('MetalPrivateOfficeBundle:PrivateDocuments:documents.html.twig', array(
            'documents' => $documentsArray,
            'form' => $form->createView(),
            'editForm' => $editForm->createView(),
            'company' => $company,
        ));
    }

    /**
     * @ParamConverter("companyFile", class="MetalCompaniesBundle:CompanyFile")
     * @Security("has_role('ROLE_SUPPLIER') and user.getHasEditPermission() and has_role('ROLE_CONFIRMED_EMAIL') and user.getCompany().getId() == companyFile.getCompany().getId() and user.getCompany().getPackageChecker().isDocumentsAllowed()")
     */
    public function deleteAction(Request $request, CompanyFile $companyFile)
    {
        $em = $this->getDoctrine()->getManager();
        /* @var $em EntityManager */

        $em->remove($companyFile);
        $em->flush();

        return JsonResponse::create( array(
            'status' => 'success',
        ));
    }

    /**
     * @ParamConverter("companyFile", class="MetalCompaniesBundle:CompanyFile")
     * @Security("has_role('ROLE_SUPPLIER') and user.getHasEditPermission() and has_role('ROLE_CONFIRMED_EMAIL') and user.getCompany().getId() == companyFile.getCompany().getId() and user.getCompany().getPackageChecker().isDocumentsAllowed()")
     */
    public function saveAction(Request $request, CompanyFile $companyFile)
    {
        $em = $this->getDoctrine()->getManager();
        /* @var $em EntityManager */

        $editForm = $this->createForm(new CompanyFileEditType(), $companyFile);
        $editForm->handleRequest($request);

        if (!$editForm->isValid()) {
            $errors = $this->get('metal.project.form_helper')->getFormErrorMessages($editForm);

            return JsonResponse::create(
                array(
                    'errors' => $errors,
                )
            );
        }

        $companyFile->setUpdatedAt(new \DateTime());
        $em->flush();

        $serializerHelper = $this->get('brouzie.helper_factory')->get('MetalPrivateOfficeBundle:Serializer');
        /* @var $serializerHelper SerializerHelper */

        return JsonResponse::create(
            array(
                'status' => 'success',
                'document' => $serializerHelper->serializeCompanyDocument($companyFile)
            )
        );
    }
}
