<?php

namespace Metal\UsersBundle\Security\Handler;

use Sonata\AdminBundle\Admin\AdminInterface;
use Sonata\AdminBundle\Security\Handler\RoleSecurityHandler as BaseRoleSecurityHandler;
use Symfony\Component\Security\Core\Authentication\Token\Storage\TokenStorageInterface;

class RoleSecurityHandler extends BaseRoleSecurityHandler
{
    /**
     * @var TokenStorageInterface
     */
    private $tokenStorage;

    public function __construct($authorizationChecker, array $superAdminRoles, TokenStorageInterface $tokenStorage)
    {
        parent::__construct($authorizationChecker, $superAdminRoles);

        $this->tokenStorage = $tokenStorage;
    }

    public function isGranted(AdminInterface $admin, $attributes, $object = null)
    {
        $deleteSuperAdminMap = array(
            'metal.categories.admin.category',
            'metal.categories.admin.menu_item',
            'metal.support.admin.topic',
            'metal.grabbers.admin.parsed_demand',
            'metal.attributes.admin.attribute_value',
        );

        if (in_array($admin->getCode(), $deleteSuperAdminMap) && $attributes === 'DELETE') {
            return $this->authorizationChecker->isGranted($this->superAdminRoles);
        }

        if ($this->authorizationChecker->isGranted($this->superAdminRoles)) {
            return true;
        }
        // * - для всех действий
        $permissionsMap = [
            'metal.users.admin.user' => 'ROLE_PRODUCT_MODERATOR',
            'metal.companies.admin.company' => ['ROLE_EXTENDED_MODERATOR', 'ROLE_PRODUCT_MODERATOR'],
            'metal.companies.admin.company_phone' => ['ROLE_EXTENDED_MODERATOR', 'ROLE_PRODUCT_MODERATOR'],
            'metal.companies.admin.company_delivery_city' => ['ROLE_EXTENDED_MODERATOR', 'ROLE_PRODUCT_MODERATOR'],
            'metal.companies.admin.company_category' => ['ROLE_EXTENDED_MODERATOR', 'ROLE_PRODUCT_MODERATOR'],

            'metal.demands.admin.demand' => ['ROLE_EXTENDED_MODERATOR', 'ROLE_PRODUCT_MODERATOR'],
            'metal.demands.admin.demand_item' => ['ROLE_EXTENDED_MODERATOR', 'ROLE_PRODUCT_MODERATOR'],
            'metal.demands.admin.demand_file' => ['ROLE_EXTENDED_MODERATOR', 'ROLE_PRODUCT_MODERATOR'],

            'metal.grabbers.admin.parsed_demand' => 'ROLE_MANAGER',
            'metal.products.admin.product' => ['ROLE_EXTENDED_MODERATOR', 'ROLE_PRODUCT_MODERATOR'],
            'metal.products.admin.product_image' => ['ROLE_EXTENDED_MODERATOR', 'ROLE_PRODUCT_MODERATOR'],
            'metal.callbacks.admin.callback' => 'ROLE_CALLBACK_ADMIN',
            'metal.complaints.admin.complaint' => ['ROLE_PRODUCT_MODERATOR', 'ROLE_MANAGER'],
            'metal.support.admin.topic' => ['ROLE_PRODUCT_MODERATOR', 'ROLE_MANAGER'],
            'metal.support.admin.answer' => 'ROLE_MANAGER',
            'metal.announcements.admin.zone' => 'ROLE_MANAGER',
            'metal.announcements.admin.announcement' => 'ROLE_MANAGER',
            'metal.announcements.admin.zone_status' => 'ROLE_MANAGER',
            'metal.announcements.admin.order_announcement' => 'ROLE_MANAGER',
            'metal.announcements.admin.announcement_category' => 'ROLE_MANAGER',
            'metal.corpsite.admin.promotion' => 'ROLE_MANAGER',
            'metal.corpsite.admin.client_review' => 'ROLE_MANAGER',
            'metal.services.admin.payment' => 'ROLE_MANAGER',
            'metal.newsletter.admin.subscriber' => 'ROLE_MANAGER',
            'metal.demands.admin.demand_subscription' => 'ROLE_MANAGER',
            'metal.mini_site.admin.mini_site_cover' => ['ROLE_EXTENDED_MODERATOR', 'ROLE_PRODUCT_MODERATOR'],
            'metal.categories.admin.category' => [
                'LIST' => ['ROLE_SEO_ADMINISTRATOR', 'ROLE_PRODUCT_MODERATOR'],
                'EDIT' => ['ROLE_SEO_ADMINISTRATOR'],
                '*' => ['ROLE_EXTENDED_MODERATOR'],
            ],
            'metal.project.admin.redirect' => [
                'LIST' => ['ROLE_SEO_ADMINISTRATOR'],
            ],
            'metal.categories.admin.category_city_metadata' => [
                'ROLE_EXTENDED_MODERATOR',
                'ROLE_SEO_ADMINISTRATOR',
            ],
            'metal.categories.admin.menu_item' => 'ROLE_EXTENDED_MODERATOR',
            'metal.territorial.admin.city_code' => 'ROLE_EXTENDED_MODERATOR',
            'metal.project.admin.normalized_phone' => 'ROLE_PRODUCT_MODERATOR',
            'metal.project.admin.normalized_email' => 'ROLE_PRODUCT_MODERATOR',
            'metal.project.admin.landing' => 'ROLE_EXTENDED_MODERATOR',
            'metal.project.admin.ban_ip' => 'ROLE_PRODUCT_MODERATOR',
            'metal.companies.admin.promocode' => 'ROLE_MANAGER',
            'metal.attributes.admin.attribute' => [
                'LIST' => ['ROLE_SEO_ADMINISTRATOR'],
                '*' => [
                    'ROLE_EXTENDED_MODERATOR',
                ],
            ],

            'metal.attributes.admin.attribute_value' => [
                'LIST' => ['ROLE_SEO_ADMINISTRATOR'],
                '*' => [
                    'ROLE_CATALOG_PRODUCT_MODERATOR',
                    'ROLE_EXTENDED_MODERATOR',
                ],
            ],
            'metal.attributes.admin.attribute_value_category' => [
                'LIST' => ['ROLE_SEO_ADMINISTRATOR'],
                '*' => [
                    'ROLE_CATALOG_PRODUCT_MODERATOR',
                    'ROLE_EXTENDED_MODERATOR',
                ],
            ],
            'metal.catalog.admin.product' => ['ROLE_CATALOG_PRODUCT_MODERATOR', 'ROLE_EXTENDED_MODERATOR'],
            'metal.catalog.admin.product_city' => ['ROLE_CATALOG_PRODUCT_MODERATOR', 'ROLE_EXTENDED_MODERATOR'],
            'metal.catalog.admin.product_attribute_value' => [
                'ROLE_CATALOG_PRODUCT_MODERATOR',
                'ROLE_EXTENDED_MODERATOR',
            ],
            'metal.catalog.admin.manufacturer' => ['ROLE_CATALOG_PRODUCT_MODERATOR', 'ROLE_EXTENDED_MODERATOR'],
            'metal.catalog.admin.brand' => ['ROLE_CATALOG_PRODUCT_MODERATOR', 'ROLE_EXTENDED_MODERATOR'],
            'metal.catalog.admin.product_review' => ['ROLE_CATALOG_PRODUCT_MODERATOR', 'ROLE_EXTENDED_MODERATOR'],

            'metal.content.admin.tag' => ['ROLE_CONTENT_MODERATOR', 'ROLE_EXTENDED_MODERATOR'],
            'metal.content.admin.topic' => ['ROLE_CONTENT_MODERATOR', 'ROLE_EXTENDED_MODERATOR'],
            'metal.content.admin.question' => ['ROLE_CONTENT_MODERATOR', 'ROLE_EXTENDED_MODERATOR'],
            'metal.content.admin.comment' => ['ROLE_CONTENT_MODERATOR', 'ROLE_EXTENDED_MODERATOR'],
            'metal.content.admin.category' => ['ROLE_EXTENDED_MODERATOR'],
            'metal.content.admin.comment_instagram' => ['ROLE_EXTENDED_MODERATOR'],
            'metal.categories.admin.landing_page' => ['ROLE_SEO_ADMINISTRATOR', 'ROLE_EXTENDED_MODERATOR'],
            'metal.project.admin.site' => ['ROLE_SEO_ADMINISTRATOR', 'ROLE_EXTENDED_MODERATOR'],
            'metal_project.admin.seo_template_admin' => ['ROLE_SEO_ADMINISTRATOR', 'ROLE_EXTENDED_MODERATOR'],
        ];

        $disabledComplaintsEmails = array('m.lukyanova@metalloprokat.ru', 'ml@product.ru');
        if (in_array($this->tokenStorage->getToken()->getUser()->getEmail(), $disabledComplaintsEmails)) {
            unset($permissionsMap['metal.complaints.admin.complaint']);
        }

        if (!isset($permissionsMap[$admin->getCode()])) {
            return false;
        }

        $rules = $permissionsMap[$admin->getCode()];
        foreach ((array)$attributes as $attribute) {
            if (isset($rules[$attribute]) && $this->authorizationChecker->isGranted($rules[$attribute])) {
                return true;
            }

            if (isset($rules['*'])) {
                return $this->authorizationChecker->isGranted($rules['*']);
            }
        }

        return $this->authorizationChecker->isGranted($rules);
    }
}
