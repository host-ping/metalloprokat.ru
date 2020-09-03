<?php

namespace Metal\PrivateOfficeBundle\Controller;

use Doctrine\ORM\EntityManager;
use Metal\CompaniesBundle\Entity\CompanyReview;
use Metal\CompaniesBundle\Entity\ReviewAnswer;
use Metal\CompaniesBundle\Form\ReviewAnswerType;
use Metal\PrivateOfficeBundle\Helper\SerializerHelper;
use Metal\UsersBundle\Entity\User;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;

use Sensio\Bundle\FrameworkExtraBundle\Configuration\Security;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\ParamConverter;

class PrivateReviewsController extends Controller
{
    /**
     * @Security("has_role('ROLE_SUPPLIER')")
     */
    public function listAction(Request $request)
    {
        $notAnswered = $request->query->get('notAnswered');
        $company = $this->getUser()->getCompany();

        $perPage = 20;
        $pagerfanta = $this->getDoctrine()->getRepository('MetalCompaniesBundle:CompanyReview')
            ->getPagerfantaForCompanyReview(array('company' => $company, 'not_answered' => $notAnswered, 'with_no_moderated' => true), array('created_at' => 'DESC'), $perPage, $request->query->get('page', 1));

        $reviews = iterator_to_array($pagerfanta->getIterator());

        $serializerHelper = $this->get('brouzie.helper_factory')->get('MetalPrivateOfficeBundle:Serializer');
        /* @var $serializerHelper SerializerHelper */

        $reviewsArray = array();
        foreach ($reviews as $review) {
            $reviewsArray[] = $serializerHelper->serializeReview($review);
        }

        if ($request->isXmlHttpRequest()) {
            return JsonResponse::create(
                array(
                    'reviews' => $reviewsArray,
                    'company' => $company,
                    'hasMore' => $pagerfanta->hasNextPage()
                )
            );
        }

        return $this->render(
            'MetalPrivateOfficeBundle:PrivateReviews:list.html.twig',
            array(
                'reviews' => $reviewsArray,
                'company' => $company,
                'hasMore' => $pagerfanta->hasNextPage()
            )
        );
    }

    /**
     * @ParamConverter("companyReview", class="MetalCompaniesBundle:CompanyReview")
     * @Security("has_role('ROLE_SUPPLIER') and has_role('ROLE_CONFIRMED_EMAIL') and companyReview.getCompany().getId() == user.getCompany().getId()")
     */
    public function deleteAction(CompanyReview $companyReview)
    {
        $em = $this->getDoctrine()->getManager();
        /* @var $em EntityManager */

        $user = $this->getUser();
        /* @var $user User */
        $reviewRepository = $this->getDoctrine()->getManager()->getRepository('MetalCompaniesBundle:CompanyReview');

        $companyReview->setDeletedAt(new \DateTime());
        $companyReview->setDeletedBy($this->getUser());
        $em->flush();
        $company = $companyReview->getCompany();

        $em->getRepository('MetalCompaniesBundle:CompanyCounter')->updateReviewsCount(array($company->getId()));
        $reviewRepository->setReviewsModerated(array($companyReview), $user);

        return JsonResponse::create( array(
            'status' => "success"
        ));
    }

    /**
     * @ParamConverter("companyReview", class="MetalCompaniesBundle:CompanyReview")
     * @Security("has_role('ROLE_SUPPLIER') and has_role('ROLE_CONFIRMED_EMAIL') and companyReview.getCompany().getId() == user.getCompany().getId()")
     */
    public function reviewAnswerAction(Request $request, CompanyReview $companyReview)
    {
        $user = $this->getUser();
        /* @var $user User */

        $reviewAnswer = $companyReview->getAnswer();
        if (!$reviewAnswer) {
            $reviewAnswer = new ReviewAnswer();
        }

        $form = $this->createForm(new ReviewAnswerType(), $reviewAnswer);
        $form->handleRequest($request);

        if (!$form->isValid()) {
            $errors = $this->get('metal.project.form_helper')->getFormErrorMessages($form);

            return JsonResponse::create(array(
                    'errors' => $errors,
                ));
        }

        $reviewAnswer->setCreatedAt(new \DateTime());
        $reviewAnswer->setUser($user);

        $companyReview->setAnswer($reviewAnswer);

        $em = $this->getDoctrine()->getManager();
        $em->persist($reviewAnswer);
        $em->flush();

        $this->get('metal.companies.company_review_mailer')->notifyOfCompanyReviewAnswer($companyReview);

        $serializerHelper = $this->get('brouzie.helper_factory')->get('MetalPrivateOfficeBundle:Serializer');
        /* @var $serializerHelper SerializerHelper */

        $review = $em->getRepository('MetalCompaniesBundle:CompanyReview')->findOneBy(array('answer' => $reviewAnswer->getId()));

        $answeredReview = $serializerHelper->serializeReview($review);

        return JsonResponse::create(array(
                'status' => 'success',
                'answeredReview' => $answeredReview
            ));
    }

    /**
     * @ParamConverter("companyReview", class="MetalCompaniesBundle:CompanyReview")
     * @Security("has_role('ROLE_SUPPLIER') and has_role('ROLE_CONFIRMED_EMAIL') and companyReview.getCompany().getId() == user.getCompany().getId()")
     */
    public function moderateReviewAction(Request $request, CompanyReview $companyReview)
    {
        $em = $this->getDoctrine()->getManager();

        $user = $this->getUser();
        $reviewRepository = $this->getDoctrine()->getManager()->getRepository('MetalCompaniesBundle:CompanyReview');

        $companyReview->setModeratedAt(new \DateTime());
        $companyReview->setModeratedBy($this->getUser());

        $em->flush();

        $em->getRepository('MetalCompaniesBundle:CompanyCounter')->changeCounter($companyReview->getCompany(), array('reviewsCount'), true);
        $reviewRepository->setReviewsModerated(array($companyReview), $user);

        return JsonResponse::create(array(
                'status' => 'success',
            ));
    }
}
