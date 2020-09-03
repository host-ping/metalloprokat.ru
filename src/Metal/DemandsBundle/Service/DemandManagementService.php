<?php

namespace Metal\DemandsBundle\Service;

use DeepCopy\DeepCopy;
use DeepCopy\Filter\Doctrine\DoctrineCollectionFilter;
use DeepCopy\Filter\Doctrine\DoctrineEmptyCollectionFilter;
use DeepCopy\Filter\KeepFilter;
use DeepCopy\Filter\SetNullFilter;
use DeepCopy\Matcher\PropertyMatcher;
use DeepCopy\Matcher\PropertyTypeMatcher;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\EntityManager;
use Metal\DemandsBundle\Entity\AbstractDemand;
use Metal\DemandsBundle\Entity\Demand;
use Metal\DemandsBundle\Entity\DemandCategory;
use Metal\DemandsBundle\Entity\DemandFile;
use Metal\DemandsBundle\Entity\DemandItem;
use Metal\DemandsBundle\Entity\PrivateDemand;
use Metal\DemandsBundle\Form\DemandType;
use Metal\NewsletterBundle\Service\Mailer;
use Metal\ProjectBundle\Entity\ValueObject\SiteSourceTypeProvider;
use Metal\ProjectBundle\Exception\FormValidationException;
use Metal\ProjectBundle\Util\RandomGenerator;
use Metal\TerritorialBundle\Entity\Region;
use Metal\UsersBundle\Entity\User;
use Metal\UsersBundle\Service\UserService;
use Symfony\Component\Form\FormFactoryInterface;
use Symfony\Component\HttpFoundation\File\UploadedFile;
use Symfony\Component\HttpFoundation\Request;

class DemandManagementService
{
    /**
     * @var EntityManager
     */
    private $em;

    /**
     * @var \Symfony\Component\Form\FormFactoryInterface
     */
    private $formFactory;

    private $mailer;

    private $userService;

    private $projectFamily;

    public function __construct(
        EntityManager $em,
        FormFactoryInterface $formFactory,
        Mailer $mailer,
        UserService $userService,
        $projectFamily
    ) {
        $this->em = $em;
        $this->formFactory = $formFactory;
        $this->mailer = $mailer;
        $this->userService = $userService;
        $this->projectFamily = $projectFamily;
    }

    public function createDemandFromRequest(Request $request, $isPrivate, User $user = null)
    {
        if ($isPrivate) {
            $demand = new PrivateDemand();
        } else {
            $demand = new Demand();
        }

        $options = array(
            'is_private' => $isPrivate,
            'is_authenticated' => $user !== null,
            'is_from_trader' => $user !== null && $user->getCompany() !== null,
            'data_class' => get_class($demand),
            'validation_groups' => array(
                $user !== null ? 'authenticated_prokat' : 'anonymous_prokat',
            ),
            'city_repository' => $this->em->getRepository('MetalTerritorialBundle:City')
        );

        $form = $this->formFactory->create(new DemandType(), $demand, $options);
        $form->handleRequest($request);

        if (!$form->isValid()) {
           throw new FormValidationException($form);
        }

        $demand->populateDataFromRequest($request);

        if ($user) {
            $demand->setUser($user);
            $user->setPhone($demand->getPhone());
            $user->setCompanyTitle($demand->getCompanyTitle());
            if ($demand->getCity()) {
                $user->setCity($demand->getCity());
            }
        }

        if ($isPrivate) {
            $city = $request->attributes->get('city');
            $region = $request->attributes->get('region');
            /* @var $region Region */
            $country = $request->attributes->get('country');

            if ($city) {
                $demand->setProductCity($city);
            } elseif ($region) {
                $demand->setProductCity($region->getAdministrativeCenter());
            }
            $demand->setProductCountry($country);

        }

        $this->em->persist($demand);
        $this->em->flush();

        if ($isPrivate) {
            $this->notifyCompanyUsersAboutNewDemand($demand);

            $this->em->getRepository('MetalCompaniesBundle:CompanyCounter')->changeCounter($demand->getCompany(), array('newDemandsCount'), true);
        }

        // если пользователя нет, то пытаемся его найти по email, иначе регестрируем (хоть для публичной, хоть для приватной заявки)
        if (null === $user && $demand->getEmail()) {
            $possibleUser = $this->em->getRepository('MetalUsersBundle:User')->findOneBy(array('email' => $demand->getEmail()));
            if ($possibleUser) {
                $demand->setConfirmationCode(RandomGenerator::generateRandomCode());
                $demand->setPossibleUser($possibleUser);
                $this->em->flush();
                $this->sendDemandConfirmationEmail($demand);
            } else {
                $userDate = array(
                    'userName' => $demand->getPerson(),
                    'userEmail' => $demand->getEmail(),
                    'userPhone'  => $demand->getPhone(),
                    'userCity' => $demand->getCity(),
                    'userCountry' => $demand->getCity()->getCountry(),
                    'companyTitle' => $demand->getCompanyTitle(),
                );
                $newUser = $this->userService->simpleUserRegister($userDate);
                $demand->setUser($newUser);
            }
        }

        $this->em->flush();

        return $demand;
    }

