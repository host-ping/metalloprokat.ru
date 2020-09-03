<?php

namespace Metal\ContentBundle\Command;

use Doctrine\ORM\EntityManager;
use Metal\CategoriesBundle\Entity\Category;
use Metal\CompaniesBundle\Entity\Company;
use Metal\CompaniesBundle\Entity\CompanyCategory;
use Metal\CompaniesBundle\Service\CompanyService;
use Metal\ContentBundle\Entity\ParserCategoryAssociate;
use Metal\ContentBundle\Entity\ParserCompanyToCategory;
use Metal\ContentBundle\Entity\UserRegistrationWithParser;
use Metal\ContentBundle\Parsers\ParserInterface;
use Metal\UsersBundle\Entity\User;
use Metal\UsersBundle\Entity\UserCounter;
use Metal\UsersBundle\Service\RegistrationService;
use Symfony\Bundle\FrameworkBundle\Command\ContainerAwareCommand;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;


class ParseCompaniesCommand extends ContainerAwareCommand
{
    const MAX_DUPLICATES = 50;
    const MAX_PROCESS_PAGE = 2;

    protected function configure() 
    {
        $this->setName('metal:content:parse-companies');
        $this->addOption('code', null, InputOption::VALUE_REQUIRED);
        $this->addOption('page', null, InputOption::VALUE_OPTIONAL);
        $this->addOption('force', null, InputOption::VALUE_NONE);
    }

    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $output->writeln(sprintf('%s: Start command "%s"', date('d.m.Y H:i:s'), $this->getName()));

        if (!$input->getOption('code')) {
            throw new \InvalidArgumentException('Code is required.');
        }
        
        $parserId = sprintf('metal.content.parsers.%s', $input->getOption('code'));
        if (!$this->getContainer()->has($parserId)) {
            throw new \RuntimeException(sprintf('Grabber "%s" does not exist.', $parserId));
        }

        $parser = $this->getContainer()->get($parserId);
        /* @var $parser ParserInterface */
        
        $registrationService = $this->getContainer()->get('metal.users.registration_services');
        /* @var $registrationService RegistrationService */

        $em = $this->getContainer()->get('doctrine.orm.default_entity_manager');
        /* @var $em EntityManager */

        $userRepository = $em->getRepository('MetalUsersBundle:User');

        $categoryRepository = $em->getRepository('MetalCategoriesBundle:Category');

        $parserCategoryAssociateRepository = $em->getRepository('MetalContentBundle:ParserCategoryAssociate');

        $companyCategoryRepository = $em->getRepository('MetalCompaniesBundle:CompanyCategory');
        $userRegistrationWithParserRepository = $em->getRepository('MetalContentBundle:UserRegistrationWithParser');

        $userRegistration = $userRegistrationWithParserRepository->findOneBy(array('siteCode' => $input->getOption('code')), array('createdAt' => 'DESC'));

        if ($userRegistration) {
            if (new \DateTime('-5 minutes') < $userRegistration->getCreatedAt()) {
                $output->writeln(sprintf('%s: Command is already process.', date('d.m.Y H:i:s')));
                return;
            }
        }

        if (!$input->getOption('page')) {
            $lastPage = $userRegistrationWithParserRepository->findOneBy(array('siteCode' => $input->getOption('code')), array('page' => 'DESC'));
            if ($lastPage) {
                $input->setOption('page', $lastPage->getPage());
                $output->writeln(sprintf('%s: Set last page %d.', date('d.m.Y H:i:s'), $lastPage->getPage()));
            }
        }

        $pageNum = $input->getOption('page') ?: 1;

        $encoderFactory = $this->getContainer()->get('security.encoder_factory');

        $buzz = $this->getContainer()->get('buzz.browser.grabber');

        $buzz->getClient()->setTimeout(10);
        
