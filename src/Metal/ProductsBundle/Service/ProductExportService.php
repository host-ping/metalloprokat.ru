<?php

namespace Metal\ProductsBundle\Service;

use Liuggio\ExcelBundle\Factory;

use Doctrine\ORM\EntityManager;
use Metal\CompaniesBundle\Entity\Company;
use Metal\ProductsBundle\Entity\Product;
use Metal\ProductsBundle\Entity\ValueObject\CurrencyProvider;
use Metal\ProductsBundle\Entity\ValueObject\ProductMeasureProvider;
use Metal\ProjectBundle\Doctrine\Utils;
use Metal\ProjectBundle\Helper\ImageHelper;
use Metal\ProjectBundle\Helper\UrlHelper;
use Sonata\AdminBundle\Admin\AdminInterface;
use Symfony\Component\DependencyInjection\ContainerInterface;
use Symfony\Component\HttpKernel\Exception\HttpException;
use Symfony\Component\Routing\Generator\UrlGeneratorInterface;

class ProductExportService
{
    /**
     * @var EntityManager
     */
    protected $em;

    /**
     * @var Factory
     */
    private $phpexcel;

    private $uploadDir;

    /**
     * @var ImageHelper
     */
    private $imageHelper;

    /**
     * @var UrlHelper
     */
    private $urlHelper;

    private $router;

    private $blankXls;

    public function __construct(EntityManager $em, Factory $phpexcel, UrlGeneratorInterface $router, ContainerInterface $container, $uploadDir)
    {
        $this->em = $em;
        $this->phpexcel = $phpexcel;
        $this->router = $router;
        $this->imageHelper = $container->get('brouzie.helper_factory')->get('MetalProjectBundle:Image');
        $this->urlHelper = $container->get('brouzie.helper_factory')->get('MetalProjectBundle:Url');
        $this->uploadDir = $uploadDir;

        $this->blankXls = $container->getParameter('web_dir').'/'.$container->getParameter('products_import_blank_file');
    }

    /**
     * @param string $exportFormat Allowed formats array('xls', 'yml', 'xls-admin')
     * @param Company|null $company
     * @param array $productsIds
     * @param AdminInterface|null $admin
     *
     * @return string Filepath to download
     */
    public function exportProducts($exportFormat, Company $company = null, array $productsIds = array(), AdminInterface $admin = null)
    {
        set_time_limit(600);

        $exportData = null;

        switch ($exportFormat) {
            case 'xls':
                $exportData = $this->getDataForExport($company, $productsIds, true, $admin);
                $fileName = $this->generateXls($company, $exportData['products'], $exportData['categories'], $admin);
                break;

            case 'yml':
                $exportData = $this->getDataForExport($company, $productsIds, true, null, true);
                $fileName = $this->generateYml($company, $exportData['products'], $exportData['categories']);
                break;

            case 'xls-admin':
                $exportData = $this->getDataForExport($company, array(), false, $admin);
                $fileName = $this->generateAdminXls($exportData['products'], $admin);
                break;

            default:
                throw new HttpException(400, 'Not allowed formats.');
        }

        return $fileName;
    }

