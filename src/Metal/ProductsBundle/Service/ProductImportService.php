<?php

namespace Metal\ProductsBundle\Service;

use Doctrine\ORM\EntityManager;
use Liuggio\ExcelBundle\Factory;
use Metal\CompaniesBundle\Entity\Company;
use Metal\CompaniesBundle\Entity\CompanyCity;
use Metal\ProductsBundle\Entity\Product;
use Metal\ProductsBundle\Entity\ProductDescription;
use Metal\ProductsBundle\Entity\ProductImage;
use Metal\ProductsBundle\Entity\ProductLog;
use Metal\ProductsBundle\Entity\ValueObject\CurrencyProvider;
use Metal\ProductsBundle\Entity\ValueObject\ProductMeasureProvider;
use Metal\ProductsBundle\Form\ProductsImportType;
use Metal\ProjectBundle\Exception\FormValidationException;
use Metal\UsersBundle\Entity\User;
use Sonata\NotificationBundle\Backend\BackendInterface;
use Symfony\Component\Form\FormError;
use Symfony\Component\Form\FormFactoryInterface;
use Symfony\Component\HttpFoundation\File\UploadedFile;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Validator\Constraints as Assert;
use Symfony\Component\Validator\Validator\ValidatorInterface;

class ProductImportService
{
    const MODE_PRIVATE_OFFICE = 1;
    const MODE_ADMIN = 2;

    /**
     * @var EntityManager
     */
    protected $em;

    /**
     * @var FormFactoryInterface
     */
    private $formFactory;

    private $phpexcel;

    private $queueBackend;

    private $uploadDir;

    /**
     * @var ValidatorInterface
     */
    private $validator;

    public function __construct(
        EntityManager $em,
        FormFactoryInterface $formFactory,
        Factory $phpexcel,
        BackendInterface $queueBackend,
        ValidatorInterface $validator,
        $uploadDir
    )
    {
        $this->em = $em;
        $this->validator = $validator;
        $this->formFactory = $formFactory;
        $this->phpexcel = $phpexcel;
        $this->queueBackend = $queueBackend;
        $this->uploadDir = $uploadDir;
    }

    public function importProductsFromExcel(Request $request, Company $company, $user = null, $isPrivateOffice = true, $mode = self::MODE_PRIVATE_OFFICE)
    {
        set_time_limit(600);
        ini_set('memory_limit', '1024M');

        $form = $this->formFactory->create(new ProductsImportType(), null, array('company_id' => $company->getId(), 'is_private_office' => $isPrivateOffice, 'xls' => true));
        $form->handleRequest($request);

        if (!$form->isValid()) {
            throw new FormValidationException($form);
        }

        $fileFromUser = $form->get('attachment')->getData();
        /* @var $fileFromUser UploadedFile */

        \PHPExcel_Settings::setLibXmlLoaderOptions(LIBXML_DTDLOAD | LIBXML_DTDATTR | LIBXML_COMPACT | LIBXML_PARSEHUGE);

        $fileExtension = substr($fileFromUser->getClientOriginalName(), strrpos($fileFromUser->getClientOriginalName(), '.') + 1);
        $fileName = $company->getId().'-'.date('Y-m-d H-i-s').'.'.$fileExtension;
        $dir = $this->uploadDir.'/products-import';

        $fileFromUser->move($dir, $fileName);

        $errorMessage = null;
        set_error_handler(function ($errno, $errstr) use (&$errorMessage) {
            //TODO: remove this hack when https://github.com/PHPOffice/PHPExcel/issues/434 issue would be fixed
            if ($errstr !== 'Undefined variable: options') {
                $errorMessage = $errstr;
            }
        });
        $phpExcelObject = $this->phpexcel->createPHPExcelObject($dir.'/'.$fileName);
        $objWorksheet = $phpExcelObject->getActiveSheet();
        restore_error_handler();

        if ($errorMessage) {
            $form->get('attachment')->addError(new FormError('Загрузите файл в формате xls/xlsx. Ошибка "{{ error_message }}"', null, array('{{ error_message }}' => $errorMessage)));

            throw new FormValidationException($form);
        }

        $maxCountProductsForCompany = $this->em->getRepository('MetalProductsBundle:Product')
            ->getAvailableAddProductsCountToCompany($company);

        switch ($mode) {
            case self::MODE_PRIVATE_OFFICE:
                $results = $this->processWorksheetPrivateOffice($objWorksheet);
                break;
            case self::MODE_ADMIN:
                $results = $this->processWorksheetAdmin($objWorksheet);
                break;
            default:
                throw new \InvalidArgumentException('Bad import mode.');
        }

        return $this->process($results, $user, $form->get('branchOffice')->getData(), $maxCountProductsForCompany);
    }

