<?php

namespace Metal\ProductsBundle\Repository;

use Doctrine\DBAL\Connection;
use Doctrine\ORM\EntityRepository;
use Metal\CategoriesBundle\Entity\ParameterGroup;
use Metal\ProductsBundle\ChangeSet\ProductsBatchEditStructure;
use Metal\ProjectBundle\Doctrine\Utils;
use Metal\ProjectBundle\Util\InsertUtil;

class ProductParameterValueRepository extends EntityRepository
{
    private $attributesGroups = array();
    private $maxMatches = array();

    public function matchAttributesForTitle($categoryId, $title, $size = null, $matchOneParamFromGroup = true)
    {
        $end = "(?:[\s;,.\/]|$)";
        $start = "(?:[\s;,.\/]|^)";

        $title = mb_strtolower(preg_replace("/[-.]/", " ", $title));
        $title = preg_replace("/\s{2,}/", " ", $title);

        $sizeOriginal = $size;
        $size = mb_strtolower(preg_replace("/-/", " ", $size));
        $size = preg_replace("/\s{2,}/", " ", $size);
        if ($size == '' || preg_match("/рельс/", $title)) {
            $size = $size . ' ' . $title;
        }

        $matchedAttributes = array();
        $matchedGroupsCount = array();
        $matchGroupsLimits = $this->getGroupsLimits($categoryId);
        foreach ($this->getAttributesGroups($categoryId) as $type => $attributes) {
            $partForMatch = $title;

            if (!isset($matchedGroupsCount[$type])) {
                $matchedGroupsCount[$type] = 0;
            }

            if ($type == ParameterGroup::PARAMETER_RAZMER) {
                if ($size === false) {
                    continue;
                }

                $partForMatch = $size;
            }

            foreach ($attributes as $attribute) {
                $nameParam = mb_strtolower($attribute['name']);
                $nameParam = preg_replace("/[.\\+*?[^\]$(){}=!<>|:\/\-]/", ".?", $nameParam);

                $sinonReal = mb_strtolower($attribute['sinonym']);
                $sinonReal = preg_replace("/end/", "$end", $sinonReal);
                $sinonReal = preg_replace("/start/", "$start", $sinonReal);

                if ($type == ParameterGroup::PARAMETER_RAZMER) {
                    $partForMatch = str_replace(".", ",", $partForMatch);
                    $nameParamCheck = str_replace(".", "[.,\s]?", $nameParam);
                    $nameParamCheck = preg_replace("/[xх]/", "[xх\s]?", $nameParam);
                    if (preg_match("/^\d+$/", $nameParam)) {
                        $nameParamCheck .= "(?:,0)?";
                    }
                    //	echo $nameParam." -- ".$checkF."<br>";
                    //	echo "/(?:^|\s|к)+".trim($nameParamCheck)."(?:[;xх]|$|\/|мм)+/<br>";
                    if (preg_match("/(?:^|\s|к)+".trim($nameParamCheck)."(?:[;xх]|$|\/|мм)+/", $partForMatch)) {
                        $matchedAttributes[$attribute['slug']] = $attribute;
                        $matchedGroupsCount[$type]++;
                        if ($matchedGroupsCount[$type] == $matchGroupsLimits[$type]) {
                            break;
                        } else {
                            continue;
                        }
                    }
                    //synonims
                    $synArr = array();
                    if ($sinonReal != "") {
                        $synArr[] = trim($sinonReal);
                    }
                    if (preg_match("/х/", $nameParam)) {
                        $arrXX = explode("х", $nameParam);
                        if (count($arrXX) == 2) {
                            $synArr[] = $arrXX[1] . "х" . $arrXX[0];
                            //	$v1['sinonym']=$arrXX[1]."х".$arrXX[0];
                        }
                    }
                    if (preg_match("/[kк][pр]/", $nameParam)) {
                        $synArr[] = preg_replace("/[kк][pр]/", $start . "[kк][pр][-\s.]?", $nameParam);
                    }
                    if (preg_match("/[kк]/", $nameParam)) {
                        $synArr[] = preg_replace("/[kк]/", $start . "[-\s.]?[kк]", $nameParam);
                    }
                    if (preg_match("/[ш]/", $nameParam)) {
                        $synArr[] = preg_replace("/[ш]/", $start . "[-\s.]?[ш]", $nameParam);
                    }
                    if (preg_match("/[бb]\d+/", $nameParam)) {
                        $synArr[] = preg_replace("/[бb]/", $start . "[бb][-\s.]?", $nameParam);
                    }
                    if (preg_match("/\d+[бb]/", $nameParam)) {
                        $synArr[] = preg_replace("/[бb]/", "[-\s.]?[бb]", $nameParam);
                    }
                    if (preg_match("/\d+[мm]/", $nameParam)) {
                        $synArr[] = preg_replace("/[мm]/", $start . "[-\s.]?[мm]", $nameParam);
                    }
                    if (preg_match("/[pр]/", $nameParam)) {
                        $synArr[] = preg_replace("/[pр]/", "[pр][-\s.]?", $nameParam);
                    }
                    if (count($synArr) > 0) {
                        $regExp = join("|", $synArr);
                        if (preg_match("/(?:^|\s|к)+(?:" . trim($regExp) . ")(?:[\s;]|$|\/|мм)+/", $partForMatch)) {
                            $matchedAttributes[$attribute['slug']] = $attribute;
                            $matchedGroupsCount[$type]++;
                            if ($matchedGroupsCount[$type] == $matchGroupsLimits[$type]) {
                                break;
                            } else {
                                continue;
                            }
                        }
                    }
                } elseif ($type == ParameterGroup::PARAMETER_MARKA) {
                    $nameParamCheck = $nameParam; // preg_replace("/(^|\s)ст(\.|\s)?/","",$nameParam);
                    //	echo $nameParamCheck."--$checkF<br>";

                    if (preg_match("/([а-яёa-z])([-.,\s]){0,2}(\d).*/", $nameParamCheck)) {
                        //	echo "!!!!";
                        $parts = preg_match_all("/([а-яёa-z])([-.,\s]){0,2}(\d).*/", $nameParamCheck, $resP);
                        //	print_r($resP);
                        if ($parts && count($resP[1]) > 0) {
                            //	$nameParamCheck=preg_replace("/".$resP[1][0]."/",$resP[1][0]."\.?\s?-?",$nameParamCheck);
                            $nameParamCheck = preg_replace("/" . $resP[1][0] . "/", $resP[1][0] . "\.?\s{0,2}-?", $nameParamCheck);
                            //echo "$start(?:".trim($nameParamCheck).")$end<br>";
                        }

                    }
                    $pattern = "/$start(?:".trim($nameParamCheck).")$end/";

                    if (preg_match($pattern, $partForMatch)) {
                        // мы уже сматчили атрибут
                        if (!$sizeOriginal) {
                            $size = preg_replace($pattern, '', $size);
                        }
                        //	echo "/$start(?:".trim($nameParamCheck).")$end/";
                        $matchedAttributes[$attribute['slug']] = $attribute;
                        $matchedGroupsCount[$type]++;
                        //		echo $nameParam." -- ".$checkF;
                        if ($matchedGroupsCount[$type] == $matchGroupsLimits[$type]) {
                            break;
                        } else {
                            continue;
                        }
                    }

                    $nameStCut = preg_replace("/(^|\s)ст(\.|\s)?/", "", $nameParam);

                    //	$sinonReal = mb_strtolower($valType['sinonym']);
                    if (preg_match("/aisi/", $nameStCut)) {
                        $nameStCut = trim(preg_replace("/aisi\.?/", "", $nameStCut));
                        $nameStCut = "aisi\s?\.?" . $nameStCut;
                    }
                    if (strlen($nameStCut) > 2) {
                        $regExp = "$start(?:[сcC]т(?:аль)?\.?\s?)?" . $nameStCut . "-?$end" . (strlen($sinonReal) > 0 ? "|" . $sinonReal : "");
                    } else
                        $regExp = "$start(?:[сcC]т(?:аль)?\.?\s?)" . $nameStCut . "-?$end" . (strlen($sinonReal) > 0 ? "|" . $sinonReal : "");

                    if (preg_match("/$regExp/", $partForMatch)) {
                        //	echo "/$start(?:".trim($nameParamCheck).")$end/";
                        $matchedAttributes[$attribute['slug']] = $attribute;
                        $matchedGroupsCount[$type]++;
                        //	echo $nameParam." -- ".$checkF;
                        if ($matchedGroupsCount[$type] == $matchGroupsLimits[$type]) {
                            break;
                        } else {
                            continue;
                        }
                    }

                } elseif ($type == ParameterGroup::PARAMETER_GOST) {
                    $nameParamCheck = $nameParam;
                    $nameParamCheck = preg_replace("/гост|ту/", "", $nameParamCheck);

                    if (preg_match("/$start(?:" . trim($nameParamCheck) . ")/", $partForMatch)) {
                        $matchedAttributes[$attribute['slug']] = $attribute;
                        $matchedGroupsCount[$type]++;
                        if ($matchedGroupsCount[$type] == $matchGroupsLimits[$type]) {
                            break;
                        } else {
                            continue;
                        }
                    }
                    $nameParam = mb_strtolower($attribute['name']);
                    $nameParam = preg_replace("/гост|ту/", "", $nameParam);
                    $arrG = explode('-', $nameParam);
                    if (count($arrG) == 2) { //	echo "/$start(?:".trim($arrG[0]).")$end/-$checkF<br>";
                        if (preg_match("/$start(?:" . trim($arrG[0]) . ")/", $partForMatch)) {
                            $matchedAttributes[$attribute['slug']] = $attribute;
                            $matchedGroupsCount[$type]++;
                            if ($matchedGroupsCount[$type] == $matchGroupsLimits[$type]) {
                                break;
                            } else {
                                continue;
                            }
                        }
                    }

                    //	$sinonReal = mb_strtolower($valType['sinonym']);
                    if (strlen(trim($sinonReal)) > 0)
                        if (preg_match("/$start(?:" . trim($sinonReal) . ")/", $partForMatch)) {
                            //	echo "/$start(?:".trim($nameParamCheck).")$end/";
                            $matchedAttributes[$attribute['slug']] = $attribute;
                            $matchedGroupsCount[$type]++;
                            if ($matchedGroupsCount[$type] == $matchGroupsLimits[$type]) {
                                break;
                            } else {
                                continue;
                            }
                        }

                } elseif ($type == ParameterGroup::PARAMETER_KLASS) {
                    $nameParamCheck = $nameParam;

                    if (preg_match("/([а-яёa-z])([-.,\s]){0,2}(\d).*/", $nameParamCheck)) {
                        $parts = preg_match_all("/([а-яёa-z])([-.,\s]){0,2}(\d).*/", $nameParamCheck, $resP);
                        if ($parts && count($resP[1]) > 0) {
                            $nameParamCheck = preg_replace("/" . $resP[1][0] . "/", $resP[1][0] . "\.?\s{0,2}-?", $nameParamCheck);
                        }

                    }
                    //	echo "/$start(?:".trim($nameParamCheck).")$end/<br/>";
                    if (preg_match("/$start(?:" . trim($nameParamCheck) . ")$end/", $partForMatch)) {
                        //	echo "/$start(?:".trim($nameParamCheck).")$end/";
                        $matchedAttributes[$attribute['slug']] = $attribute;
                        $matchedGroupsCount[$type]++;
                        if ($matchedGroupsCount[$type] == $matchGroupsLimits[$type]) {
                            break;
                        } else {
                            continue;
                        }
                    }

                    if (strlen(trim($sinonReal)) > 0) {
                        if (preg_match("/$start(?:" . trim($sinonReal) . ")/", $partForMatch)) {
                            //	echo "/(?:".trim($sinonReal).")/";
                            $matchedAttributes[$attribute['slug']] = $attribute;
                            $matchedGroupsCount[$type]++;
                            if ($matchedGroupsCount[$type] == $matchGroupsLimits[$type]) {
                                break;
                            } else {
                                continue;
                            }
                        }
                    }

                } else {
                    $nameParamCheck = $nameParam;
                    //	echo "/$start(?:".trim($nameParamCheck).")/";
                    if (preg_match("/$start(?:" . trim($nameParamCheck) . ")/", $partForMatch)) {
                        //	echo "/$start(?:".trim($nameParamCheck).")$end/";
                        $matchedAttributes[$attribute['slug']] = $attribute;
                        $matchedGroupsCount[$type]++;
                        if ($matchedGroupsCount[$type] == $matchGroupsLimits[$type]) {
                            break;
                        } else {
                            continue;
                        }
                    }
                    if (strlen(trim($sinonReal)) > 0) {
                        if (preg_match("/$start(?:" . trim($sinonReal) . ")/", $partForMatch)) {
                            //	echo "/(?:".trim($sinonReal).")/";
                            $matchedAttributes[$attribute['slug']] = $attribute;
                            $matchedGroupsCount[$type]++;
                            if ($matchedGroupsCount[$type] == $matchGroupsLimits[$type]) {
                                break;
                            } else {
                                continue;
                            }
                        }
                    }
                }
            }
        }

        return $matchedAttributes;
    }

