<?php

namespace Metal\CategoriesBundle\Service;

use Brouzie\Bundle\HelpersBundle\Helper\HelperFactory;
use Doctrine\DBAL\Connection;
use Doctrine\ORM\EntityManager;
use Metal\CategoriesBundle\Entity\Category;
use Metal\CategoriesBundle\Entity\ParameterOption;
use Metal\CategoriesBundle\Helper\FindCategoriesHelper;

class MetalloprokatCategoryDetector implements CategoryDetectorInterface
{
    const DEFAULT_CATEGORY_ID = 479;
    
    protected $em;

    /**
     * @var FindCategoriesHelper
     */
    protected $findCategoriesHelper;

    private $cache = array();

    public function __construct(EntityManager $em, HelperFactory $helperFactory)
    {
        $this->em = $em;
        $this->findCategoriesHelper = $helperFactory->get('MetalCategoriesBundle:FindCategories');
    }

    public function getCategoryByTitle($title)
    {
        return $this->findCategory($title);
    }

    public function getCategoriesByText($text)
    {
        throw new \BadMethodCallException('Not implemented yet.');
    }


    /**
     * @param $title
     * @param $nerzMarks
     * @param $zarMarks
     *
     * @return Category
     */
    protected function findCategory($title, $nerzMarks = null, $zarMarks = null)
    {
        if (!$title) {
            throw new \InvalidArgumentException('Empty title.');
        }

        $title = mb_strtolower(preg_replace('/[\/%«»]/ui', '', $title));

        $return = false;
        $denyW = false;

        $arrSynonim = $this->findCategoriesHelper->getSynonim();
        $arrBranch = $this->findCategoriesHelper->getBranch();
        $arrayException = $this->findCategoriesHelper->getExeption();
        $arrMulti = $this->findCategoriesHelper->getMulti();
        $arrStroySynonim = $this->findCategoriesHelper->getStroySynonim();

        foreach ($arrSynonim as $key => $val) {
            if (preg_match("/".$val."/ui", $title)) {
                $denyW = false;
                if (array_key_exists($key, $arrBranch)) {
                    if (array_key_exists($key, $arrayException)) {
                        foreach ($arrayException[$key] as $brval) {
                            if (preg_match("/".$brval."/ui", $title)) {
                                $denyW = true;
                                break;
                            }
                        }
                    }

                    if (!$denyW) {
                        foreach ($arrBranch[$key] as $categoryId => $branch) {
                            if (preg_match("/".$branch."/ui", $title)) {
                                return $this->getCategoryById($categoryId);
                            }
                        }
                    }
                }

                if (!$return && !$denyW && $key != 178) {
                    $return = $key;
                    break;
                }
            }
        }

        if (!$return) {

            if (null === $nerzMarks) {
                $nerzMarks = $this->markSearch(2);
            }

            if (null === $zarMarks) {
                $zarMarks = $this->markSearch(3);
            }

            $addCondNerz = implode("|", $nerzMarks);
            $arrMulti[1]['search'] = $arrMulti[1]['search']."|".$addCondNerz;

            $addCondZar = implode("|", $zarMarks);
            $arrMulti[2]['search'] = $arrMulti[2]['search']."|".$addCondZar;

            foreach ($arrMulti as $val) {
                if (preg_match("/".$val['search']."/ui", $title)) {
                    foreach ($val['variants'] as $key => $value) {
                        if (preg_match("/".$value."/ui", $title)) {
                            $return = $key;
                            break 2;
                        }
                    }
                }
            }

            if (!$return) {
                $shortN = '';

                $arrWords = explode(' ', $title);

                foreach ($arrWords as $key => $w) {
                    if ($key > 1) {
                        break;
                    }
                    $shortN .= $w.' ';
                }

                if (!$return) {
                    $allC = $this->defAllDirs($shortN, array(), 0, $title);
                    $return = $allC[0];
                    $allCateg = $allC[2];

                    if (!$return && ($title != $shortN)) {
                        $allC = $this->defAllDirs($title, $allCateg, 0, $title);
                        $return = $allC[0];
                    }
                }

                if (!$return) {
                    foreach ($arrStroySynonim as $key => $val) {
                        if (preg_match("/".$val."/ui", $title)) {
                            if (array_key_exists($key, $arrBranch)) {
                                $denyW = 0;

                                if (array_key_exists($key, $arrayException)) {
                                    foreach ($arrayException[$key] as $branchPattern) {
                                        if (preg_match("/".$branchPattern."/ui", $title)) {
                                            $denyW = 1;
                                            break;
                                        }
                                    }
                                }

                                if (!$denyW) {
                                    foreach ($arrBranch[$key] as $categoryId => $branchPattern) {
                                        if (preg_match("/".$branchPattern."/ui", $title)) {
                                            return $this->getCategoryById($categoryId);
                                        }
                                    }
                                }
                            }
                        }
                    }
                    $return = Category::CATEGORY_ID_DRUGOE;
                }
            }
        } else {
            return $this->getCategoryById($return);
        }
        return $this->getCategoryById($return);
    }

