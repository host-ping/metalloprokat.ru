<?php

namespace Metal\TerritorialBundle\Admin;

use Doctrine\ORM\EntityManager;
use Metal\TerritorialBundle\Entity\Country;
use Sonata\AdminBundle\Admin\AbstractAdmin;
use Sonata\AdminBundle\Datagrid\DatagridMapper;
use Sonata\AdminBundle\Datagrid\ListMapper;
use Sonata\AdminBundle\Form\FormMapper;
use Sonata\AdminBundle\Route\RouteCollection;
use Sonata\CoreBundle\Validator\ErrorElement;
use Symfony\Component\Validator\Constraints as Assert;

class CountryAdmin extends AbstractAdmin
{
    /**
     * @var EntityManager
     */
    private $em;

    /**
     * @param EntityManager $em
     */
    public function setEntityManager(EntityManager $em)
    {
        $this->em = $em;
    }

    public function configureRoutes(RouteCollection $collection)
    {
        $collection->remove('delete');
    }

    protected function configureFormFields(FormMapper $formMapper)
    {
        $subject = $this->getSubject();
        $parameters = array();
        if ($subject->getId()) {
            $parameters['country_id'] = $subject->getId();
        }

        $formMapper
            ->add('title', null, array(
                'label' => 'Название',
                'required' => true,
            ))
            ->add('capitalTitle', 'text', array(
                'label' => 'Столица',
                'required' => true,
                'constraints' => array(new Assert\NotBlank()),
                'attr' => array(
                    'typeahead' => '',
                    'typeahead-prefetch-url' => $this->routeGenerator->generate('MetalTerritorialBundle:Suggest:getCitiesForCountry', $parameters),
                    'typeahead-suggestion-template-url' => "'typeahead-suggestion-with-parent'",
                    'typeahead-model' => 'city',
                ),
            ))
            ->add('capital', 'entity_id', array(
                'class' => 'MetalTerritorialBundle:City',
                'label' => 'ID столицы',
                'hidden' => false,
                'read_only' => true,
                'required' => false,
                'attr' => array(
                    'ng-model' => 'city.id',
                    'initial-value' => '',
                ),
            ))
            ->add('titleLocative', null, array('label' => 'Местный падеж', 'help' => 'Где? - заявка на покупку арматуры композитной в России'))
            ->add('titleGenitive', null, array('label' => 'Родительный падеж', 'help' => 'Кого? Чего? - из России'))
            ->add('titleAccusative', null, array('label' => 'Винительный падеж', 'help' => 'Кого? Что? Куда? - товары доствляемые в Россию'))
            ->add('callbackPhone', null, array('label' => 'Номер обратного звонка'))
            ->add('supportPhone', null, array('label' => 'Номер тех. поддержки'))
        ;
    }

    protected function configureListFields(ListMapper $listMapper)
    {
        $listMapper
            ->addIdentifier('id')
            ->add('title', null, array('label' => 'Название'))
            ->add('capital', null, array(
                    'label' => 'Столица',
                    'associated_property' => 'title',
                    'placeholder' => 'Выберите столицу',
                )
            )
            ->add('domainTitle', null, array('label' => 'Домен'))
            ->add('titleLocative', null, array('label' => 'Местный'))
            ->add('titleGenitive', null, array('label' => 'Родительный'))
            ->add('titleAccusative', null, array('label' => 'Винительный'))
            ->add('supportPhone', null, array('label' => 'Номер тех. поддержки'))
            ->add(
                'menu',
                null,
                array(
                    'label' => 'Меню',
                    'template' => 'MetalTerritorialBundle:AdminCountry:countryMenu.html.twig'
                )
            )
        ;
    }

    public function validate(ErrorElement $errorElement, $object)
    {
        if ($object->getCapital()) {
            $isUseCapital = $this->em->getRepository('MetalTerritorialBundle:Country')->createQueryBuilder('country')
                ->where('country.capital = :capital')
                ->andWhere('country.id <> :country')
                ->setParameter('country', $object)
                ->setParameter('capital', $object->getCapital())
                ->getQuery()
                ->getResult()
            ;

            if ($isUseCapital) {
                $errorElement
                    ->with('capital')
                    ->addViolation('Нельзя добавлять одну и туже столицу нескольким странам. Сначала поменяйте столицу в других странах и сохраните изменения.')
                    ->end();
            }

            $IsOtherCountry = $this->em->getRepository('MetalTerritorialBundle:City')
                ->createQueryBuilder('city')
                ->where('city.id = :capital')
                ->andWhere('city.country = :country')
                ->setParameter('country', $object)
                ->setParameter('capital', $object->getCapital())
                ->getQuery()
                ->getOneOrNullResult()
            ;

            if (!$IsOtherCountry) {
                $errorElement
                    ->with('capital')
                    ->addViolation('Нельзя добавлять город из другой страны.')
                    ->end();
            }
        }
    }

    public function postPersist($object)
    {
        $this->updateCity($object);
    }

    public function postUpdate($object)
    {
        $this->updateCity($object);
    }

    protected function configureDatagridFilters(DatagridMapper $datagridMapper)
    {
        $datagridMapper
            ->add('title', null, array('label' => 'Название'));
    }

    private function updateCity(Country $object)
    {
        $this->em->createQueryBuilder()
            ->update('MetalTerritorialBundle:City', 'city')
            ->set('city.isCapital', 0)
            ->where('city.country = :country')
            ->setParameter('country', $object)
            ->andWhere('city.isCapital = true')
            ->getQuery()
            ->execute();

        if ($object->getCapital()) {
            $this->em->createQueryBuilder()
                ->update('MetalTerritorialBundle:City', 'city')
                ->set('city.isCapital', 1)
                ->andWhere('city.id = :capital')
                ->setParameter('capital', $object->getCapital())
                ->getQuery()
                ->execute();
        }
    }

    public function toString($object)
    {
        return $object instanceof Country ? $object->getTitle() : '';
    }
}
