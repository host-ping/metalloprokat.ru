<?php

namespace Metal\AnnouncementsBundle\Widget;

use Brouzie\Bundle\WidgetsBundle\Widget\WidgetAbstract;
use Brouzie\WidgetsBundle\Widget\ConditionallyRenderedWidget;
use Metal\AnnouncementsBundle\Helper\DefaultHelper as AnnouncementHelper;
use Metal\ProjectBundle\Entity\ValueObject\SourceTypeProvider;

class AnnounceFrameWidget extends WidgetAbstract implements ConditionallyRenderedWidget
{
    public function shouldBeRendered()
    {
        if ($this->options['zone_slug'] !== 'premium') {
            return true;
        }

        //TODO: del после четверга 16.2017
        $announcementHelper = $this->container->get('brouzie.helper_factory')->get('MetalAnnouncementsBundle');
        /* @var $announcementHelper AnnouncementHelper */
        $shouldReplacePremiumAnnouncement = $announcementHelper->shouldReplacePremiumAnnouncement();
        if ($shouldReplacePremiumAnnouncement && $this->options['source_type_slug'] !== 'frontpage') {
            return false;
        }

        return true;
    }

    protected function setDefaultOptions()
    {
        $this->optionsResolver
            ->setRequired(array('zone_slug', 'tag_type', 'source_type_slug'))
            ->setDefaults(array(
                'tag_type' => 'div',
                'element_id_prefix' => 'announcement-container-',
                'category_id' => null,
                'additional_class' => '',
                'only_company_id' => null
            ))
        ;
    }

    public function getParametersToRender()
    {
        $request = $this->getRequest();

        $city = $request->attributes->get('city');
        $category = $request->attributes->get('category');
        $country = $request->attributes->get('country');

        try {
            $announcementLoadingOptions = array(
                'source_type_id' => SourceTypeProvider::createBySlug($this->options['source_type_slug'])->getId()
            );
        } catch (\InvalidArgumentException $ex) {
            $announcementLoadingOptions = array(
                'source_type_id' => 4
            );
        }

        $announcementLoadingOptions['element_id'] = $this->options['element_id_prefix'].substr(sha1(microtime(true).mt_rand(0, 9999)), 0, 10);
        $announcementLoadingOptions['zone_slug'] = $this->options['zone_slug'];
        $announcementLoadingOptions['only_company_id'] = $this->options['only_company_id'];
        $announcementLoadingOptions['is_background'] = false;

        if ($city) {
            $announcementLoadingOptions['city_id'] = $city->getId();
        }

        if ($country) {
            $announcementLoadingOptions['country_id'] = $country->getId();
        }

        if ($category) {
            $announcementLoadingOptions['category_id'] = $category->getId();
        } elseif ($categoryForBanner = $this->options['category_id']) {
            $announcementLoadingOptions['category_id'] = $categoryForBanner;
        }

        $announcementLoadingOptions['referer'] = $request->getUri();

        if ($this->options['zone_slug'] === 'background') {
            $announcementLoadingOptions['is_background'] = true;
        }

        return compact('announcementLoadingOptions');
    }
}
