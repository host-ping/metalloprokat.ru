<?php

namespace Metal\PrivateOfficeBundle\Controller;

use Doctrine\ORM\EntityManager;

use Metal\CompaniesBundle\Entity\Company;

use Metal\MiniSiteBundle\Form\MiniSiteConfigType;
use Metal\PrivateOfficeBundle\Form\MiniSiteAddressType;
use Metal\PrivateOfficeBundle\Form\MiniSiteColorsType;
use Metal\PrivateOfficeBundle\Form\MiniSiteSaveCoverType;
use Metal\CompaniesBundle\Form\CompanySaveLogoType;
use Metal\ProjectBundle\Helper\UrlHelper;
use Metal\TerritorialBundle\Entity\ValueObject\MinisiteDomainsProvider;
use Metal\UsersBundle\Entity\User;
use Metal\MiniSiteBundle\Entity\MiniSiteCover;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\JsonResponse;

use Sensio\Bundle\FrameworkExtraBundle\Configuration\Security;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\ParamConverter;

class PrivateMiniSiteController extends Controller
{
    /**
     * @Security("has_role('ROLE_SUPPLIER') and user.getHasEditPermission() and (not request.isMethod('POST') or has_role('ROLE_CONFIRMED_EMAIL'))")
     */
    public function viewHeaderAction()
    {
        $user = $this->getUser();
        /* @var $user User */
        $company = $user->getCompany();
        $em = $this->getDoctrine()->getManager();
        $coverRepository = $em->getRepository('MetalMiniSiteBundle:MiniSiteCover');
        $miniSiteCovers = $coverRepository->findBy(array('company' => null));
        /* @var $miniSiteCovers MiniSiteCover[] */

        $transparentCover = null;
        foreach ($miniSiteCovers as $miniSiteCoverNull) {
            if ($miniSiteCoverNull->getId() == 50) {
                $transparentCover = $miniSiteCoverNull;
                unset($miniSiteCoverNull);
            }
        }
        array_unshift($miniSiteCovers, $transparentCover);

        $miniSiteCover = $coverRepository->findOneBy(array('company' => $company->getId()));

        if ($miniSiteCover) {
            array_unshift($miniSiteCovers, $miniSiteCover);
        }

        $miniSiteCovers = array_filter($miniSiteCovers);

        $formCover = $this->createForm(new MiniSiteSaveCoverType());
        $formLogo = $this->createForm(new CompanySaveLogoType(), $company);

        $formattedCovers = array();
        foreach ($miniSiteCovers as $cover) {
            $formattedCovers[] = $this->formatCover($cover);
        }

        $cover = $company->getMinisiteConfig()->getCover();
        $activeCover = array();
        if ($cover) {
            $activeCover = $this->formatCover($cover);
        }

        return $this->render(
            '@MetalPrivateOffice/MiniSite/header.html.twig',
            array(
                'formCover' => $formCover->createView(),
                'formLogo' => $formLogo->createView(),
                'company' => $company,
                'minisiteCovers' => $formattedCovers,
                'activeCover' => $activeCover
            )
        );
    }

    /**
     * @Security("has_role('ROLE_SUPPLIER') and user.getHasEditPermission() and has_role('ROLE_CONFIRMED_EMAIL')")
     */
    public function uploadCoverAction(Request $request)
    {
        $em = $this->getDoctrine()->getManager();
        /* @var $em EntityManager */

        $user = $this->getUser();
        /* @var $user User */
        $company = $user->getCompany();

        $miniSiteCover = $em->getRepository('MetalMiniSiteBundle:MiniSiteCover')->findOneBy(
            array('company' => $company->getId())
        );

        if (!$miniSiteCover) {
            $miniSiteCover = new MiniSiteCover();
        }

        $form = $this->createForm(new MiniSiteSaveCoverType(), $miniSiteCover);

        $form->handleRequest($request);

        if (!$form->isValid()) {
            $errors = $this->get('metal.project.form_helper')->getFormErrorMessages($form);

            return JsonResponse::create(array(
                    'errors' => $errors
                    // set Content-Type header for IE
                ), 200, array('Content-Type' => 'text/plain'));
        }

        $miniSiteCover->setCompany($company);

        $em->persist($miniSiteCover);
        $em->flush();

        return JsonResponse::create(array(
                'status' => 'success',
                'cover' => $this->formatCover($miniSiteCover)
                // set Content-Type header for IE
            ), 200, array('Content-Type' => 'text/plain'));
    }

    /**
     * @ParamConverter("cover", class="MetalMiniSiteBundle:MiniSiteCover", options={"id" = "cover_id"})
     * @Security("has_role('ROLE_SUPPLIER') and user.getHasEditPermission() and has_role('ROLE_CONFIRMED_EMAIL') and (not cover.getCompany() or cover.getCompany().getId() == user.getCompany().getId())")
     */
    public function saveCoverAction(MiniSiteCover $cover)
    {
        $this->getUser()->getCompany()->getMinisiteConfig()->setCover($cover);
        $this->getDoctrine()->getManager()->flush();

        return JsonResponse::create(array(
                'status' => 'success'
            ));
    }

