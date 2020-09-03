<?php

namespace Metal\AnnouncementsBundle\Admin;

use Doctrine\ORM\EntityManager;
use Doctrine\ORM\EntityRepository;
use Metal\AnnouncementsBundle\Entity\Announcement;
use Metal\AnnouncementsBundle\Entity\ZoneStatus;
use Sonata\AdminBundle\Admin\AbstractAdmin;
use Sonata\AdminBundle\Datagrid\DatagridMapper;
use Sonata\AdminBundle\Datagrid\ListMapper;
use Sonata\AdminBundle\Form\FormMapper;
use Sonata\AdminBundle\Route\RouteCollection;
use Sonata\CoreBundle\Validator\ErrorElement;
use Sonata\DoctrineORMAdminBundle\Datagrid\ProxyQuery;
use Alchemy\Zippy;

class AnnouncementAdmin extends AbstractAdmin
{
    protected $datagridValues = array(
        '_sort_order' => 'DESC',
        '_page' => 1,
        '_per_page' => 25,
    );

    /**
     * The number of result to display in the list.
     *
     * @var int
     */
    protected $maxPerPage = 25;

    /**
     * Predefined per page options.
     *
     * @var array
     */
    protected $perPageOptions = array(15, 25, 50, 100, 150, 200);

    /**
     * @var EntityManager
     */
    private $em;

    private $fileUploadPath;

    public function __construct($code, $class, $baseControllerName, EntityManager $em, $path)
    {
        parent::__construct($code, $class, $baseControllerName);

        $this->em = $em;
        $this->fileUploadPath = $path;
    }

    protected $parentAssociationMapping = 'company';

    public function configure()
    {
        $this->setTemplate('edit', 'MetalAnnouncementsBundle:AdminAnnouncement:announcement_edit.html.twig');
    }

    public function prePersist($object)
    {
        /* @var $object Announcement */
        $object->populateFileData();
    }

    public function preUpdate($object)
    {
        /* @var $object Announcement */
        $object->populateFileData();
    }

    public function postPersist($object)
    {
        $this->processUploadedFile($object);

        /* @var $object Announcement */
        if ($object->getIsPayed()) {
            $this->createZoneStatus($object);
        } else {
            $this->createZoneStatus($object, true);
        }

        $this->changeFileSize($object);
    }

    public function postUpdate($object)
    {
        /* @var $object Announcement */

        if (null !== $object->photo || null !== $object->fallbackPhoto) {
            $object->setVersion($object->getVersion() + 1);
        }

        $this->processUploadedFile($object);

        $zoneStatus = $this->em->getRepository('MetalAnnouncementsBundle:ZoneStatus')->findOneBy(array('announcement' => $object));
        if ($object->getIsPayed()) {
            if ($zoneStatus) {
                $zoneStatus->setStatus(ZoneStatus::STATUS_PURCHASED);
                $zoneStatus->setDeleted(false);
                $zoneStatus->setStartsAt($object->getStartsAt());
                $zoneStatus->setEndsAt($object->getEndsAt());
            } else {
                $this->createZoneStatus($object);
            }
        } else {
            if ($zoneStatus) {
                $zoneStatus->setDeleted(true);
                $zoneStatus->setStartsAt($object->getStartsAt());
                $zoneStatus->setEndsAt($object->getEndsAt());
            } else {
                $this->createZoneStatus($object, true);
            }
        }

        $this->em->flush();

        $this->changeFileSize($object);
    }

    public function changeFileSize(Announcement $object)
    {
        if (!$object->isHtml()) {
            return;
        }

        $filePath = $this->fileUploadPath.$object->getWebPath();
        $htmlFile = file_get_contents($filePath);

        $zone = $object->getZone();
        $width = $zone->getWidth().'px';
        $height = $zone->getHeight().'px';

        if ($object->isResizable()) {
            $width = '100%';
            $height = '100%';
        }

        $htmlFile = preg_replace('/<div id=\"swiffycontainer\"(.*?)>/ui', '<div id="swiffycontainer" style="width: '.$width.'; height:'.$height.' ">', $htmlFile);

        file_put_contents($filePath, $htmlFile);
    }