    private function markSearch($markType)
    {
        if (isset($this->cache['mark_type'][$markType])) {
            return $this->cache['mark_type'][$markType];
        }

        $parameterOptionRepository = $this->em->getRepository('MetalCategoriesBundle:ParameterOption');
        $qb = $parameterOptionRepository->createQueryBuilder('po')
            ->where('po.typeId = :type_id')
            ->setParameter('type_id', 1)
            ->andWhere('po.markType = :mark_type')
            ->setParameter('mark_type', $markType);

        $paramOptions = $qb->getQuery()->getResult();
        $marks = array();
        /* @var $paramOptions ParameterOption[] */
        foreach ($paramOptions as $paramOption) {
            $marks[] = $paramOption->getTitle().($paramOption->getPattern() ? '|'.$paramOption->getPattern() : '');
        }

        $this->cache['mark_type'][$markType] = $marks;

        return $marks;
    }

    private function defAllDirs($searchText, $catArr = array(), $cat_Id = 0, $fullStr = '')
    {
        $categoryId = 0;
        $parentCategory = 0;
        $prices = array();

        $searchText = preg_replace('/[\/-]/', '', $searchText);

        if (mb_strlen($fullStr)) {
            $fullStr = mb_strtolower(preg_replace('/-/ui', ' ', $fullStr));
        }

        $endString = "(?:[\s;,.]|$)";
        $startString = "(?:^|[\s.,;])";

        if (count($catArr) < 1) {
            if ($cat_Id) {
                $prices = $this->em->getConnection()->executeQuery(
                    'SELECT
                        category.Message_ID,
                        category.cat_name,
                        categoryExtended.pattern AS pattern,
                        category.cat_parent
                    FROM Message73 AS category
                    JOIN category_extended AS categoryExtended ON categoryExtended.id = category.Message_ID
                    WHERE category.Checked = :checked
                        AND
                          category.Message_ID = :category_id
                        AND
                          category.allow_products = :allowProducts
                    ORDER BY LENGTH(category.cat_name) DESC, category.cat_parent DESC
                    ',
                    array(
                        'checked' => true,
                        'allowProducts' => true,
                        'category_id'  => $cat_Id
                    )
                )->fetchAll();
            } else {

                if (isset($this->cache['default_prices'])) {
                    $prices = $this->cache['default_prices'];
                } else {
                    $prices = $this->getDefaultPrices();
                    $this->cache['default_prices'] = $prices;
                }
            }
        }

        foreach ($prices as $price) {
            $searchN = mb_strtolower(mb_strlen($price['pattern']) > 3 ? $price['pattern'] : $price['cat_name']);

            if (preg_match("/###/", $searchN)) {
                $arrsyn = explode("###", $searchN);
            } else {
                $arrsyn[] = $searchN;
            }

            foreach ($arrsyn as $syn) {
                $searchN = trim($syn);
                $addCase = null;

                if (preg_match('/\*\*\*/', $searchN)) {
                    $searchNA = explode("***", $searchN);
                    $regExp = trim($searchNA[0]);
                    $addCase = trim($searchNA[1]);
                } else {
                    $regExp = $syn;
                }

                $regExp = preg_replace("/[\/]/", "", $regExp);
                $regExp = preg_replace("/end/", $endString, $regExp);
                $regExp = preg_replace("/start/", $startString, $regExp);

                if (preg_match("/" . $startString . $regExp . "/ui", $searchText)) {
                    if (!$addCase) {
                        $categoryId = $price['Message_ID'];
                        $parentCategory = $price['cat_parent'];
                        break 2;
                    } else {
                        if (mb_strlen($fullStr) && preg_match("/" . $startString . $addCase . "/ui", $fullStr)) {
                            $categoryId = $price['Message_ID'];
                            $parentCategory = $price['cat_parent'];
                            break 2;
                        }
                    }
                }
            }
        }

        return array(
            $categoryId,
            $parentCategory,
            $catArr
        );
    }

    private function getCategoryById($categoryId)
    {
        return $this->em->getRepository('MetalCategoriesBundle:Category')->find($categoryId);
    }

    private function getDefaultPrices()
    {
        return $this->em
            ->getConnection()
            ->executeQuery(
                'SELECT
                    category.Message_ID,
                    category.cat_name,
                    categoryExtended.pattern AS pattern,
                    category.cat_parent
                FROM Message73 AS category
                JOIN category_extended AS categoryExtended ON categoryExtended.id = category.Message_ID
                WHERE category.Checked = :checked
                  AND
                    category.allow_products = :allowProducts
                  AND
                    (category.cat_parent != :parentCategoryNot OR category.Message_ID IN (:categoriesIds))
                ORDER BY LENGTH(category.cat_name) DESC, category.cat_parent DESC
              ',
                array(
                    'checked' => true,
                    'allowProducts' => true,
                    'parentCategoryNot' => 0,
                    'categoriesIds' => array(1105, 24)
                ),
                array(
                    'categoriesIds' => Connection::PARAM_INT_ARRAY
                )
            )
            ->fetchAll();
    }

    public function getDefaultCategoryId()
    {
        return self::DEFAULT_CATEGORY_ID;
    }
}