    /**
     * @Security("has_role('ROLE_SUPPLIER') and user.getHasEditPermission() and (not request.isMethod('POST') or has_role('ROLE_CONFIRMED_EMAIL'))")
     */
    public function addressAction(Request $request)
    {
        $user = $this->getUser();
        /* @var $user User */
        $company = $user->getCompany();
        $currentSlug = $company->getSlug();

        $options = array(
            'validation_groups' => 'company_address',
            'minisite_domains' => MinisiteDomainsProvider::getAllDomainsAsSimpleArray($company->getCountry()->getId()),
        );

        $form = $this->createForm(new MiniSiteAddressType(), $company, $options);

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

            $em = $this->getDoctrine()->getManager();
            /* @var $em  EntityManager */

            if ($company->isSlugChanged()) {
                $em->getRepository('MetalCompaniesBundle:CompanyOldSlug')->changeCompanySlug($company, $currentSlug);

                if ($company->getPackageChecker()->isHttpsAllowed()) {
                    $this->container->get('metal.project.cloudflare_service')->renameRecord($currentSlug, $company->getSlug());
                }
            }

            $em->flush();

            return JsonResponse::create(
                array(
                    'companyArray' => $this->formatCompany($company)
                )
            );
        }

        return $this->render(
            'MetalPrivateOfficeBundle:MiniSite:address.html.twig',
            array(
                'form' => $form->createView(),
                'companyArray' => $this->formatCompany($company)
            )
        );
    }

    /**
     * @Security("has_role('ROLE_SUPPLIER') and user.getHasEditPermission() and (not request.isMethod('POST') or has_role('ROLE_CONFIRMED_EMAIL'))")
     */
    public function analyticsAction(Request $request)
    {
        $user = $this->getUser();
        /* @var $user User */
        $em = $this->getDoctrine()->getManager();
        /* @var $em  EntityManager */
        $miniSiteConfig = $user->getCompany()->getMinisiteConfig();

        $form = $this->createForm(new MiniSiteConfigType(), $miniSiteConfig);

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

            $miniSiteConfig->setUpdatedAt(new \DateTime());
            $em->flush();

            return JsonResponse::create(
                array(
                    'status' => 'success',
                    'updatedAt' => $this->get('brouzie.helper_factory')
                        ->get('MetalProjectBundle:Formatting')
                        ->formatDateTime($miniSiteConfig->getUpdatedAt())
                ),
                200
            );
        }

        return $this->render(
            '@MetalPrivateOffice/MiniSite/external_statistics.html.twig',
            array(
                'form' => $form->createView(),
                'miniSiteConfig' => $miniSiteConfig
            )
        );
    }

    /**
     * @Security("has_role('ROLE_SUPPLIER') and user.getHasEditPermission() and (not request.isMethod('POST') or has_role('ROLE_CONFIRMED_EMAIL'))")
     */
    public function colorsAction(Request $request)
    {
        $em = $this->getDoctrine()->getManager();
        $user = $this->getUser();
        /* @var $user User */
        $company = $user->getCompany();

        $minisiteConfig = $company->getMinisiteConfig();

        $form = $this->createForm(new MiniSiteColorsType(), $minisiteConfig);

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

            $company->getCounter()->setMinisiteColorsUpdatedAt(new \DateTime());

            $em->flush();

            $miniSiteColorsHelper = $this->container->get('brouzie.helper_factory')->get('MetalMiniSiteBundle:ValueObject');
            $backgroundColors = $miniSiteColorsHelper->getAllBackgroundColors();
            $colors = $backgroundColors[$minisiteConfig->getBackgroundColor()];
            $colors['primary'] = $minisiteConfig->getPrimaryColor();
            $colors['secondary'] = $minisiteConfig->getSecondaryColor();
            $colors['background'] = $minisiteConfig->getBackgroundColor();
            $this->get('metal.mini_site.service.mini_site_css_compiler')->compileCss($colors, $minisiteConfig->getCompany()->getId());

            return JsonResponse::create(
                array(
                    'status' => 'success',
                )
            );
        }

        return $this->render(
            'MetalPrivateOfficeBundle:MiniSite:colors.html.twig',
            array(
                'form' => $form->createView(),
            )
        );
    }

    /**
     * @Security("has_role('ROLE_SUPPLIER') and user.getHasEditPermission() and has_role('ROLE_CONFIRMED_EMAIL')")
     */
    public function shareAction()
    {
        return $this->render('MetalPrivateOfficeBundle:MiniSite:share.html.twig');
    }

    private function formatCover(MiniSiteCover $cover)
    {
        $cacheManager = $this->container->get('liip_imagine.cache.manager');
        $path = $this->container->get('vich_uploader.templating.helper.uploader_helper')->asset($cover, 'uploadedFile');

        $activeCover = array(
            'id' => $cover->getId(),
            'coverThumbUrl' => $cacheManager->getBrowserPath($path, 'minisite_cover_small'),
            'coverUrl' => $cacheManager->getBrowserPath($path, 'minisite_cover_big'),
            'applyCoverUrl' => $this->generateUrl('MetalPrivateOfficeBundle:MiniSite:saveCover', array('cover_id' => $cover->getId())),
        );

        return $activeCover;
    }

    private function formatCompany(Company $company)
    {
        $companyAsArray = array(
            'slug' => $company->getSlug(),
            'isSlugChanged' => $company->isSlugChanged(),
            'domain' => $company->getDomain(),
            'countryBaseHost' => $company->getCountry()->getBaseHost(),
            'slugChangedDate' => $this->get('brouzie.helper_factory')
                ->get('MetalProjectBundle:Formatting')
                ->formatDateTime($company->getSlugChangedAt())
        );

        $urlHelper = $this->container->get('brouzie.helper_factory')->get('MetalProjectBundle:Url');
        /* @var $urlHelper UrlHelper */

        if ($company->getMinisiteEnabled()) {
            $companyAsArray['url'] = $urlHelper->generateUrl(
                'MetalMiniSiteBundle:MiniSite:view',
                array('domain' => $company->getDomain(), '_secure' => $company->getPackageChecker()->isHttpsAvailable()),
                true
            );
        }

        return $companyAsArray;
    }
}