    /**
     * @param Company $company
     * @param array $products
     * @param array $categories
     * @return string
     */
    private function generateYml(Company $company = null, array $products, array $categories)
    {
        if (!$company) {
            throw new \InvalidArgumentException('Method generateYml requires company.');
        }

        $fileName = $company->getId().'-'.date('Y_m_d_H_i_s').'.yml';
        $currentDate = new \DateTime();
        $xml = new \SimpleXMLElement("<?xml version=\"1.0\" encoding=\"UTF-8\"?><yml_catalog date=\"".$currentDate->format('Y-m-d H:i')."\"></yml_catalog>");
        $shop = $xml->addChild('shop');

        $shop->addChild('name', $company->getTitle());
        $shop->addChild('company', $company->getTitle());

        if ($company->getMinisiteEnabled()) {
            $shop->addChild(
                'url',
                $this->urlHelper->generateUrl(
                    'MetalMiniSiteBundle:MiniSite:view',
                    array('domain' => $company->getDomain(), '_secure' => $company->getPackageChecker()->isHttpsAvailable()),
                    true
                )
            );
        }

        $currencyName = $company->getCountry()->getCurrency()->getTokenEn();
        $shop->addChild('currencies')->addChild('currency')->addAttribute('id', $currencyName);

        $categoriesElement = $shop->addChild('categories');
        foreach ($categories as $categoryRow) {
            $category = $categoriesElement->addChild('category', $categoryRow['title']);
            $category->addAttribute('id', $categoryRow['id']);

            if (isset($categoryRow['parentId'])) {
                $category->addAttribute('parentId', $categoryRow['parentId']);
            }
        }

        $offers = $shop->addChild('offers');
        $currencies = CurrencyProvider::getAllTypes();
        foreach ($products as $product) {
            $offer = $offers->addChild('offer');

            if (!empty($product['externalUrl'])) {
                $url = $product['externalUrl'];
            } else {
                $url = $this->router->generate(
                    'MetalProductsBundle:Product:view_subdomain',
                    array('subdomain' => $product['citySlug'], 'id' => $product['id']),
                    true
                );
            }

            $offer->addAttribute('id', $product['id']);
            $offer->addChild('url', $url);
            $offer->addChild('price', $product['isContractPrice'] ? 0 : $product['price']);
            $offer->addChild('currencyId', $currencies[$product['currencyId']]->getTokenEn());
            $offer->addChild('categoryId', $product['categoryId']);
            if ($product['imageName']) {
                $imageData = array(
                    'imageName'      => $product['imageName'],
                    'imageCompanyId' => $product['imageCompanyId'],
                    'mimeType'       => $product['mimeType'],
                    'optimized'      => $product['optimized'],
                    'downloaded'     => $product['downloaded'],
                    'imageUrl'       => $product['imageUrl']
                );
                $imageUrl = $this->imageHelper->getPhotoUrlForImageData($imageData, 'sq600');
                $offer->addChild('picture', $imageUrl);
            }
            $offer->addChild('name', $product['title']);
            $offer->addChild('vendor', $company->getTitle());
            $offer->addChild('description', $product['description']);
        }

        $xml->saveXML($this->uploadDir.'/products-export/'.$fileName);

        return $fileName;
    }

    /**
     * @param Company $company
     * @param array $products
     * @param AdminInterface|null $admin
     *
     * @return string
     * @throws \PHPExcel_Exception
     */
    private function generateXls(Company $company = null, array $products, array $categories, AdminInterface $admin = null)
    {
        if ($admin) {
            $fileName = sprintf(
                'export_%s_%s.xls',
                strtolower(substr($admin->getClass(), strripos($admin->getClass(), '\\') + 1)),
                date('Y_m_d_H_i_s')
            );
        } elseif ($company) {
            $fileName = $company->getId().'-'.date('Y-m-d_H-i-s').'.xls';
        } else {
            throw new \InvalidArgumentException('Method generateXls requires company or admin.');
        }

        $catArr = array();
        foreach ($categories as $categoryRow) {
            $catArr[$categoryRow['id']] = $categoryRow['title'];
        }

        $objPHPExcel = $this->phpexcel->createPHPExcelObject($this->blankXls);

        $rowNum = 7;
        $activeSheet = $objPHPExcel->setActiveSheetIndex(0);
        //Удаляем 50 строк начиная с $rowNum
        $activeSheet->removeRow($rowNum, 50);
        $measuresList = ProductMeasureProvider::getAllTypesAsSimpleArray();

        foreach ($products as $i => $product) {
            $activeSheet
                ->setCellValue('A'.$rowNum, $product['title'])
                ->setCellValue('B'.$rowNum, $product['description'])
                ->setCellValueExplicit('C'.$rowNum, $product['size'], \PHPExcel_Cell_DataType::TYPE_STRING);

            $activeSheet
                ->setCellValue('E'.$rowNum, $product['currencyId'])
                ->setCellValue('F'.$rowNum, $measuresList[$product['measureId']]);

            if ($product['isContractPrice']) {
                $activeSheet->setCellValue('D'.$rowNum, 'дог.');
            } elseif ($product['isPriceFrom']) {
                $activeSheet->setCellValue('G'.$rowNum, $product['price']);
            } else {
                $activeSheet->setCellValue('D'.$rowNum, $product['price']);
            }

            if (!empty($product['externalUrl'])) {
                $activeSheet->setCellValue('I'.$rowNum, $product['externalUrl']);
            }

            if (!empty($product['imageUrl'])) {
                $activeSheet->setCellValue('H'.$rowNum, $product['imageUrl']);
            }

            $activeSheet->setCellValue('J'.$rowNum, $product['isSpecialOffer'] ? $product['position'] : 0);

            if (array_key_exists($product['categoryId'], $catArr)) {
                $activeSheet->setCellValue('K'.$rowNum, $catArr[$product['categoryId']]);
            }

            $rowNum++;
            unset($products[$i]);
        }

        $objWriter = $this->phpexcel->createWriter($objPHPExcel);
        $objWriter->save($this->uploadDir.'/products-export/'.$fileName);

        return $fileName;
    }