    private function getAttributesGroups($categoryId)
    {
        if (isset($this->attributesGroups[$categoryId])) {
            return $this->attributesGroups[$categoryId];
        }

        $conn = $this->_em->getConnection();

        Utils::checkConnection($conn);

        $steel = $conn->fetchColumn("SELECT Steel FROM Message157 WHERE Parent_Razd= :category AND Type=1",
            array('category' => $categoryId));
        //	echo "select Steel from Message157 where Parent_Razd=".$cat_id."";
        $addPars = array();
        if ($steel > 0) {
            $sql = "SELECT m155.name, m155.Message_ID AS parameterOptionId, m155.slug, IFNULL(m155.sinonym, '') AS sinonym, m155.Type AS categ
                FROM Message155 m155 WHERE Type = 1 AND Steel= :steel";
            $addPars = $conn->fetchAll($sql, array('steel' => $steel));
//                  $db->get_results($sql,ARRAY_A);
            //	print_r($addPars);
        }
        $sql = "SELECT m155.name, m155.Message_ID AS parameterOptionId, m155.slug, IFNULL(m155.sinonym, '') AS sinonym, m155.Type AS categ
                FROM Message155 m155
                JOIN Message162 m162 ON m162.Par_ID = m155.Message_ID
                JOIN Message157 m157 ON m157.Message_ID = m162.Parent_Razd
                WHERE m157.Parent_Razd = :category ORDER BY LENGTH(m155.name) DESC";

        $gostM = $conn->fetchAll($sql, array('category' => $categoryId));
//        vd($gostM, $addPars);

        $gostM = array_merge($gostM, $addPars);

//        else
//            $gostM = $db->get_results("SELECT m155.name, m155.Message_ID AS parameterOptionId, IFNULL(m155.sinonym, '') as sinonym, m155.Type as categ FROM Message155 m155 ORDER BY LENGTH(m155.name) desc", ARRAY_A);
        //echo "SELECT m155.name, m155.Message_ID, m155.sinonym, m157.Message_ID as categ FROM Message162 m162 join `Message155` m155 on m162.Par_ID=m155.Message_ID join Message157 m157 on m157.Message_ID=m162.Parent_Razd and m157.Parent_Razd=".$sub2." ";

        $attributesGroups = array();
        foreach ($gostM as $val) {
            $attributesGroups[$val['categ']][] = $val;
        }

        return $this->attributesGroups[$categoryId] = $attributesGroups;
    }

