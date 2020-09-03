<?php

namespace Metal\CompaniesBundle\Entity;

use Metal\ServicesBundle\Entity\Package;

class PackageChecker
{
    const MAX_PRODUCTS_COUNT_PROMOCODE = 100;

    const SHOW_PRODUCT_PHOTO_NONE = 0;
    const SHOW_PRODUCT_PHOTO_HALF_SIZE = 1;
    const SHOW_PRODUCT_PHOTO_FULL = 2;

    private static $packageOptions = array(
        Package::BASE_PACKAGE => array(
            'allow_minisite_header' => false,
            'allow_minisite_colors' => false,
            'max_products_count' => 50,
            'max_products_count_minisite' => 50,
            'max_cities_count' => 0,
            'max_cities_count_other_countries' => 0,
            'max_categories_count' => 10,
            'show_foreign_advertise' => true,
            'show_foreign_advertise_minisite' => true,
            'show_product_photo' => self::SHOW_PRODUCT_PHOTO_NONE,
            'show_product_photo_minisite' => true,
            'allow_connect_with_photo' => false,
            'enabled_auto_association_with_photos' => false,
            'allow_add_in_favorite' => false,
            'priority_show' => 0,
            'actualization_available' => false,
            'auto_approving_documents' => false,
            'display_callback_block' => false,
            'show_contacts' => false,
            'show_company_logo' => false,
            'show_company_logo_minisite' => false,
            'allow_show_company_name' => false,
            'show_additional_contacts_block' => false,
            'allow_export_demands' => false,
            'add_minisite_to_sites' => false,
            'show_on_corpsite' => false,
            'https_allowed' => false,
            'allow_add_custom_categories' => false,
            'allow_add_documents' => false,
            'show_hot_offer_menu_item' => false,
        ),
        Package::STANDARD_PACKAGE => array(
            'allow_minisite_header' => true,
            'allow_minisite_colors' => true,
            'max_products_count' => 500,
            'max_products_count_minisite' => null,
            'max_cities_count' => 0,
            'max_cities_count_other_countries' => 0,
            'max_categories_count' => 10,
            'show_foreign_advertise' => true,
            'show_foreign_advertise_minisite' => false,
            'show_product_photo' => self::SHOW_PRODUCT_PHOTO_FULL,
            'show_product_photo_minisite' => false,
            'allow_connect_with_photo' => true,
            'enabled_auto_association_with_photos' => true,
            'allow_add_in_favorite' => true,
            'priority_show' => 1,
            'actualization_available' => false,
            'auto_approving_documents' => false,
            'display_callback_block' => false,
            'show_contacts' => false,
            'show_company_logo' => false,
            'show_company_logo_minisite' => true,
            'allow_show_company_name' => false,
            'show_additional_contacts_block' => false,
            'allow_export_demands' => false,
            'add_minisite_to_sites' => false,
            'show_on_corpsite' => false,
            'https_allowed' => true,
            'allow_add_custom_categories' => true,
            'allow_add_documents' => true,
            'show_hot_offer_menu_item' => true,
        ),
        Package::ADVANCED_PACKAGE => array(
            'allow_minisite_header' => true,
            'allow_minisite_colors' => true,
            'max_products_count' => null,
            'max_products_count_minisite' => null,
            'max_cities_count' => 10,
            'max_cities_count_other_countries' => 2,
            'max_categories_count' => null,
            'show_foreign_advertise' => false,
            'show_foreign_advertise_minisite' => false,
            'show_product_photo' => self::SHOW_PRODUCT_PHOTO_FULL,
            'show_product_photo_minisite' => true,
            'allow_connect_with_photo' => true,
            'enabled_auto_association_with_photos' => true,
            'allow_add_in_favorite' => true,
            'priority_show' => 2,
            'actualization_available' => true,
            'auto_approving_documents' => true,
            'display_callback_block' => true,
            'show_contacts' => true,
            'show_company_logo' => true,
            'show_company_logo_minisite' => true,
            'allow_show_company_name' => true,
            'show_additional_contacts_block' => true,
            'allow_export_demands' => false,
            'add_minisite_to_sites' => true,
            'show_on_corpsite' => true,
            'https_allowed' => true,
            'allow_add_custom_categories' => true,
            'allow_add_documents' => true,
            'show_hot_offer_menu_item' => true,
        ),
        Package::FULL_PACKAGE => array(
            'allow_minisite_header' => true,
            'allow_minisite_colors' => true,
            'max_products_count' => null,
            'max_products_count_minisite' => null,
            'max_cities_count' => 20,
            'max_cities_count_other_countries' => 2,
            'max_categories_count' => null,
            'show_foreign_advertise' => false,
            'show_foreign_advertise_minisite' => false,
            'show_product_photo' => self::SHOW_PRODUCT_PHOTO_FULL,
            'show_product_photo_minisite' => true,
            'allow_connect_with_photo' => true,
            'enabled_auto_association_with_photos' => true,
            'allow_add_in_favorite' => true,
            'priority_show' => 2,
            'actualization_available' => true,
            'auto_approving_documents' => true,
            'display_callback_block' => true,
            'show_contacts' => true,
            'show_company_logo' => true,
            'show_company_logo_minisite' => true,
            'allow_show_company_name' => true,
            'show_additional_contacts_block' => true,
            'allow_export_demands' => true,
            'add_minisite_to_sites' => true,
            'show_on_corpsite' => true,
            'https_allowed' => true,
            'allow_add_custom_categories' => true,
            'allow_add_documents' => true,
            'show_hot_offer_menu_item' => true,
        ),
    );

