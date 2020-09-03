<?php

namespace Metal\PrivateOfficeBundle\Helper;

use Brouzie\Bundle\HelpersBundle\Helper\HelperAbstract;

use Metal\CompaniesBundle\Entity\Company;
use Metal\CompaniesBundle\Entity\CompanyFile;
use Metal\CompaniesBundle\Entity\CompanyReview;
use Metal\ProductsBundle\Entity\Product;
use Metal\ProductsBundle\Entity\ProductImage;
use Metal\ProjectBundle\Helper\FormattingHelper;
use Metal\ProjectBundle\Helper\ImageHelper;
use Metal\ProjectBundle\Helper\UrlHelper;
use Symfony\Component\HttpFoundation\File\MimeType\ExtensionGuesser;

class SerializerHelper extends HelperAbstract
{
    public function serializeProduct(Product $product, $forCustomCategory = false)
    {
        $imagesHelper = $this->container->get('brouzie.helper_factory')->get('MetalProjectBundle:Image');
        /* @var $imagesHelper ImageHelper */

        $serialized = array(
            'id' => $product->getId(),
            'title' => $product->getTitle(),
            'size' => $product->getSize(),
            'isSpecialOffer' => $product->getIsSpecialOffer(),
            'position' => $product->getPosition(),
            'isPriceFrom' => $product->getIsPriceFrom(),
            'isModerated' => $product->isModerated(),
            'isContractPrice' => $product->isContractPrice(),
            'isPendingProcess' => $product->isLockedForEditing(),
            'isLimitExceeding' => $product->isLimitExceeding(),
            'measureTypeId' => $product->getMeasureId(),
            'measureType' => $product->getMeasure()->getToken(),
            'price' => $product->isContractPrice() ? null : $product->getPrice(),
            'currency' => $product->getCurrency()->getToken(),
            'symbolClass' => $product->getCurrency()->getSymbolClass(),
            'saveUrl' => $this->container->get('router')->generate('MetalPrivateOfficeBundle:Product:save', array('id' => $product->getId())),
            'description' => $product->getProductDescription()->getDescription(),
            'isHotOffer' => $product->getIsHotOffer(),
            'hotOfferPosition' => $product->getHotOfferPosition(),
        );

        if ($product->getCategory()) {
            $serialized['category'] = array(
                'id' => $product->getCategory()->getId(),
                'title' => $product->getCategory()->getTitle(),
            );
        }

        if ($forCustomCategory && $product->getCustomCategory()) {
            $serialized['customCategory'] = array(
                'id' => $product->getCustomCategory()->getId(),
                'title' => $product->getCustomCategory()->getTitle(),
            );
        }

        if ($imageUrl = $imagesHelper->getPhotoUrlForProductPhoto($product->getImage(), 'sq136')) {
            $serialized['photo'] = array(
                'id' => $product->getImage()->getId(),
                'imageUrl' => $imageUrl
            );
        }

        return $serialized;
    }

    public function serializeProductPhoto(ProductImage $productPhoto)
    {
        $imagesHelper = $this->container->get('brouzie.helper_factory')->get('MetalProjectBundle:Image');
        /* @var $imagesHelper ImageHelper */

        $router = $this->container->get('router');

        return array(
            'id' => $productPhoto->getId(),
            'isCommon' => $productPhoto->isCommon(),
            'description' => $productPhoto->getCategory() ? $productPhoto->getCategory()->getTitle() : $productPhoto->getDescription(),
            'imageUrl' => $imagesHelper->getPhotoUrlForProductPhoto($productPhoto, 'sq136'),
            'editUrl' => $router->generate('MetalPrivateOfficeBundle:PrivateProductPhoto:save', array('id' => $productPhoto->getId())),
            'deleteUrl' => $router->generate('MetalPrivateOfficeBundle:PrivateProductPhoto:delete', array('photo_id' => $productPhoto->getId())),
            'connectProductsUrl' => $router->generate('MetalPrivateOfficeBundle:Products:connectProductsWithPhoto', array('photo_id' => $productPhoto->getId())),
            'optimized' => $productPhoto->getOptimized()
        );
    }

     public function serializeCompanyDocument(CompanyFile $document)
    {
        $router = $this->container->get('router');

        $urlHelper = $this->container->get('brouzie.helper_factory')->get('MetalProjectBundle:Url');
        /* @var $urlHelper UrlHelper */
        $company = $document->getCompany();
        /* @var $company Company */
        $extensionGuesser = ExtensionGuesser::getInstance();

        return array(
            'id' => $document->getId(),
            'title' => $document->getTitle(),
            'fileSize' => $document->getFile()->getSize(),
            'extension' => $extensionGuesser->guess($document->getFile()->getMimeType()),
            'downloadUrl' => $urlHelper->generateUrl('MetalMiniSiteBundle:MiniSite:downloadDocument', array('id' => $document->getId(), 'domain' => $company->getDomain(), '_secure' => $company->getPackageChecker()->isHttpsAvailable())),
            'saveUrl' => $router->generate('MetalPrivateOfficeBundle:DocumentsManagement:save', array('id' => $document->getId())),
            'deleteUrl' => $router->generate('MetalPrivateOfficeBundle:DocumentsManagement:delete', array('id' => $document->getId())),
        );
    }

    public function serializeReview(CompanyReview $review)
    {
        $formatHelper = $this->getHelper('MetalProjectBundle:Formatting');
        /* @var $formatHelper FormattingHelper */

        $urlHelper = $this->container->get('brouzie.helper_factory')->get('MetalProjectBundle:Url');
        /* @var $urlHelper UrlHelper */
        $company = $review->getCompany();
        /* @var $company Company */

        $serialized = array(
            'id' => $review->getId(),
            'firstName' => $review->getName(),
            'isModerated' => $review->isModerated(),
            'isCommentPositive' => $review->isCommentPositive(),
            'createdAt' => $formatHelper->getTimeLocalized($review->getCreatedAt()),
            'comment' => $review->getComment(),
            'moderateUrl' => $urlHelper->generateUrl('MetalCompaniesBundle:MiniSite:moderateReview', array('id' => $review->getId(), '_secure' => $company->getPackageChecker()->isHttpsAvailable())),
            'deleteUrl' => $this->container->get('router')->generate('MetalPrivateOfficeBundle:Reviews:delete', array('id' => $review->getId())),
            'answerUrl' => $urlHelper->generateUrl('MetalCompaniesBundle:MiniSite:review_answer', array('id' => $review->getId(), '_secure' => $company->getPackageChecker()->isHttpsAvailable())),
        );

        if ($review->getAnswer()) {
            $serialized['answer'] = array(
                'id' => $review->getAnswer()->getId(),
                'comment' => $review->getAnswer()->getComment()
            );
        }

        if ($review->getUser()) {
            $serialized['firstName'] = $review->getUser()->getFirstName();
        }

        return $serialized;
    }
}
