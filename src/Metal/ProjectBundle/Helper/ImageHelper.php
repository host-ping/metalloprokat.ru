<?php

namespace Metal\ProjectBundle\Helper;

use Brouzie\Bundle\HelpersBundle\Helper\HelperAbstract;
use Metal\CompaniesBundle\Entity\Company;
use Metal\CompaniesBundle\Entity\PackageChecker;
use Metal\CorpsiteBundle\Entity\Client;
use Metal\ProductsBundle\Entity\Product;
use Metal\ProductsBundle\Entity\ProductImage;
use Metal\UsersBundle\Entity\User;

class ImageHelper extends HelperAbstract
{
    protected $imageTypes = array(
        'image/bmp' => 'bmp',
        'image/x-ms-bmp' => 'bmp',
        'image/gif' => 'gif',
        'image/jpeg' => 'jpeg',
        'image/jpg' => 'jpg',
        'image/pjpeg' => 'jpg',
        'image/png' => 'png',
        'image/tiff' => 'tiff',
        'image/x-png' => 'png',
        'application/x-shockwave-flash' => 'swf'
    );

    public function getImageTypes()
    {
        return $this->imageTypes;
    }

    public function getExtensionByMimeType($mimeType)
    {
        return isset($this->imageTypes[$mimeType]) ? $this->imageTypes[$mimeType] : 'gif';
    }

    /**
     * @param Product $product
     * @param string $pattern
     * @param string $context = minisite|portal|admin|private
     *
     * @return string
     */
    public function getPhotoUrlForProduct(Product $product, $pattern, $context)
    {
        $packageChecker = $product->getCompany()->getPackageChecker();

        switch ($context) {
            case 'minisite':
                if (!$packageChecker->isProductPhotosShouldBeVisibleOnMinisite()) {
                    return null;
                }
                break;

            case 'portal':
                switch ($packageChecker->getShowProductPhoto()) {
                    case PackageChecker::SHOW_PRODUCT_PHOTO_NONE:
                        return null;

                    case PackageChecker::SHOW_PRODUCT_PHOTO_HALF_SIZE:
                        // change pattern
                        $patterns = array(
                            'sq136' => 'sq68',
                        );
                        if (isset($patterns[$pattern])) {
                            $pattern = $patterns[$pattern];
                        }
                        break;

                    case PackageChecker::SHOW_PRODUCT_PHOTO_FULL:
                        // nothing to do
                        break;
                }
                break;

            case 'admin':
            case 'private':
                break;
        }

        return $this->getPhotoUrlForProductPhoto($product->getImage(), $pattern);
    }

    public function getPhotoUrlForProductPhoto(ProductImage $image = null, $pattern)
    {
        if (!$image || !$image->getPhoto()->getName()) {
            return null;
        }

        if ($image->getUrl() && !$image->getDownloaded()) {
            return null;
        }

        if (!$image->getOptimized()) {
            $pattern .= '_non_optim';
        }

        $path = $this->container->get('vich_uploader.templating.helper.uploader_helper')->asset($image, 'uploadedPhoto');
        $cacheManager = $this->container->get('liip_imagine.cache.manager');

        return $cacheManager->getBrowserPath($path, sprintf('products_%s', $pattern));
    }

    public function getPhotoUrlForImageData(array $imageData, $pattern)
    {
        if ($imageData['imageUrl'] && !$imageData['downloaded']) {
            return null;
        }

        if (!$imageData['optimized']) {
            $pattern = $pattern.'_non_optim';
        }

        $uri = '/products/'.substr($imageData['imageName'], 0, 2).'/'.$imageData['imageName'];

        $cacheManager = $this->container->get('liip_imagine.cache.manager');

        return $cacheManager->getBrowserPath($uri, sprintf('products_%s', $pattern));
    }

    public function getCompanyLogoUrl(Company $company, $pattern, $context)
    {
        if (!$company->getLogo()->getName()) {
            return null;
        }

        switch ($context) {
            case 'minisite':
                if (!$company->getPackageChecker()->isCompanyLogoShouldBeVisibleOnMinisite()) {
                    return null;
                }
                break;

            case 'portal':
                if (!$company->getPackageChecker()->isCompanyLogoShouldBeVisible()) {
                    return null;
                }
                break;

            case 'admin':
            case 'corp':
            case 'private':
                break;
        }

        if (!$company->getOptimizeLogo()) {
            $pattern .= '_non_optim';
        }

        $uri = $this->container->get('vich_uploader.templating.helper.uploader_helper')->asset($company, 'uploadedLogo');
        $cacheManager = $this->container->get('liip_imagine.cache.manager');

        return $cacheManager->getBrowserPath($uri, sprintf('logo_%s', $pattern));
    }

    public function getAvatarUrl(User $user, $pattern)
    {
        if (!$user->getPhoto()->getName()) {
            return null;
        }

        $uri = $this->container->get('vich_uploader.templating.helper.uploader_helper')->asset($user, 'uploadedPhoto');
        $cacheManager = $this->container->get('liip_imagine.cache.manager');

        return $cacheManager->getBrowserPath($uri, sprintf('users_%s', $pattern));
    }

    public function getCorpClientLogo(Client $client)
    {
        $mimeType = explode(':', $client->getLogoInfo())[1];

        $imageHost = $this->container->getParameter('avatar_host');
        $hostnamesMap = $this->container->getParameter('hostnames_map');
        $host = $this->container->getParameter('base_host');

        return $hostnamesMap[$host]['host_prefix'].'://'.$imageHost.'/netcat_files/1581_'.$client->getId().'.'.$this->getExtensionByMimeType($mimeType);
    }
}
