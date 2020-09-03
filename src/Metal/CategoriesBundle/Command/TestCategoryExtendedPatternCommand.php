<?php

namespace Metal\CategoriesBundle\Command;

use Doctrine\ORM\EntityManager;
use Metal\CategoriesBundle\Entity\Category;
use Metal\CategoriesBundle\Service\ExpressionLanguage;
use Metal\CategoriesBundle\Service\ProductCategoryDetector;
use Metal\ProductsBundle\Entity\Product;
use Symfony\Bundle\FrameworkBundle\Command\ContainerAwareCommand;
use Symfony\Component\Console\Helper\Table;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;

class TestCategoryExtendedPatternCommand extends ContainerAwareCommand
{
    private $originArray;
    private $modifyArray;
    private $minWordLength = 3;

    private $delSimbols;

    protected function configure()
    {
        $this->setName('metal:categories:test-category-extended-pattern');
        $this->addOption('product-id', null, InputOption::VALUE_OPTIONAL|InputOption::VALUE_IS_ARRAY, 'List of product identifiers to check.', array());
        $this->addOption('category-id', null, InputOption::VALUE_OPTIONAL|InputOption::VALUE_IS_ARRAY, 'The list of categories to check IDs.', array());
    }

    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $em = $this->getContainer()->get('doctrine')->getManager();
        /* @var $em EntityManager */
        $productRepository = $em->getRepository('MetalProductsBundle:Product');

        $productsIds = $input->getOption('product-id');
        $categoriesIds = $input->getOption('category-id');

        $qb = $em
            ->createQueryBuilder('category')
            ->select('category')
            ->addSelect('categoryExtended')
            ->from('MetalCategoriesBundle:Category', 'category')
            ->join('category.categoryExtended', 'categoryExtended')
            ->andWhere('categoryExtended.extendedPattern <> :empty')
            ->setParameter('empty', '')
        ;

        if ($categoriesIds) {
            $qb->andWhere('category.id IN(:categoriesIds)')
                ->setParameter('categoriesIds', $categoriesIds)
            ;
        }

        $categories = $qb->getQuery()->getResult();
        /* @var $categories Category[] */

        $qb = $em
            ->createQueryBuilder('product')
            ->select('product')
            ->addSelect('category')
            ->from('MetalProductsBundle:Product', 'product')
            ->join('product.category', 'category')
        ;

        $idFrom = 0;
        $idMax = 0;
        if (!$productsIds) {
            $idsRange = $productRepository->createQueryBuilder('product')
                ->select('MIN(product.id) AS _min')
                ->addSelect('MAX(product.id) AS _max')
                ->getQuery()
                ->getSingleResult()
            ;

            $idFrom = $idsRange['_min'];
            $idMax = $idsRange['_max'];
        }

        $delSymbols = array(
            "/\\,/ui", "/\\./ui", "/\\;/ui", "/\\:/ui", "/\"/ui", "/\\#/ui", "/\\$/ui", "/\\%/ui", "/\\^/ui",
            "/\\!/ui", "/\\@/ui", "/\\`/ui", "/\\~/ui", "/\\*/ui", "/\\-/ui", "/\\=/ui", "/\\+/ui", "/\\\\/ui",
            "/\\|/ui", "/\\>/ui", "/\\</ui", "/\\(/ui", "/\\)/ui", "/\\&/ui", "/\\?/ui", "/\t/ui",
            "/\\{/ui", "/\\//ui", "/\\[/ui", "/\\]/ui", "/\\'/ui", "/\\“/ui", "/\\”/ui", "/\\•/ui",
            "/%begin%как%end%/ui", "/%begin%для%end%/ui", "/%begin%что%end%/ui", "/%begin%или%end%/ui", "/%begin%это%end%/ui", "/%begin%этих%end%/ui",
            "/%begin%всех%end%/ui", "/%begin%вас%end%/ui", "/%begin%они%end%/ui", "/%begin%оно%end%/ui", "/%begin%еще%end%/ui", "/%begin%когда%end%/ui",
            "/%begin%где%end%/ui", "/%begin%эта%end%/ui", "/%begin%лишь%end%/ui", "/%begin%уже%end%/ui", "/%begin%вам%end%/ui", "/%begin%нет%end%/ui",
            "/%begin%если%end%/ui", "/%begin%надо%end%/ui", "/%begin%все%end%/ui", "/%begin%так%end%/ui", "/%begin%его%end%/ui", "/%begin%чем%end%/ui",
            "/%begin%при%end%/ui", "/%begin%даже%end%/ui", "/%begin%мне%end%/ui", "/%begin%есть%end%/ui", "/%begin%раз%end%/ui", "/%begin%два%end%/ui", "/%begin%ГОСТ%end%/ui",
            "/%begin%МАРКА%end%/ui"
        );

        $delSymbols = str_replace(array_keys(ProductCategoryDetector::$patternReplacements), array_values(ProductCategoryDetector::$patternReplacements), $delSymbols);