    /**
     * @var Company
     */
    private $company;

    private $codeAccess;

    public static function initializePackageTypes(array $packageTypes, $debug = false)
    {
        if ($debug) {
            foreach (self::$packageOptions as $packageId => $packageOptions) {
                if (!isset($packageTypes[$packageId])) {
                    continue;
                }

                if ($keys = array_keys(array_diff_key($packageOptions, $packageTypes[$packageId]))) {
                    throw new \InvalidArgumentException(sprintf('Missing keys: %s', implode(',', $keys)));
                }
            }
        }

        self::$packageOptions = array_replace_recursive(self::$packageOptions, $packageTypes);
    }

    public static function getPackagesByOption($option, $value = true)
    {
        $packages = array();
        foreach (self::$packageOptions as $package => $packageOption) {
            if ($packageOption[$option] === $value) {
                $packages[] = $package;
            }
        }

        return $packages;
    }

    public static function getOptionValuesPerPackage($option)
    {
        $optionValues = array();
        foreach (self::$packageOptions as $package => $packageOption) {
            $optionValues[$package] = $packageOption[$option];
        }

        return $optionValues;
    }

    public function __construct(Company $company)
    {
        $this->company = $company;
        $this->codeAccess = $company->getCodeAccess();
    }

    public function isAllowedSetHeader()
    {
        return self::$packageOptions[$this->codeAccess]['allow_minisite_header'];
    }

    public function isAllowedSetColor()
    {
        return self::$packageOptions[$this->codeAccess]['allow_minisite_colors'];
    }

    public function isAllowedSetDeliveryDescription()
    {
        return $this->company->getVisibilityStatus() !== Company::VISIBILITY_STATUS_NORMAL;
    }

    public function isAllowedConnectWithPhoto()
    {
        return self::$packageOptions[$this->codeAccess]['allow_connect_with_photo'];
    }

    public function isEnabledAutoAssociationWithPhotos()
    {
        return self::$packageOptions[$this->codeAccess]['enabled_auto_association_with_photos'];
    }

    public function isForeignAdvertiseShouldBeVisible()
    {
        return self::$packageOptions[$this->codeAccess]['show_foreign_advertise'];
    }

    public function isForeignAdvertiseShouldBeVisibleOnMinisite()
    {
        return self::$packageOptions[$this->codeAccess]['show_foreign_advertise_minisite'];
    }

    public function getShowProductPhoto()
    {
        return self::$packageOptions[$this->codeAccess]['show_product_photo'];
    }

    public function getShowHotOfferMenuItem()
    {
        return self::$packageOptions[$this->codeAccess]['show_hot_offer_menu_item'];
    }

    public function isProductPhotosShouldBeVisibleOnMinisite()
    {
        return self::$packageOptions[$this->codeAccess]['show_product_photo_minisite'];
    }

    public function isAllowedAddInFavorite()
    {
        return self::$packageOptions[$this->codeAccess]['allow_add_in_favorite'];
    }

    /**
     * @return int|null
     */
    public function getMaxAvailableProductsCount()
    {
        if ($this->company->getPromocode()) {
            return self::MAX_PRODUCTS_COUNT_PROMOCODE;
        }

        return self::$packageOptions[$this->codeAccess]['max_products_count'];
    }

    /**
     * @return int|null
     */
    public function getMaxAvailableProductsCountMinisite()
    {
        if ($this->company->getPromocode()) {
            return self::MAX_PRODUCTS_COUNT_PROMOCODE;
        }

        return self::$packageOptions[$this->codeAccess]['max_products_count_minisite'];
    }