    public function onProductStatusChanging(ProductsBatchEditStructure $structure, $enable)
    {
        if (!$enable) {
            return;
        }

        $productToAttributes = array();
        foreach ($structure->products as $product) {
            $productToAttributes[$product['id']] = $this->matchAttributesForTitle($product['categoryId'], $product['title'], $product['size']);
        }

        Utils::checkEmConnection($this->_em);

        foreach ($productToAttributes as $productId => $productAttributes) {
            $parameterOptions = array();
            foreach ((array)$productAttributes as $attribute) {
                $parameterOptions[$attribute['parameterOptionId']] = $attribute['categ'];
            }

            $qb = $this->_em->createQueryBuilder()
                ->delete('MetalProductsBundle:ProductParameterValue', 'ppv')
                ->where('ppv.product = :product')
                ->setParameter('product', $productId);

            if ($parameterOptions) {
                $qb->andWhere('ppv.parameterOption NOT IN (:parameter_options)') //Удаляем только те которых нет в списке
                    ->setParameter('parameter_options', array_keys($parameterOptions));
            }

            $qb->getQuery()->execute();
        }

        $conn = $this->_em->getConnection();
        /* @var $conn Connection */

        $productsToParametersOptions = array();
        if ($productToAttributes) {
            $callable = function ($ids) use ($conn) {
                return $conn->fetchAll(
                    'SELECT Price_ID AS product_id, GostM_ID AS parameter_option FROM Message159 WHERE Price_ID IN(:products_ids)',
                    array(
                        'products_ids' => $ids,
                    ),
                    array(
                        'products_ids' => Connection::PARAM_INT_ARRAY
                    )
                );
            };

            $existsParameterOptions = InsertUtil::processBatch(array_keys($productToAttributes), $callable, 1000);
            foreach ($existsParameterOptions as $existsParameterOption) {
                foreach ((array)$existsParameterOption as $el) {
                    $productsToParametersOptions[$el['product_id']][$el['parameter_option']] = true;
                }
            }
        }

        $rows = array();
        foreach ($productToAttributes as $productId => $productAttributes) {
            $parameterOptions = array();
            foreach ((array)$productAttributes as $attribute) {
                $parameterOptions[$attribute['parameterOptionId']] = $attribute['categ'];
            }

            if (!isset($productsToParametersOptions[$productId])) {
                $productsToParametersOptions[$productId] = array();
            }

            foreach ($parameterOptions as $parameterOptionId => $type) {
                if (isset($productsToParametersOptions[$productId][$parameterOptionId])) {
                    continue;
                }

                $rows[] = array(
                    'Price_ID' => $productId,
                    'GostM_ID' => $parameterOptionId,
                    'Type' => $type
                );

                $productsToParametersOptions[$productId][$parameterOptionId] = true;
            }
        }

        InsertUtil::insertMultipleOrUpdate($conn, 'Message159', $rows, array('GostM_ID', 'Type'), 100);
    }