    /**
     * @param AdminInterface $admin
     * @param array $products
     *
     * @return string
     * @throws \PHPExcel_Exception
     */
    private function generateAdminXls(array $products, AdminInterface $admin)
    {
        $fileName = sprintf(
            'export_%s_%s.xls',
            strtolower(substr($admin->getClass(), strripos($admin->getClass(), '\\') + 1)),
            date('Y_m_d_H_i_s')
        );

        $objPHPExcel = $this->phpexcel->createPHPExcelObject($this->uploadDir.'/import-export-blanks/products-admin.xls');

        $activeSheet = $objPHPExcel->setActiveSheetIndex(0);
        $measuresList = ProductMeasureProvider::getAllTypesAsSimpleArray();

        $rowNum = 2;
        foreach ($products as $i => $product) {
            $activeSheet
                ->setCellValue('A'.$rowNum, $product['title'])
                ->setCellValueExplicit('B'.$rowNum, $product['size'], \PHPExcel_Cell_DataType::TYPE_STRING)
                ->setCellValue('C'.$rowNum, $product['isContractPrice'] ? '' : $product['price'])
                ->setCellValue('D'.$rowNum, $measuresList[$product['measureId']]);
            $rowNum++;
            unset($products[$i]);
        }

        $objWriter = $this->phpexcel->createWriter($objPHPExcel);
        $objWriter->save($this->uploadDir.'/products-export/'.$fileName);

        return $fileName;
    }

    /**
     * @param Company|null $company
     * @param array $productsIds
     * @param bool $selectCategories
     * @param AdminInterface|null $admin
     * @param bool $selectCity
     *
     * @return array
     */
    private function getDataForExport(Company $company = null, array $productsIds = array(), $selectCategories = false, AdminInterface $admin = null, $selectCity = false)
    {
        Utils::checkEmConnection($this->em);

        if ($admin) {
            $qb = $admin->getDatagrid()->getQuery()->getQueryBuilder();
            $qb->setMaxResults(null)->setFirstResult(null);
        } else {
            $qb = $this->em->createQueryBuilder()->from('MetalProductsBundle:Product', 'o', 'o.id');
        }

        if (!$admin && !$productsIds && !$company) {
            throw new \InvalidArgumentException('Method getDataForExport requires company or productsIds or admin.');
        }

        $qb->select('o.id, o.title, o.size, o.price, o.isPriceFrom, o.isContractPrice, o.isSpecialOffer, o.position, o.measureId, o.currencyId, pd.description')
            ->addSelect('image.photo.name AS imageName, image.companyId AS imageCompanyId, image.photo.mimeType AS mimeType, image.optimized, image.downloaded, image.url AS imageUrl')
            ->addSelect('o.externalUrl')
            ->join('o.productDescription', 'pd')
            ->leftJoin('o.image', 'image')
            ->andWhere('o.isVirtual = false')
        ;

        if ($selectCity) {
            $qb->addSelect('city.slug AS citySlug')
                ->join('o.branchOffice', 'branchOffice')
                ->join('branchOffice.city', 'city');
        }

        if ($selectCategories) {
            $qb->addSelect('cat.id AS categoryId');
            $qb->leftJoin('o.category', 'cat');
        }

        if ($productsIds) {
            $qb->andWhere('o.id IN (:products_id)')
                ->setParameter('products_id', $productsIds);
        }

        if ($company) {
            $qb->andWhere('o.company = :company_id')
                ->setParameter('company_id', $company->getId());
        }

        $unsortedProducts = $qb->getQuery()->getResult();

        $products = array();
        $categories = array();

        if ($admin) {
            $products = $unsortedProducts;
        } else {
            $categoriesIds = array();
            // preserve same order that was on input
            foreach ($productsIds as $productId) {
                if (isset($unsortedProducts[$productId])) {
                    if ($selectCategories) {
                        $categoriesIds[$unsortedProducts[$productId]['categoryId']] = true;
                    }
                    $products[] = $unsortedProducts[$productId];
                    unset($unsortedProducts[$productId]);
                }
            }
            unset($unsortedProducts);

            if ($categoriesIds) {
                Utils::checkEmConnection($this->em);
                $categories = $this->em->getRepository('MetalCategoriesBundle:Category')->getSimpleCategoriesByLevels(array_keys($categoriesIds));
            }
        }

        return array('products' => $products, 'categories' => $categories);
    }
}