        $parameterOptions = $em
            ->createQueryBuilder('parameterOption')
            ->from('MetalCategoriesBundle:ParameterOption', 'parameterOption', 'parameterOption.title')
            ->select('parameterOption.title')
            ->getQuery()
            ->getResult()
        ;

        $parameterOptionsName = array_keys($parameterOptions);

        $parameterOptionsName = array_map(function($parameterOptionName){
            return '/'.preg_quote($parameterOptionName, '/').'/ui';
        }, $parameterOptionsName);

        $this->delSimbols = array_merge($delSymbols , $parameterOptionsName);
        $expressionLanguage = new ExpressionLanguage();
        $table = new Table($output);
        $table->setHeaders(array('id', 'Название', 'Найденая категория', 'Текущая категория', 'Возможный шаблон'));

        do {
            $idTo = $idFrom + 200;
            $cloneQb = clone $qb;

            if ($productsIds) {
                $cloneQb
                    ->where('product.id IN (:productsIds)')
                    ->setParameter('productsIds', $productsIds)
                    ->andWhere('product.isVirtual = false')
                ;
            } else {
                $cloneQb
                    ->where('product.checked = :checked')
                    ->andWhere('product.isVirtual = false')
                    ->andWhere('product.id >= :idFrom')
                    ->andWhere('product.id < :idTo')
                    ->setParameter('checked', Product::STATUS_CHECKED)
                    ->setParameter('idFrom', $idFrom)
                    ->setParameter('idTo', $idTo)
                ;
            }

            $products = $cloneQb->getQuery()->getResult();
            /* @var $products Product[] */

            $i = 0;
            $categoryDetector = $this->getContainer()->get('metal.categories.category_matcher');
            foreach ($products as $product) {
                $pattern = $this->generatePattern(array_keys($this->getKeywords($product->getTitle())));
                $matchCategories = '';
                foreach ($categories AS $category) {
                    $extendedPattern = $category->getCategoryExtended()->getExtendedPattern();
                    if ($expressionLanguage->evaluate($extendedPattern, array('title' => $product->getTitle(), 'category_detector' => $categoryDetector))) {
                        $matchCategories .= $matchCategories ? "\n" : '';
                        $matchCategories .= sprintf('%s: %d', $category->getTitle(), $category->getId());
                    }
                }

                $table->addRow(
                    array(
                        $product->getId(),
                        $product->getTitle(),
                        $matchCategories,
                        sprintf('%s: %d', $product->getCategory()->getTitle(), $product->getCategory()->getId()),
                        $pattern
                    )
                );

                if (($i % 20) === 0) {
                    $table->render();
                    $table->setRows(array());
                }

                $i++;
            }

            $idFrom = $idTo;
        } while(!$productsIds && ($idFrom <= $idMax));

        $table->render();
        $table->setRows(array());
    }

    private function generatePattern(array $particles)
    {
        $strPattern = "match(title, '%s')";
        $pattern = '';
        foreach ($particles as $key => $particle) {
            if ($key == 0) {
                $pattern .= ' or ('.sprintf($strPattern, $particle);
            } else {
                $pattern .= ' and '.sprintf($strPattern, $particle);
            }
        }

        return $pattern.')';
    }

    /**
     * @param string $text
     * @return array
     */
    private function explodeStringOnWords($text)
    {
        $text = strtolower($text);
        $searchAndReplace = array (
            '/([\r\n])[\s]+/ui' => '',                 // Вырезается пустое пространство
            '/&(amp|#38);/ui' => '',
            '/&(lt|#60);/ui' => '\\1 ',
            '/&(gt|#62);/ui' => '"',
            '/&(nbsp|#160);/ui' => '',
            '/&(iexcl|#161);/ui' => chr(161),
            '/&(cent|#162);/ui' => chr(162),
            '/&(pound|#163);/ui' => chr(163),
            '/&(copy|#169);/ui' => chr(169)
        );

        $text = preg_replace(array_keys($searchAndReplace), array_values($searchAndReplace), $text);
        $text = preg_replace($this->delSimbols, array(' '), $text);
        $text = preg_replace("( +)", " ", $text);
        $text = preg_replace('/\s+/u', ' ', $text);
        $this->originArray = explode(" ", trim($text));

        return $this->originArray;
    }

    private function countWords()
    {
        $tmpArray = array();
        foreach ($this->originArray as $val)
        {
            if (strlen($val)>=$this->minWordLength)
            {
                $val = strtolower($val);
                if (array_key_exists($val, $tmpArray))
                {
                    $tmpArray[$val]++;
                }
                else
                {
                    $tmpArray[$val] = 1;
                }
            }
        }

        arsort($tmpArray);

        $this->modifyArray = $tmpArray;
    }

    private function getKeywords($text)
    {
        $this->explodeStringOnWords($text);
        $this->countWords();

        return array_slice($this->modifyArray, 0, 5);
    }
}