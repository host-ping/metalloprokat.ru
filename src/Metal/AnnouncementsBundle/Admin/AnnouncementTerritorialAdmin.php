<?php

namespace Metal\AnnouncementsBundle\Admin;

use Doctrine\ORM\EntityManager;
use Doctrine\ORM\EntityManagerInterface;
use Metal\AnnouncementsBundle\Entity\AnnouncementTerritorial;
use Metal\TerritorialBundle\Entity\City;
use Metal\TerritorialBundle\Entity\Country;
use Metal\TerritorialBundle\Entity\Region;
use Sonata\AdminBundle\Admin\Admin;
use Sonata\AdminBundle\Datagrid\ListMapper;
use Sonata\AdminBundle\Form\FormMapper;

class AnnouncementTerritorialAdmin extends Admin
{
    /**
     * @var EntityManager
     */
    private $em;

    public function __construct($code, $class, $baseControllerName, EntityManagerInterface $em)
    {
        parent::__construct($code, $class, $baseControllerName);

        $this->em = $em;
    }

    protected function configureListFields(ListMapper $listMapper)
    {
        $listMapper
            ->addIdentifier('id');
    }

    protected function configureFormFields(FormMapper $form)
    {
        $form
            ->add(
                'city',
                'entity',
                array(
                    'label' => 'Город',
                    'required' => false,
                    'placeholder' => '',
                    'class' => 'MetalTerritorialBundle:City',
                    'property' => 'title',
                    'choices' => $this->em->getRepository('MetalTerritorialBundle:City')->findBy(array('country' => Country::getEnabledCountriesIds()))
                )
            )
            ->add(
                'region',
                'entity',
                array(
                    'label' => 'Регион',
                    'required' => false,
                    'placeholder' => '',
                    'class' => 'MetalTerritorialBundle:Region',
                    'property' => 'title',
                    'choices' => $this->em->getRepository('MetalTerritorialBundle:Region')->findBy(array('country' => Country::getEnabledCountriesIds()))
                )
            )
            ->add(
                'country',
                'entity',
                array(
                    'label' => 'Страна',
                    'required' => false,
                    'placeholder' => '',
                    'class' => 'MetalTerritorialBundle:Country',
                    'property' => 'title',
                    'choices' => $this->em->getRepository('MetalTerritorialBundle:Country')->findBy(array('id' => Country::getEnabledCountriesIds()))
                )
            )
        ;
    }

    public function toString($object)
    {
        if (!$object instanceof AnnouncementTerritorial) {
            return '';
        }

        if ($object->getCity() instanceof City) {
            return sprintf('Город: %s', $object->getCity()->getTitle());
        } elseif ($object->getRegion() instanceof Region) {
            return sprintf('Регион: %s', $object->getRegion()->getTitle());
        } elseif ($object->getCountry() instanceof Country) {
            return sprintf('Страна: %s', $object->getCountry()->getTitle());
        }

        return '';
    }
}