    public function getParameterOptions(array $productsIds = array())
    {
        if (!$productsIds) {
            return array();
        }

        $response = array_fill_keys($productsIds, array());

        $parameterOptions = $this->_em->createQueryBuilder()
            ->select('IDENTITY(ppv.product) AS productId')
            ->addSelect('parameterOption.id AS parameterOptionId')
            ->addSelect('parameterOption.typeId AS typeId')
            ->from('MetalProductsBundle:ProductParameterValue', 'ppv')
            ->join('ppv.parameterOption', 'parameterOption')
            ->where('ppv.product IN (:ids)')
            ->setParameter('ids', $productsIds)
            ->getQuery()
            ->getResult()
        ;

        foreach ($parameterOptions as $parameterOption) {
            $id = $parameterOption['productId'];
            $attributeId = (int)$parameterOption['typeId'];

            if (!$attributeId) {
                continue;
            }

            $response[$id][$attributeId] = $parameterOption['parameterOptionId'];
        }

        return $response;
    }

    private function getGroupsLimits($categoryId)
    {
        if (isset($this->maxMatches[$categoryId])) {
            return $this->maxMatches[$categoryId];
        }

        $arrLimits = array();
        $attributes = $this->_em->getRepository('MetalAttributesBundle:AttributeCategory')
            ->getAttributesForCategory($categoryId);

        foreach ($attributes as $attribute){
            $arrLimits[$attribute->getId()] = $attribute->getMaxMatches();
        }

        $this->maxMatches[$categoryId] = $arrLimits;

        return $arrLimits;
    }
}