        $companyIsAlreadyCount = 0;
        try {
            do {

                if ($input->getOption('page')) {
                    $parser->setNextPage($input->getOption('page'));
                    $input->setOption('page', null);
                }

                try {
                    $mainContent = $buzz->get($parser->getCatalogUrl())->getContent();
                } catch (\Exception $e) {
                    $output->writeln(sprintf('%s: Error get page "%s" sleep 20 seconds.', date('d.m.Y H:i:s'), $e->getMessage()));
                    sleep(20);
                    $mainContent = $buzz->get($parser->getCatalogUrl())->getContent();
                }

                $parser->initMainCrawler($mainContent);

                $output->writeln(sprintf('%s: Current page "%s".', date('d.m.Y H:i:s'), $parser->getCurrentPage()));
                $postsLinks = $parser->getPostsLinks();

                foreach ($postsLinks as $postLink) {

                    try {
                        $postContent = $buzz->get($postLink)->getContent();
                    } catch (\Exception $e) {
                        $output->writeln(sprintf('%s: Error get content "%s" sleep 20 seconds.', date('d.m.Y H:i:s'), $e->getMessage()));
                        sleep(20);
                        $postContent = $buzz->get($postLink)->getContent();
                    }

                    $parser->initPostCrawler($postContent);

                    if (!$input->getOption('force') && $companyIsAlreadyCount > self::MAX_DUPLICATES) {
                        break 2;
                    }

                    $sleep = $parser->getSleep();
                    $output->writeln(sprintf('%s: Sleep "%d" seconds.', date('d.m.Y H:i:s'), $sleep));
                    sleep($sleep);

                    if (!$parser->getEmail()) {
                        $output->writeln(sprintf('%s: Email not found.', date('d.m.Y H:i:s')));
                        continue;
                    }

                    if ($userRepository->findBy(array('email' => $parser->getEmail()))) {
                        $output->writeln(sprintf('%s: The user email "%s" is already registered.', date('d.m.Y H:i:s'), $parser->getEmail()));
                        $companyIsAlreadyCount++;
                        continue;
                    }

                    if ($parser->getPhone() && $userRepository->findBy(array('phone' => $parser->getPhone()))) {
                        $output->writeln(sprintf('%s: The user phone "%s" is already registered.', date('d.m.Y H:i:s'), $parser->getPhone()));
                        $companyIsAlreadyCount++;
                        continue;
                    }

                    if (!$city = $em->getRepository('MetalTerritorialBundle:City')->findOneBy(array('title' => $parser->getCity()))) {
                        $output->writeln(sprintf('%s: City "%s" not match..', date('d.m.Y H:i:s'), $parser->getCity()));
                        continue;
                    }

                    if (($parser->getCurrentPage() - $pageNum) > self::MAX_PROCESS_PAGE) {
                        $output->writeln(sprintf('%s: Processed %d pages, exit...', date('d.m.Y H:i:s'), $parser->getCurrentPage() - $pageNum));
                        break 2;
                    }

                    $user = new User();
                    $user->setCountry($city->getCountry());
                    $user->cityTitle = $city->getTitle();
                    $user->setCity($city);

                    $output->writeln(sprintf('%s: City title "%s".', date('d.m.Y H:i:s'), $user->cityTitle));

                    $user->setFullName($parser->getUserName()); //TODO: Придумать что сюда писать
                    $output->writeln(sprintf('%s: Set username "%s".', date('d.m.Y H:i:s'), $parser->getUserName()));
                    $user->setPhone($parser->getPhone());
                    $output->writeln(sprintf('%s: Set phone "%s".', date('d.m.Y H:i:s'), $parser->getPhone()));
                    $user->setCompanyTitle($parser->getTitle());
                    $output->writeln(sprintf('%s: Set company title "%s".', date('d.m.Y H:i:s'), $parser->getTitle()));
                    $user->setEmail($parser->getEmail());
                    $output->writeln(sprintf('%s: Set email "%s".', date('d.m.Y H:i:s'), $parser->getEmail()));

                    $user->newPassword = User::randomPassword();
                    $encoder = $encoderFactory->getEncoder($user);
                    $password = $encoder->encodePassword($user->newPassword, $user->getSalt());
                    $user->setPassword($password);

                    $em->persist($user);

                    $company = new Company();

                    $companyTypeId = 1;
                    $companyTitle = $parser->getTitle();
                    if ($foundCompanyType = CompanyService::getCompanyTypeByTitle($parser->getTitle())) {
                        $companyTypeId = $foundCompanyType->getId();
                        $output->writeln(sprintf('%s: Found company type "%s".', date('d.m.Y H:i:s'), $foundCompanyType->getTitle()));

                        $companyTitle = preg_replace(sprintf('/(?:\W|\s|^)%s(?:\W|\s|$)/ui', $foundCompanyType->getTitle()), '', $parser->getTitle());
                        $output->writeln(sprintf('%s: Old CompanyTitle: "%s" -> New company title "%s".', date('d.m.Y H:i:s'), $parser->getTitle(), $companyTitle));
                    }

                    $output->writeln(sprintf('%s: Create new company.', date('d.m.Y H:i:s')));
                    $registrationService->createCompany($company, $user, $parser->getPhone(), trim($companyTitle, " \t\n\r\0\x0B,."), $companyTypeId, $city);

                    $em->flush();

                    $userCounter = new UserCounter();
                    $user->setCounter($userCounter);

                    $em->persist($userCounter);

                    $em->flush();

                    $categoriesTitles = $parser->getCategoriesTitles();

                    if ($categoriesTitles) {
                        $output->writeln(sprintf('%s: Get categories titles "%s."', date('d.m.Y H:i:s'), implode("\n", $categoriesTitles)));
                    }

                    $noMatchedCategories = array();
                    foreach ($categoriesTitles as $categoryTitle) {
                        $category = $categoryRepository->findOneBy(array('title' => $categoryTitle));
                        if ($category) {
                            $output->writeln(sprintf('%s: Category matched site: "%s" real: "%s".', date('d.m.Y H:i:s'), $categoryTitle, $category->getTitle()));
                            $companyCategory = new CompanyCategory();
                            $companyCategory->setCategory($category);
                            $company->addCompanyCategory($companyCategory);
                        } else {
                            $noMatchedCategories[] = $categoryTitle;
                        }
                    }

                    foreach ($noMatchedCategories as $categoryTitle) {

                        $isAssociated = false;
                        $parserCategoryAssociate = $parserCategoryAssociateRepository
                            ->findOneBy(array('title' => $categoryTitle));
                        if (!$parserCategoryAssociate) {
                            $output->writeln(sprintf('%s: Add new parser category company: %d categoryTitle "%s".',
                                    date('d.m.Y H:i:s'),
                                    $company->getId(),
                                    $categoryTitle)
                            );

                            $parserCategoryAssociate = new ParserCategoryAssociate();
                            $parserCategoryAssociate->setTitle($categoryTitle);
                            $em->persist($parserCategoryAssociate);
                        } elseif ($parserCategoryAssociate->getCategory() instanceof Category) {
                            $output->writeln(sprintf('%s: ParserCategoryAssociate with its associated : "%s".',
                                date('d.m.Y H:i:s'),
                                $parserCategoryAssociate->getCategory()->getTitle()
                            ));

                            $isAssociated = true;
                            $categories = array($parserCategoryAssociate->getCategory());
                            if (!$parserCategoryAssociate->getCategory()->getAllowProducts()) {
                                $categories = $parserCategoryAssociate->getCategory()->getChildren();
                            }
                            /* @var $categories Category[] */

                            foreach ($categories as $category) {
                                if (!$category->getAllowProducts()) {
                                    continue;
                                }

                                if ($companyCategoryRepository->findOneBy(array('company' => $company, 'category' => $category))) {
                                    continue;
                                }

                                $companyCategory = new CompanyCategory();
                                $companyCategory->setCategory($parserCategoryAssociate->getCategory());
                                $company->addCompanyCategory($companyCategory);
                            }

                        } else {
                            $parserCategoryAssociate->incrementPriority();
                            $output->writeln(sprintf('%s: Parser category "%s" found, increment priority to %d.',
                                    date('d.m.Y H:i:s'),
                                    $parserCategoryAssociate->getTitle(),
                                    $parserCategoryAssociate->getPriority())
                            );
                        }

                        if (!$isAssociated) {
                            $parserCompanyToCategory = new ParserCompanyToCategory();
                            $parserCompanyToCategory->setCompany($company);
                            $parserCompanyToCategory->setParsedCategory($parserCategoryAssociate);
                            $em->persist($parserCompanyToCategory);
                        }

                        $em->flush();
                    }

                    $output->writeln(sprintf('%s: Create UserRegistrationWithParser.', date('d.m.Y H:i:s')));
                    $userRegistrationWithParser = new UserRegistrationWithParser();
                    $userRegistrationWithParser->setCompany($company);
                    $userRegistrationWithParser->setUser($user);
                    $userRegistrationWithParser->setUrl($postLink);
                    $userRegistrationWithParser->setSiteCode($input->getOption('code'));
                    $userRegistrationWithParser->setPage($parser->getCurrentPage());
                    $em->persist($userRegistrationWithParser);
                    $em->flush();
                    $output->writeln(sprintf('%s: Company "%d" is registered.', date('d.m.Y H:i:s'), $company->getId()));
                }
            } while ($parser->hasNextPage());
        } catch (\Exception $e) {
            $output->writeln(sprintf('%s: %s , Last page %d, parser code "%s"', date('d.m.Y H:i:s'), $e->getMessage(), $parser->getCurrentPage(), $input->getOption('code')));
        }

        $output->writeln(sprintf('%s: Done command "%s"', date('d.m.Y H:i:s'), $this->getName()));
    }
}