    protected function createZoneStatus(Announcement $object, $deleted = false)
    {
        $zoneStatus = new ZoneStatus();
        $zoneStatus->setStartsAt($object->getStartsAt());
        $zoneStatus->setEndsAt($object->getEndsAt());
        $zoneStatus->setStatus(ZoneStatus::STATUS_PURCHASED);
        $zoneStatus->setAnnouncement($object);
        $zoneStatus->setCompany($object->getCompany());
        $zoneStatus->setZone($object->getZone());
        $zoneStatus->setDeleted($deleted);

        $this->em->persist($zoneStatus);
        $this->em->flush();
    }

    public function postRemove($object)
    {
        /* @var $object Announcement */
        @unlink($this->fileUploadPath.'/'.$object->getSubDir().'/'.$object->getFilePath());
        @unlink($this->fileUploadPath.'/'.$object->getSubDir().'/'.$object->getFallbackFilePath());
    }

    public function getBatchActions()
    {
        return array();
    }

    protected function configureRoutes(RouteCollection $collection)
    {
        $collection
            ->add('download_file', $this->getRouterIdParameter().'/download_file', array('_controller' => 'MetalAnnouncementsBundle:AnnouncementsAdmin:downloadFile'));
    }

    public function setSubject($subject)
    {
        parent::setSubject($subject);
        /* @var $subject Announcement */

        if (!$subject->getId()) {
            $this->formOptions['validation_groups'] = array('Default', 'new_item');
        }
    }

    protected function configureFormFields(FormMapper $form)
    {
        $announcementHelper = $this->getConfigurationPool()->getContainer()->get('brouzie.helper_factory')->get('MetalAnnouncementsBundle:AdminDefault');
        $subject = $this->getSubject();
        /* @var $subject Announcement */

        $subjectSize = $announcementHelper->getAnnouncementSizePerZone($subject);
        $subjectWidth = $subjectSize['width'];
        $subjectHeight = $subjectSize['height'];

        $fileOptions = array('label' => 'Баннер');

        if ($subject->getFilePath()) {
            $defaultWebPath = $subject->getEmbedWebPath();
            $webPath = $this->getConfigurationPool()->getContainer()->get('templating.helper.assets')->getUrl($defaultWebPath, null, $subject->getVersion());

            if ($subject->isFlash()) {
                $webPathCode = <<<HTML
    <object type="application/x-shockwave-flash" data="$webPath" width="$subjectWidth" height="$subjectHeight">
        <param name="movie" value="$webPath" />
        <param name="quality" value="high" />
        <embed src="$webPath" quality="high" width="$subjectWidth" height="$subjectHeight"></embed>
    </object>
HTML;
                $fileOptions['help'] = $webPathCode;
            } elseif ($subject->isHtml() || $subject->isZip()) {
                $webPathCode = <<<HTML
    <iframe src="$webPath" width="$subjectWidth" height="$subjectHeight" scrolling="no">
    </iframe>
HTML;
                $fileOptions['help'] = $webPathCode;
            }
            else {
                $fileOptions['help'] = '<img src="'.$webPath.'" />';
            }

            $fileOptions['required'] = false;
            $fileOptions['help'] .='<br /><a href="'.$this->generateObjectUrl('download_file', $subject).'">'.htmlspecialchars($subject->getFileOriginalName(), ENT_QUOTES | ENT_SUBSTITUTE, 'UTF-8').'</a>';

        }

        $fallbackFileOptions = array('label' => 'Запасной файл', 'required' => false);
        if ($subject->getFallbackFilePath()) {
            $defaultWebPath = $subject->getFallbackWebPath();
            $fallbackWebPath = $this->getConfigurationPool()->getContainer()->get('templating.helper.assets')->getUrl($defaultWebPath, null, $subject->getVersion());
            $fallbackFileOptions['help'] = '<img src="'.$fallbackWebPath.'" />';
        }

        $form
            ->add('isPayed', 'checkbox', array('label' => 'Оплачено', 'required' => false))
            ->add('startsAt', 'sonata_type_date_picker', array('label' => 'Дата старта', 'format' => 'dd.MM.yyyy'))
            ->add('endsAt', 'sonata_type_date_picker', array('label' => 'Дата окончания', 'format' => 'dd.MM.yyyy'))
            ->add('link', 'url', array('label' => 'Ссылка'));
            if (!$this->isChild()) {
                $form
                    ->add('company', 'entity_id', array(
                            'label' => 'ID Компании',
                            'class' => 'MetalCompaniesBundle:Company',
                            'required'  => true,
                            'hidden'  => false,
                        ));
            }
        $form
            ->add('photo', 'file', $fileOptions)
            ->add('fallbackPhoto', 'file', $fallbackFileOptions)
            ->add('resizable',
                'checkbox',
                array(
                    'label' => 'Резиновый',
                    'required' => false
                )
            )
            ->add('zone', 'entity', array(
                'class' => 'MetalAnnouncementsBundle:Zone',
                'property' => 'titleAndSize',
                'label' => 'Зона',
                'placeholder' => '',
                'query_builder' => function (EntityRepository $repository) {
                    return $repository->createQueryBuilder('zone')
                        ->orderBy('zone.title', 'ASC');
                },
                'required' => true,
                'group_by' => 'section.title'

            ))
            ->add('showEverywhere',
                'checkbox',
                array(
                    'label' => 'Выводить во всех категориях',
                    'required' => false,
                    'help' => 'Не совместимо с категориями'
                )
            )
            ->add(
                'announcementCategories',
                'sonata_type_collection',
                array(
                    'label' => 'Категории',
                    'by_reference' => false,
                    'required' => false,
                    'help' => 'Категории, в которых будет выводиться баннер.'
                ),
                array(
                    'edit' => 'inline',
                    'inline' => 'table',
                )
            )
            ->add('showEverywhereTerritory',
                'checkbox',
                array(
                    'label' => 'Выводить во всех территориях',
                    'required' => false,
                    'help' => 'Не совместимо с территориями'
                )
            )
            ->add(
                'announcementTerritorial',
                'sonata_type_collection',
                array(
                    'label' => 'Территориальная принадлежность',
                    'by_reference' => false,
                    'required' => false,
                    'help' => 'Города/Регионы/Страны в которых будет выводится баннер. Заполнена графа Страна - баннер показывается только в этой стране.'
                ),
                array(
                    'edit' => 'inline',
                    'inline' => 'table',
                )
            )
        ;
    }