    public function copyDemand(Demand $demand)
    {
        $copier = new DeepCopy();
        $copier->addFilter(new SetNullFilter(), new PropertyMatcher(AbstractDemand::class, 'id'));
        $copier->addFilter(new KeepFilter(), new PropertyMatcher(AbstractDemand::class, 'user'));
        $copier->addFilter(new KeepFilter(), new PropertyMatcher(AbstractDemand::class, 'updatedBy'));
        $copier->addFilter(new KeepFilter(), new PropertyMatcher(AbstractDemand::class, 'city'));
        $copier->addFilter(new KeepFilter(), new PropertyMatcher(AbstractDemand::class, 'country'));
        $copier->addFilter(new KeepFilter(), new PropertyMatcher(AbstractDemand::class, 'possibleUser'));
        $copier->addFilter(new SetNullFilter(), new PropertyMatcher(AbstractDemand::class, 'category'));
        $copier->addFilter(new SetNullFilter(), new PropertyMatcher(AbstractDemand::class, 'fromDemand'));
        $copier->addFilter(new SetNullFilter(), new PropertyMatcher(AbstractDemand::class, 'fromCallback'));
        $copier->addFilter(new SetNullFilter(), new PropertyMatcher(AbstractDemand::class, 'parsedDemand'));

        $copier->addFilter(new DoctrineEmptyCollectionFilter(), new PropertyMatcher(AbstractDemand::class, 'demandItems'));
        $copier->addFilter(new DoctrineEmptyCollectionFilter(), new PropertyMatcher(AbstractDemand::class, 'demandCategories'));
        $copier->addFilter(new DoctrineEmptyCollectionFilter(), new PropertyMatcher(AbstractDemand::class, 'demandFiles'));

        $copiedDemand = $copier->copy($demand);

        return $copiedDemand;
    }

    public function createDemandFromPrivateDemand(PrivateDemand $demand)
    {
        $copier = new DeepCopy();
        $copier->addFilter(new SetNullFilter(), new PropertyMatcher(AbstractDemand::class, 'id'));
        $copier->addFilter(new KeepFilter(), new PropertyMatcher(AbstractDemand::class, 'user'));
        $copier->addFilter(new KeepFilter(), new PropertyMatcher(AbstractDemand::class, 'category'));
        $copier->addFilter(new KeepFilter(), new PropertyMatcher(AbstractDemand::class, 'city'));
        $copier->addFilter(new KeepFilter(), new PropertyMatcher(AbstractDemand::class, 'region'));
        $copier->addFilter(new KeepFilter(), new PropertyMatcher(AbstractDemand::class, 'country'));
        $copier->addFilter(new KeepFilter(), new PropertyMatcher(AbstractDemand::class, 'possibleUser'));

        $copier->addFilter(new SetNullFilter(), new PropertyMatcher(DemandItem::class, 'id'));
        $copier->addFilter(new SetNullFilter(), new PropertyMatcher(DemandCategory::class, 'id'));

        $copier->addFilter(new SetNullFilter(), new PropertyMatcher(PrivateDemand::class, 'product'));
        $copier->addFilter(new SetNullFilter(), new PropertyMatcher(PrivateDemand::class, 'company'));
        $copier->addFilter(new SetNullFilter(), new PropertyMatcher(PrivateDemand::class, 'productCity'));
        $copier->addFilter(new SetNullFilter(), new PropertyMatcher(PrivateDemand::class, 'productCountry'));

        $copier->addFilter(new SetNullFilter(), new PropertyMatcher(DemandFile::class, 'id'));

        $copier->addFilter(new DoctrineCollectionFilter(), new PropertyTypeMatcher(Collection::class));

        $copiedDemand = $copier->copy($demand);
        /* @var $copiedDemand PrivateDemand */

        foreach ($copiedDemand->getDemandFiles() as $demandFile) {
            // триггерим обновление файла, что б он пересохранился с другим именем
            if (null === $file = $demandFile->getUploadedFile()) {
                continue;
            }

            $tmpFilePath = tempnam(sys_get_temp_dir(), 'demand');
            copy($file->getRealPath(), $tmpFilePath);

            $uploadedFile = new UploadedFile(
                $tmpFilePath,
                $demandFile->getFile()->getOriginalName(),
                $file->getMimeType(),
                $file->getSize(),
                null,
                true
            );

            $demandFile->setUploadedFile($uploadedFile);
        }

        $copiedDemand->setSourceTypeId(SiteSourceTypeProvider::SOURCE_COPY_OF_PRIVATE);

        $this->em->persist($copiedDemand);
        $this->em->flush();

        $this->em->getConnection()
            ->executeUpdate(
                'UPDATE demand SET demand_type = :demand_type, from_demand_id = :from_demand_id WHERE id = :id',
                array(
                    'id' => $copiedDemand->getId(),
                    'demand_type' => AbstractDemand::TYPE_PUBLIC,
                    'from_demand_id' => $demand->getId()
                )
            );
    }