    /**
     * Сколько ВСЕГО можно добавить городов
     *
     * @return int|null
     */
    public function getMaxPossibleCompanyCitiesCount()
    {
        if ($this->company->isPromo()) {
            return null;
        }

        return self::$packageOptions[$this->codeAccess]['max_cities_count'];
    }

    /**
     * Сколько городов можно добавить из текущей страны
     *
     * @return int|null
     */
    public function getMaxPossibleCitiesCountFromCurrentCountry()
    {
        //  если куплен пакет "Вся россия" - все города по россии, макс 2 из снг
        if ($this->company->getVisibilityStatus() == Company::VISIBILITY_STATUS_ALL_CITIES) {
            return null;
        }

        //  если куплен пакет "СНГ" - все города СНГ, макс 20 по россии,
        if ($this->company->getVisibilityStatus() == Company::VISIBILITY_STATUS_OTHER_COUNTRIES) {
            return 20;
        }

        // если оба пакета - нет ограничений
        if ($this->company->getVisibilityStatus() == Company::VISIBILITY_STATUS_ALL_COUNTRIES) {
            return null;
        }

        // в своей стране можно сколько угодно городов создавать
        return null;
    }

    /**
     * Сколько городов можно добавить из других стран
     *
     * @return int|null
     */
    public function getMaxPossibleCitiesCountFromOtherCountries()
    {
        //  если куплен пакет "Вся россия" - все города по россии, макс 2 из снг
        if ($this->company->getVisibilityStatus() == Company::VISIBILITY_STATUS_ALL_CITIES) {
            return self::$packageOptions[$this->codeAccess]['max_cities_count_other_countries'];
        }

        //  если куплен пакет "СНГ" - все города СНГ, макс 20 по россии,
        if ($this->company->getVisibilityStatus() == Company::VISIBILITY_STATUS_OTHER_COUNTRIES) {
            return null;
        }

        if ($this->company->getVisibilityStatus() == Company::VISIBILITY_STATUS_ALL_COUNTRIES) {
            return null;
        }

        if ($this->company->getCity()->getCountry()->getId() != $this->company->getCity()->getDisplayInCountry()->getId()) {
            return self::$packageOptions[$this->codeAccess]['max_cities_count'];
        }

        return self::$packageOptions[$this->codeAccess]['max_cities_count_other_countries'];
    }

    public function getMaxPossibleCategoriesCount()
    {
        return self::$packageOptions[$this->codeAccess]['max_categories_count'];
    }

    public function isPaidAccount()
    {
        return $this->codeAccess != Package::BASE_PACKAGE;
    }

    public function isDisplayCallbackBlock()
    {
        return self::$packageOptions[$this->codeAccess]['display_callback_block'];
    }

    public function isScheduledActualizationAvailable()
    {
        return self::$packageOptions[$this->codeAccess]['actualization_available'];
    }

    public function isContactsShouldBeVisible()
    {
        return self::$packageOptions[$this->codeAccess]['show_contacts'];
    }

    public function isCompanyLogoShouldBeVisible()
    {
        return self::$packageOptions[$this->codeAccess]['show_company_logo'];
    }

    public function isCompanyLogoShouldBeVisibleOnMinisite()
    {
        return self::$packageOptions[$this->codeAccess]['show_company_logo_minisite'];
    }

    public function isAllowedShowCompanyName()
    {
        return self::$packageOptions[$this->codeAccess]['allow_show_company_name'];
    }

    public function isAdditionalContactsBlockShouldBeVisible()
    {
        return self::$packageOptions[$this->codeAccess]['show_additional_contacts_block'];
    }

    public function isAllowedExportDemands()
    {
        return self::$packageOptions[$this->codeAccess]['allow_export_demands'];
    }

    public function isHttpsAllowed()
    {
        return self::$packageOptions[$this->codeAccess]['https_allowed'];
    }

    public function isHttpsAvailable()
    {
        return $this->company->getIsAddedToCloudflare()
            && $this->company->getCountry()->getSecure()
            && (!$this->company->getDomainId() || $this->company->getDomainId() == 1)
            && $this->isHttpsAllowed();
    }

    public function isCustomCategoriesAllowed()
    {
        return self::$packageOptions[$this->codeAccess]['allow_add_custom_categories'];
    }

    public function isDocumentsAllowed()
    {
        return self::$packageOptions[$this->codeAccess]['allow_add_documents'];
    }
}