    protected function configureListFields(ListMapper $list)
    {
        $list
            ->addIdentifier('id')
            ->add('company', null, array('label' => 'Компания', 'template' => 'MetalProjectBundle:Admin:company_with_id_list.html.twig'))
            ->add('photo', null, array(
                    'label' => 'Баннер',
                    'template' => 'MetalAnnouncementsBundle:AdminAnnouncement:announcement_item.html.twig'
                ))
            ->add('fallbackPhoto', null, array(
                    'label' => 'Запасной файл',
                    'template' => 'MetalAnnouncementsBundle:AdminAnnouncement:announcement_fallback_item.html.twig'
                ))
            ->add('startsAt', null, array('label' => 'Дата старта'))
            ->add('endsAt', null, array('label' => 'Дата окончания'))
            ->add('isPayed', null, array('label' => 'Оплачено'))
            ->add('zone', null, array('label' => 'Зона', 'template' => 'MetalAnnouncementsBundle:AdminZone:section_type_list.html.twig'))
        ;
    }

    protected function processUploadedFile(Announcement $announcement)
    {
        $dir = $this->fileUploadPath.'/'.$announcement->getSubDir();
        $isZip = $announcement->isZip();

        if (null !== $announcement->photo) {
            $announcement->photo->move($dir, $announcement->getFilePath());
            $announcement->photo = null;

            if ($isZip) {
                $zippy = Zippy\Zippy::load();
                $archive = $zippy->open($dir.'/'.$announcement->getFilePath());
                $dirToExtract = $dir.'/'.$announcement->getId();

                if (!@mkdir($dirToExtract) && !is_dir($dirToExtract)) {
                    throw new \RuntimeException(sprintf('Не удалось создать директорию "%s"', $dirToExtract));
                }

                $archive->extract($dirToExtract);
            }
        }

        if (null !== $announcement->fallbackPhoto) {
            $announcement->fallbackPhoto->move($dir, $announcement->getFallbackFilePath());
            $announcement->fallbackPhoto = null;
        }
    }