    protected function notifyCompanyUsersAboutNewDemand(PrivateDemand $demand)
    {
        $replyTo = null;
        if ($demand->getFixedEmail()) {
            $replyTo = array($demand->getFixedEmail() => $demand->getFixedCompanyTitle());
        }

        $company = $demand->getCompany();
        $mainUser = $company->getCompanyLog()->getCreatedBy();
        $sendMessageToMainUser = $company->getMainUserAllSees();

        $emailSendSuccess = array();

        $companyConnectedUsers = $this->em->getRepository('MetalUsersBundle:User')
            ->getEmployeesForTerritory($company, $demand->getCity());
        foreach ($companyConnectedUsers as $companyConnectedUser) {
            if (isset($emailSendSuccess[$companyConnectedUser->getEmail()])) {
                continue;
            }

            if ($companyConnectedUser->isMainUserForCompany()) {
                $sendMessageToMainUser = false;
            }

            try {
                $this->mailer->sendMessage(
                    'MetalDemandsBundle::emails/private_demand_creation.html.twig',
                    array($companyConnectedUser->getEmail() => $companyConnectedUser->getFullName()),
                    array(
                        'demand' => $demand,
                        'user' => $companyConnectedUser,
                        'country' => $demand->getCity()->getCountry()
                    ),
                    null,
                    $replyTo
                );

                $emailSendSuccess[$companyConnectedUser->getEmail()] = true;
            } catch (\Swift_RfcComplianceException $e) {
            }
        }

        $companyCity = $this->em->getRepository('MetalCompaniesBundle:CompanyCity')
            ->findOneBy(array('company' => $company, 'city' => $demand->getCity()));
        if ($companyCity && $companyCity->getEmail() && !isset($emailSendSuccess[$companyCity->getEmail()])) {
            $sendMessageToMainUser = $mainUser->getEmail() !== $companyCity->getEmail() && $company->getMainUserAllSees();
            try {
                $this->mailer->sendMessage(
                    'MetalDemandsBundle::emails/private_demand_creation.html.twig',
                    array($companyCity->getEmail()),
                    array(
                        'demand' => $demand,
                        'user' => null,
                        'country' => $demand->getCity()->getCountry()
                    ),
                    null,
                    $replyTo
                );
            } catch (\Swift_RfcComplianceException $e) {
            }
        }

        if ($sendMessageToMainUser) {
            try {
                $this->mailer->sendMessage(
                    'MetalDemandsBundle::emails/private_demand_creation.html.twig',
                    array($mainUser->getEmail() => $mainUser->getFullName()),
                    array(
                        'demand' => $demand,
                        'user' => $mainUser,
                        'country' => $demand->getCity()->getCountry()
                    ),
                    null,
                    $replyTo
                );
            } catch (\Swift_RfcComplianceException $e) {
            }
        }
    }

    protected function sendDemandConfirmationEmail(AbstractDemand $demand)
    {
        try {
            $this->mailer->sendMessage(
                'MetalDemandsBundle::emails/demand_creation_confirmation.html.twig',
                array($demand->getPossibleUser()->getEmail() => $demand->getPossibleUser()->getFullName()),
                array(
                    'demand' => $demand,
                    'user' => $demand->getPossibleUser(),
                    'country' => $demand->getPossibleUser()->getCountry()
                )
            );
        } catch (\Swift_RfcComplianceException $e) {
        }
    }

    /**
     * @param array $data Required keys: demandItemTitle, phone, sourceTypeId. Optional keys: city, category, callbackId.
     * @param User $user
     *
     * @return Demand
     */
    public function createSimplePublicDemand(Request $request, array $data, User $user = null)
    {
        $demand = new Demand();
        $demand->setCity($data['city']);
        $demand->setPhone($data['phone']);
        $demand->setSourceTypeId($data['sourceTypeId']);
        $demand->setUser($user);

        if ($data['callback']) {
            $demand->setFromCallback($data['callback']);
        }

        if (!empty($data['demandItemTitle'])) {
            $demandItem = new DemandItem();
            $demandItem->setTitle($data['demandItemTitle']);
            $demandItem->setCategory($data['category']);
            if ($data['callback']) {
                $demandItem->setVolumeTypeId($data['callback']->getVolumeTypeId());
                $demandItem->setVolume($data['callback']->getVolume());
            }

            $demand->addDemandItem($demandItem);
        }

        $demand->populateDataFromRequest($request);

        $this->em->persist($demand);
        $this->em->flush();

        $demand->updateCategories();

        return $demand;
    }
}
