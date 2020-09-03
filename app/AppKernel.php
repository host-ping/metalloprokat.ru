<?php

use Symfony\Component\Config\Loader\LoaderInterface;
use Symfony\Component\HttpKernel\Kernel;

class AppKernel extends Kernel
{
    private const LOG_DIR = '/var/log/www';

    private $hostnamePackage;

    public function __construct($environment, $debug, $hostnamePackage)
    {
        parent::__construct($environment, $debug);
        $this->hostnamePackage = $hostnamePackage;
    }

    /**
     * @return string
     */
    public function getHostnamePackage()
    {
        return $this->hostnamePackage;
    }

    /**
     * {@inheritdoc}
     */
    public function registerBundles()
    {
        $bundles = array(
            new Symfony\Bundle\FrameworkBundle\FrameworkBundle(),
            new Symfony\Bundle\SecurityBundle\SecurityBundle(),
            new Symfony\Bundle\TwigBundle\TwigBundle(),
            new Symfony\Bundle\MonologBundle\MonologBundle(),
            new Symfony\Bundle\SwiftmailerBundle\SwiftmailerBundle(),
            new Symfony\Bundle\AsseticBundle\AsseticBundle(),
            new Doctrine\Bundle\DoctrineBundle\DoctrineBundle(),
            new Sensio\Bundle\FrameworkExtraBundle\SensioFrameworkExtraBundle(),
            new Doctrine\Bundle\MigrationsBundle\DoctrineMigrationsBundle(),
            new WhiteOctober\PagerfantaBundle\WhiteOctoberPagerfantaBundle(),
            new Knp\Bundle\MenuBundle\KnpMenuBundle(),
            new Gregwar\FormBundle\GregwarFormBundle(),
            new Presta\SitemapBundle\PrestaSitemapBundle(),
            new Sonata\CoreBundle\SonataCoreBundle(),
            new Sonata\BlockBundle\SonataBlockBundle(),
            new Sonata\AdminBundle\SonataAdminBundle(),
            new Sonata\DoctrineORMAdminBundle\SonataDoctrineORMAdminBundle(),
            new Sonata\NotificationBundle\SonataNotificationBundle(),
            new Sonata\IntlBundle\SonataIntlBundle(),
            new Buzz\Bundle\BuzzBundle\BuzzBundle(),
            new Bazinga\Bundle\GeocoderBundle\BazingaGeocoderBundle(),
            new Stof\DoctrineExtensionsBundle\StofDoctrineExtensionsBundle(),
            new Symfony\Cmf\Bundle\RoutingBundle\CmfRoutingBundle(),
            new Liuggio\ExcelBundle\LiuggioExcelBundle(),
            new Snc\RedisBundle\SncRedisBundle(),
            new Liip\ImagineBundle\LiipImagineBundle(),
            new jh9\RobokassaBundle\jh9RobokassaBundle(),
            new Vipx\BotDetectBundle\VipxBotDetectBundle(),
            new Ornicar\AkismetBundle\OrnicarAkismetBundle(),
            new EWZ\Bundle\RecaptchaBundle\EWZRecaptchaBundle(),
            new FM\BbcodeBundle\FMBbcodeBundle(),
            new Gregwar\CaptchaBundle\GregwarCaptchaBundle(),
            new Jmikola\AutoLoginBundle\JmikolaAutoLoginBundle(),
            new Vich\UploaderBundle\VichUploaderBundle(),
            new FOS\ElasticaBundle\FOSElasticaBundle(),
            new Enqueue\Bundle\EnqueueBundle(),

            new Brouzie\WidgetsBundle\BrouzieWidgetsBundle(),
            new Brouzie\Bundle\HelpersBundle\BrouzieHelpersBundle(),
            new Brouzie\Bundle\SphinxyBundle\BrouzieSphinxyBundle(),

            new Metal\UsersBundle\MetalUsersBundle(),
            new Metal\CompaniesBundle\MetalCompaniesBundle(),
            new Metal\DemandsBundle\MetalDemandsBundle(),
            new Metal\ProductsBundle\MetalProductsBundle(),
            new Metal\CategoriesBundle\MetalCategoriesBundle(),
            new Metal\TerritorialBundle\MetalTerritorialBundle(),
            new Metal\ProjectBundle\MetalProjectBundle(),
            new Metal\NewsletterBundle\MetalNewsletterBundle(),
            new Metal\ComplaintsBundle\MetalComplaintsBundle(),
            new Metal\StatisticBundle\MetalStatisticBundle(),
            new Metal\CallbacksBundle\MetalCallbacksBundle(),
            new Metal\PrivateOfficeBundle\MetalPrivateOfficeBundle(),
            new Metal\MiniSiteBundle\MetalMiniSiteBundle(),
            new Metal\SupportBundle\MetalSupportBundle(),
            new Metal\AnnouncementsBundle\MetalAnnouncementsBundle(),
            new Metal\CorpsiteBundle\MetalCorpsiteBundle(),
            new Metal\ServicesBundle\MetalServicesBundle(),
            new Metal\AttributesBundle\MetalAttributesBundle(),
            new Metal\GrabbersBundle\MetalGrabbersBundle(),

            // stroy, product
            new Metal\CatalogBundle\MetalCatalogBundle(),

            // stroy
            new Metal\ContentBundle\MetalContentBundle(),

            // metalloprokat
            new Spros\ProjectBundle\SprosProjectBundle(),
        );

        if (in_array($this->getEnvironment(), ['prod', 'profiler'])) {
            $bundles[] = new SmartCore\Bundle\AcceleratorCacheBundle\AcceleratorCacheBundle();
            $bundles[] = new Intaro\PinbaBundle\IntaroPinbaBundle();
        }

        if (in_array($this->getEnvironment(), ['dev', 'test', 'profiler'])) {
            $bundles[] = new Symfony\Bundle\DebugBundle\DebugBundle();
            $bundles[] = new Symfony\Bundle\WebProfilerBundle\WebProfilerBundle();
            $bundles[] = new Sensio\Bundle\DistributionBundle\SensioDistributionBundle();
            $bundles[] = new Sensio\Bundle\GeneratorBundle\SensioGeneratorBundle();
        }

        return $bundles;
    }

    /**
     * {@inheritdoc}
     */
    public function registerContainerConfiguration(LoaderInterface $loader)
    {
        $loader->load($this->rootDir.'/config/config_'.$this->getEnvironment().'.yml');
        $additionalParameters = $this->rootDir.'/config/parameters/'.$this->hostnamePackage.'.yml';
        $loader->load($additionalParameters);
    }

    /**
     * {@inheritdoc}
     */
    public function getCacheDir()
    {
        return $this->rootDir.'/cache/'.$this->hostnamePackage.'/'.$this->environment;
    }

    /**
     * {@inheritdoc}
     */
    public function getLogDir()
    {
        if (is_dir(self::LOG_DIR) && is_writable(self::LOG_DIR)) {
            return self::LOG_DIR.'/'.basename(dirname(__DIR__)).'-'.$this->hostnamePackage;
        }

        return $this->rootDir.'/logs/'.$this->hostnamePackage;
    }

    public function serialize()
    {
        return serialize(array($this->environment, $this->debug, $this->hostnamePackage));
    }

    public function unserialize($data)
    {
        list($environment, $debug, $hostnamePackage) = unserialize($data);

        $this->__construct($environment, $debug, $hostnamePackage);
    }

    /**
     * {@inheritdoc}
     */
    protected function getKernelParameters()
    {
        return array_merge(
            array(
                'kernel.hostname_package' => $this->getHostnamePackage(),
            ),
            parent::getKernelParameters()
        );
    }
}