    protected function configureDatagridFilters(DatagridMapper $datagridMapper)
    {
        $datagridMapper
            ->add('company', 'doctrine_orm_number', array('label' => 'ID компании'))
            ->add('company.title', 'doctrine_orm_string', array('label' => 'Название компании'))
            ->add(
                'is_active',
                'doctrine_orm_callback',
                array(
                    'label' => 'Время действия',
                    'callback' => function (ProxyQuery $queryBuilder, $alias, $field, $value) {
                        if (!isset($value['value'])) {
                            return;
                        }

                        switch ($value['value']) {
                            case 'past': {
                                $queryBuilder->andWhere(sprintf("%s.endsAt < :now", $alias));
                                break;
                            }
                            case 'active': {
                                $queryBuilder->andWhere(sprintf(":now BETWEEN %s.startsAt AND %s.endsAt", $alias, $alias));
                                break;
                            }
                            case 'future': {
                                $queryBuilder->andWhere(sprintf("%s.startsAt > :now", $alias));

                                break;
                            }
                        }
                        $queryBuilder->setParameter('now', new \DateTime());

                        return true;
                    }
                ),
                'choice',
                array(
                    'choices' => array(
                        'past' => 'Отработвашие',
                        'active' => 'Действующие',
                        'future' => 'Будущие'
                    )
                )
            )
            ->add(
                'isPayed',
                'doctrine_orm_boolean',
                array(
                    'label' => 'Оплачено'
                ),
                'sonata_type_boolean'
            )
            ->add(
                'showEverywhere',
                'doctrine_orm_boolean',
                array(
                    'label' => 'Выводить во всех категориях'
                ),
                'sonata_type_boolean'
            )
            ->add(
                'zone',
                null,
                array(
                    'class' => 'MetalAnnouncementsBundle:Zone',
                    'label' => 'Зона',
                    'query_builder' => function (EntityRepository $repository) {
                        return $repository->createQueryBuilder('zone')
                            ->orderBy('zone.title', 'ASC');
                    },
                ),
                null,
                array('property' => 'title', 'group_by' => 'section.title')
            )
        ;
    }

    public function toString($object)
    {
        return $object instanceof Announcement ? sprintf('Баннер %d', $object->getId()) : '';
    }

    public function validate(ErrorElement $errorElement, $object)
    {
        /* @var $object Announcement */
        if (!$this->isChild() && !$object->getCompany()) {
            $errorElement
                ->with('company')
                ->addViolation('Поле обязательное к заполнению')
                ->end()
            ;
        }

        if ($object->getUploadedFile() && $object->isZip()) {
            $zippy = Zippy\Zippy::load();
            $archive = $zippy->open($object->getUploadedFile()->getRealPath(), 'zip');
            $existIndexFile = false;
            foreach ($archive->getMembers() as $el) {
                if ($el->getLocation() === 'index.html') {
                    $existIndexFile = true;
                }
            }

            if (!$existIndexFile) {
                $errorElement
                    ->with('photo')
                    ->addViolation('Файл index.html является обязательным.')
                    ->end()
                ;
            }
        }

        if ($object->getShowEverywhere() && count($object->getAnnouncementCategories())) {
            $errorElement
                ->with('announcementCategories')
                ->addViolation('"Выводить во всех категориях" не совместимо с категориями. Удалите категории или отключите "Выводить во всех категориях".')
                ->end()
            ;
        }

        $announcementTerritorial = $object->getAnnouncementTerritorial();
        foreach ($announcementTerritorial as $territorial) {
            $territoryValid = 0;
            if ($territorial->getCity()) {
                $territoryValid++;
            }

            if ($territorial->getRegion()) {
                $territoryValid++;
            }

            if ($territorial->getCountry()) {
                $territoryValid++;
            }

            if ($territoryValid > 1) {
                $errorElement
                    ->with('announcementTerritorial')
                    ->addViolation('В территориальную принадлежность на одно поле можно добавить только "Город или Регион или Страну"')
                    ->end()
                ;
            }

            if ($territoryValid === 0) {
                $errorElement
                    ->with('announcementTerritorial')
                    ->addViolation('В территориальную принадлежность нельзя добавлять пустую строку.')
                    ->end()
                ;
            }
        }
    }
}