    public function importProductsFromYml(Request $request, Company $company, User $user = null)
    {
        set_time_limit(600);

        $form = $this->formFactory->create(new ProductsImportType(), null, array('company_id' => $company->getId(), 'is_private_office' => true, 'yml' => true));
        $form->handleRequest($request);

        if (!$form->isValid()) {
            throw new FormValidationException($form);
        }

        $url = $form->get('ymlUrl')->getData();

        //TODO: use buzz
        $curl = curl_init();
        curl_setopt($curl, CURLOPT_URL, $url);
        curl_setopt($curl, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($curl, CURLOPT_FAILONERROR, true);
        curl_setopt($curl, CURLOPT_AUTOREFERER, true);
        curl_setopt($curl, CURLOPT_FOLLOWLOCATION, true);
        curl_setopt($curl, CURLOPT_SSL_VERIFYPEER, false);
        curl_setopt($curl, CURLOPT_USERAGENT, 'Mozilla/5.0 (X11; Linux x86_64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/51.0.2704.63 Safari/537.36');

        $cookieFile = tempnam(sys_get_temp_dir(), 'cookies');
        curl_setopt($curl, CURLOPT_COOKIEJAR, $cookieFile);
        curl_setopt($curl, CURLOPT_COOKIEFILE, $cookieFile);
        $content = curl_exec($curl);
        $error = curl_error($curl);

        unlink($cookieFile);

        if ($error) {
            $form->get('ymlUrl')->addError(new FormError(sprintf('Невозможно скачать файл (%s)', $error)));

            throw new FormValidationException($form);
        }

        $xml = simplexml_load_string($content);
        unset($content);

        $products = array();
        foreach ($xml->shop->offers->offer as $offer) {
            $productRow['title'] = (string)$offer->name;
            $productRow['description'] = (string)$offer->description;
            $productRow['price'] = (string)$offer->price;
            $productRow['currency'] = (string)$offer->currencyId;
            $productRow['imageUrl'] = (string)$offer->picture;
            $productRow['externalUrl'] = (string)$offer->url;
            //TODO: хардкод. нужно придумать что делать со следующими колонками. в файле импорта их нету
            $productRow['measure'] = '-';
            $productRow['size'] = '';
            $productRow['position'] = 0;
            $products[] = $productRow;
        }

        $maxCountProductsForCompany = $this->em->getRepository('MetalProductsBundle:Product')
            ->getAvailableAddProductsCountToCompany($company);

        return $this->process($products, $user, $form->get('branchOffice')->getData(), $maxCountProductsForCompany);
    }

    public function process(array $results, User $user, CompanyCity $branchOffice, $maxProductsCount)
    {
        $products = array();
        $productsImages = array();
        $resultErrors = array();
        $resultWarnings = array();
        $queueBackendData = array('insert_product_changes' => array(), 'products_reindex_ids' => array());
        $countNewProducts = 0;
        $countUpdatedProducts = 0;

        $productRepository = $this->em->getRepository('MetalProductsBundle:Product');
        $productImageRepository = $this->em->getRepository('MetalProductsBundle:ProductImage');
        $commitAll = function () {
            $this->em->flush();
            // еще раз вызываем flush для того, что б заинсертились данные в product_log/product_description
            $this->em->flush();
            $this->em->clear(Product::class);
            $this->em->clear(ProductDescription::class);
            $this->em->clear(ProductLog::class);
            $this->em->clear(ProductImage::class);
        };

        /**
         * @param Product[] $products
         */
        $collectConsumerData = function (array $products) use (&$queueBackendData) {
            foreach ($products as $product) {
                $queueBackendData['insert_product_changes'][] = array(
                    'product_id' => $product->getId(),
                    'company_id' => $product->getCompany()->getId(),
                    'is_added' => $product->getAttribute('is_added')
                );

                if ($product->getAttribute('schedule_reindex')) {
                    $queueBackendData['products_reindex_ids'][] = $product->getId();
                }
            }
        };

        $company = $branchOffice->getCompany();
        $availableCurrencies = CurrencyProvider::getAllTypes();
        $defaultCurrency = $company->getCountry()->getCurrency();

        $urlConstraint = new Assert\Url();
        $lengthConstraint = new Assert\Length(array('max' => 400));

        foreach ($results as $rowNum => $item) {
            $currentErrors = array();
            $scheduleReindex = false;
            if (!$item['title']) {
                $currentErrors[] = 'Название товара обязательно';
            } elseif (mb_strlen($item['title']) > 120) {
                $currentErrors[] = 'Название товара не должно превышать 120 символов';
            }

            $measure = null;
            if (empty($item['measure'])) {
                // если нет цены или цена договорная - единицы измерения опциональны
                if (!$item['price'] || (is_string($item['price']) && false !== mb_stripos($item['price'], 'дог'))) {
                    $measure = ProductMeasureProvider::create(ProductMeasureProvider::WITHOUT_VOLUME);
                } else {
                    $currentErrors[] = 'Не заданы единицы измерения';
                }
            } else {
                $measure = ProductMeasureProvider::createByPattern($item['measure']);
                if (!$measure) {
                    $currentErrors[] = 'Неверно заданы единицы измерения';
                }
            }

            if ($item['price'] < 0) {
                $currentErrors[] = 'Цена должна быть не меньше 0';
            }

            if ($currentErrors) {
                $resultErrors[$rowNum] = $currentErrors;
                continue;
            }

            $itemHash = Product::calculateItemHash($branchOffice->getId(), $item['title'], $item['size']);

            if (isset($products[$itemHash])) {
                $product = $products[$itemHash];
            } else {
                $product = $productRepository->loadProductForEditing($itemHash);

                if ($product) {
                    $products[$itemHash] = $product;
                }
            }

            $isAdded = true;
            if ($product) {
                if ($product->isDeleted()) {
                    if ($maxProductsCount !== null && $maxProductsCount <= 0) {
                        $resultErrors[$rowNum] = array(sprintf('Полный и расширенный пакеты позволяют добавлять более %d товаров.', $company->getPackageChecker()->getMaxAvailableProductsCount()));
                        continue;
                    }
                    $maxProductsCount = $this->decrementCount($maxProductsCount);
                    $product->setChecked(Product::STATUS_NOT_CHECKED);
                } else {
                    $isAdded = false;
                }

            } else {
                if ($maxProductsCount !== null && $maxProductsCount <= 0) {
                    $resultErrors[$rowNum] = array(sprintf('Полный и расширенный пакеты позволяют добавлять более %d товаров.', $company->getPackageChecker()->getMaxAvailableProductsCount()));
                    continue;
                }
                $maxProductsCount = $this->decrementCount($maxProductsCount);
                $product = new Product();
                $product->setBatchInsertingMode(true);
                $products[$itemHash] = $product;
                $product->setTitle($item['title']);
                if ($item['size']) {
                    $product->setSize($item['size']);
                }
            }

            if (!empty($item['externalUrl'])) {
                $imageValidate = $this->validator->validate($item['externalUrl'], array($lengthConstraint, $urlConstraint));
                foreach ((array) $imageValidate as $valid) {
                    foreach ((array) $valid as $validEl) {
                        $resultErrors[$rowNum][] = $validEl->getMessage();
                    }
                }

                if (0 === count($imageValidate)) {
                    $product->setExternalUrl($item['externalUrl']);
                }
            }

            if (!$product->getCategory()) {
                $product->setChecked(Product::STATUS_PENDING_CATEGORY_DETECTION);
            }

            $oldPrice = $product->getPrice();
            $product->setIsPriceFrom(false);
            if ($item['price']) {
                $product->setPrice($item['price']);
                if ($item['price'] != $oldPrice) {
                    $scheduleReindex = true;
                }
            } elseif (isset($item['priceFrom'])) {
                $product->setPrice($item['priceFrom']);
                $product->setIsPriceFrom(true);
                if ($item['priceFrom'] != $oldPrice) {
                    $scheduleReindex = true;
                }
            }

            if (isset($item['description'])) {
                $product->getProductDescription()->setDescription($item['description']);
            }

            if (!empty($item['imageUrl'])) {
                $imageUrl = $item['imageUrl'];
                $productImage = null;

                if (isset($productsImages[$imageUrl])) {
                    $productImage = $productsImages[$imageUrl];
                } else {
                    $productImage = $productImageRepository->findOneBy(array('company' => $company, 'url' => $imageUrl));
                }

                $errorList = $this->validator->validate($imageUrl, $urlConstraint);
                $imageUrlValid = 0 === count($errorList);

                if (!$imageUrlValid) {
                    $resultWarnings[$rowNum][] = 'Некорректный URL картинки.';
                } else {
                    if (!$productImage) {
                        $productImage = new ProductImage();
                        $productImage->setCompany($company);
                        $productImage->setDescription($product->getTitle());
                        $productImage->setUrl($imageUrl);

                        $this->em->persist($productImage);
                        $productsImages[$imageUrl] = $productImage;
                    }

                    $product->setImage($productImage);
                }
            }

            $currency = $defaultCurrency;
            if (isset($item['currency'])) {
                if (isset($availableCurrencies[$item['currency']])) {
                    $currency = $availableCurrencies[$item['currency']];
                } elseif ($fallbackCurrency = CurrencyProvider::getTypeByTokenEn($item['currency'])) {
                    $currency = $fallbackCurrency;
                }
            }

            $oldPosition = $product->getPosition();
            if (isset($item['position']) && (int)$item['position'] > 0) {
                $product->setIsSpecialOffer(true);
                $product->setPosition((int)$item['position']);
            } else {
                $product->setPosition(1);
                $product->setIsSpecialOffer(false);
            }

            if ($oldPosition != $product->getPosition()) {
                $scheduleReindex = true;
            }

            $product->setCurrency($currency);
            $product->setMeasure($measure);
            $product->setCompany($company);
            $product->setBranchOffice($branchOffice);

            if ($product->getId()) {
                $product->getProductLog()->setUpdatedBy($user);
            } else {
                $product->getProductLog()->setCreatedBy($user);
            }

            $product->setUpdated();

            if ($isAdded) {
                $this->em->persist($product);
                $countNewProducts++;
            } else {
                $countUpdatedProducts++;
            }

            if (($rowNum % 50) == 0) {
                $commitAll();
                $collectConsumerData($products);
                $products = array();
                $productsImages = array();
            }

            if ($scheduleReindex) {
                $product->setAttribute('schedule_reindex', true);
            }

            $product->setAttribute('is_added', $isAdded);
        }

        $company->getCounter()->setProductsUpdatedAt(new \DateTime());

        $commitAll();
        $collectConsumerData($products);

        if ($countNewProducts > 0) {
            $this->em->getRepository('MetalCompaniesBundle:CompanyCounter')->changeCounter($company, array('allProductsCount'), $countNewProducts);
        }

        $this->queueBackend->createAndPublish('products_import', $queueBackendData);

        return compact('resultErrors', 'resultWarnings', 'countNewProducts', 'countUpdatedProducts');
    }

    protected function processWorksheetPrivateOffice(\PHPExcel_Worksheet $objWorksheet)
    {
        $cellsKeys = array(
            'A' => 'title',
            'B' => 'description',
            'C' => 'size',
            'D' => 'price',
            'E' => 'currency',
            'F' => 'measure',
            'G' => 'priceFrom',
            'H' => 'imageUrl',
            'I' => 'externalUrl',
            'J' => 'position'
        );

        $results = array();
        foreach ($objWorksheet->getRowIterator(7) as $k => $row) {
            $cellIterator = $row->getCellIterator();
            $cellIterator->setIterateOnlyExistingCells(false);

            $parsedRow = array();
            $isEmpty = true;

            foreach ($cellIterator as $letter => $cell) {
                if (!isset($cellsKeys[$letter])) {
                    continue;
                }

                $value = $cell->getCalculatedValue();
                $cellKey = $cellsKeys[$letter];

                if (!in_array($cellKey, array('imageUrl', 'externalUrl'))) {
                    $parsedRow[$cellKey] = Product::normalizeTitle($value, $cellKey !== 'description');
                } else {
                    $parsedRow[$cellKey] = $value;
                }

                if ($value) {
                    $isEmpty = false;
                }
            }

            if (!$isEmpty) {
                $results[$k] = $parsedRow;
            }
        }

        return $results;
    }

    protected function processWorksheetAdmin(\PHPExcel_Worksheet $objWorksheet)
    {
        $cellsKeys = array(
            'A' => 'title',
            'B' => 'size',
            'C' => 'price',
            'D' => 'measure'
        );

        $results = array();
        foreach ($objWorksheet->getRowIterator(2) as $k => $row) {
            $cellIterator = $row->getCellIterator();
            $cellIterator->setIterateOnlyExistingCells(false);

            $parsedRow = array();
            $isEmpty = true;

            foreach ($cellIterator as $letter => $cell) {
                if (!isset($cellsKeys[$letter])) {
                    continue;
                }
                $value = $cell->getCalculatedValue();
                $parsedRow[$cellsKeys[$letter]] = Product::normalizeTitle($value);
                if ($value) {
                    $isEmpty = false;
                }
            }

            if (!$isEmpty) {
                $results[$k] = $parsedRow;
            }
        }

        return $results;
    }

    private function decrementCount($count)
    {
        if ($count !== null && $count > 0) {
            $count--;
        }

        return $count;
    }
}
