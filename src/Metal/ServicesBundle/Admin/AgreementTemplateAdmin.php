<?php

namespace Metal\ServicesBundle\Admin;

use Brouzie\Bundle\HelpersBundle\Helper\HelperFactory;
use Metal\CompaniesBundle\Entity\Company;
use Metal\ServicesBundle\Entity\AgreementTemplate;
use Metal\ServicesBundle\Helper\AgreementHelper;
use Sonata\AdminBundle\Admin\AbstractAdmin;
use Sonata\AdminBundle\Datagrid\DatagridMapper;
use Sonata\AdminBundle\Datagrid\ListMapper;
use Sonata\AdminBundle\Form\FormMapper;
use Sonata\AdminBundle\Route\RouteCollection;
use Symfony\Component\Security\Core\Authentication\Token\Storage\TokenStorageInterface;

class AgreementTemplateAdmin extends AbstractAdmin
{
    /**
     * @var \Twig_Environment
     */
    private $twig;

    /**
     * @var TokenStorageInterface
     */
    private $tokenStorage;

    /**
     * @var AgreementHelper
     */
    protected $agreementHelper;

    public function __construct($code, $class, $baseControllerName, $twig,
                                TokenStorageInterface $tokenStorage,
                                HelperFactory $helperFactory)
    {
        parent::__construct($code, $class, $baseControllerName);

        $this->tokenStorage = $tokenStorage;
        $this->twig = $twig;
        $this->agreementHelper = $helperFactory->get('MetalServicesBundle:Agreement');
    }

    protected function configureRoutes(RouteCollection $collection)
    {
        $collection
            ->remove('delete')
            ->remove('create')
        ;
    }

    protected function configureFormFields(FormMapper $formMapper)
    {
        $agreementTemplate = $this->getSubject();
        /* @var $agreementTemplate AgreementTemplate */

        $company = $this->tokenStorage->getToken()->getUser()->getCompany();
        /* @var $company Company */

        $twigTemplate = $this->twig->createTemplate($agreementTemplate->getContent());
        $arrayMatch = $this->agreementHelper->getMatches($company);

        $agreementHtml = $twigTemplate->render(array(
            'company' => $arrayMatch['company'],
            'paymentDetails' => $arrayMatch['paymentDetails']
        ));

//        $agreementHtml = $this->twig->render($agreementTemplate->getContent(), array(
//            'company' => $company,
//            'paymentDetails' => $company->getPaymentDetails(),
//        ));

        $replacements = $agreementTemplate->getReplacements();

        $description = null;
        if ($replacements) {
            $description = sprintf('<pre>%s</pre>', $replacements);
        }

        $formMapper
            ->tab('Main')
                ->with('Main', array('description' => $description))
                    ->add('title', null, array('label' => 'Заголовок', 'disabled' => true))
                    ->add('content', 'textarea', array('label' => 'Контент','required' => false, 'attr' => array('rows' => 30)))
                ->end()
            ->end()


            ->tab('Preview')
                ->with('Preview', array('description' => $agreementHtml))
            ->end()
        ;
    }

    protected function configureListFields(ListMapper $listMapper)
    {
        $listMapper
            ->addIdentifier('id')
            ->add('title', null, array('label' => 'Заголовок'))
            ->add('content', null, array('label' => 'Контент'))
        ;
    }

    protected function configureDatagridFilters(DatagridMapper $datagridMapper)
    {
        $datagridMapper
            ->add('title', null, array('label' => 'Заголовок'))
        ;
    }

    public function toString($object)
    {
        return $object instanceof AgreementTemplate ? $object->getTitle() : '';
    }


}
