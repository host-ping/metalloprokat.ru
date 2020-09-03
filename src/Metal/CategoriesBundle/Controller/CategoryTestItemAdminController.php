<?php

namespace Metal\CategoriesBundle\Controller;

use Metal\CategoriesBundle\Entity\ParameterGroup;
use Sonata\AdminBundle\Controller\CRUDController;
use Sonata\AdminBundle\Datagrid\ProxyQueryInterface;
use Doctrine\ORM\EntityManager;

use Symfony\Component\HttpFoundation\RedirectResponse;

class CategoryTestItemAdminController extends CRUDController
{
    public function batchActionTestCategory(ProxyQueryInterface $query)
    {
        $categoryTestItems = $query->execute();
        /* @var $categoryTestItems CategoryTestItem[] */

        $catService = $this->get("metal.categories.category_matcher");
        $mismatch = array();

        foreach ($categoryTestItems as $categoryTestItem){
            $newCategory = $catService->getCategoryByTitle($categoryTestItem->getTitle());
            if ($newCategory != $categoryTestItem->getCategory()){
                $mismatch[]='id='.$categoryTestItem->getID().', "'.$categoryTestItem->getTitle().'": "'.$categoryTestItem->getCategory().'" != new - "'.$newCategory->getTitle().'"';
                $this->addFlash('sonata_flash_error', 'id='.$categoryTestItem->getID().', "'.$categoryTestItem->getTitle().'": "'.$categoryTestItem->getCategory().'" != new - "'.$newCategory->getTitle().'"');
            }
        }

        if (count($mismatch) ==0){
            $this->addFlash('sonata_flash_success','Проверка определения категории - ок для всех выбранных позиций');
        }
        else{
            foreach ($mismatch as $err){
                $this->addFlash('sonata_flash_error',$err);
            }
        }

        return new RedirectResponse(
            $this->generateUrl(
                'admin_metal_categories_categorytestitem_list'
            )
        );
    }

    public function batchActionTestParameters(ProxyQueryInterface $query)
    {
        $arrForTest = array();
        $arrTitles = array(ParameterGroup::PARAMETER_MARKA=>'Марка',ParameterGroup::PARAMETER_GOST=>'ГОСТ',ParameterGroup::PARAMETER_RAZMER=>'Р-р', ParameterGroup::PARAMETER_KLASS=>'Класс',ParameterGroup::PARAMETER_TIP=>'Тип',ParameterGroup::PARAMETER_VID=>'Вид');
        for($i=1;$i<7;$i++){
            $arrForTest[$i]['id']=null;
            $arrForTest[$i]['name']=null;
            $arrForTest[$i]['typeName']=$arrTitles["$i"];
        }

        $em = $this->getDoctrine()->getManager();
        /* @var $em EntityManager */
        $categoryTestItems = $query->execute();
        /* @var $categoryTestItems CategoryTestItem[] */

        $catService = $this->get("metal.categories.category_matcher");
        $mismatch = array();

        foreach ($categoryTestItems as $categoryTestItem){
            $mismatchPar = array();
            $arrPars = $arrForTest;
            $arrParsCurrent = $arrForTest;
            if ($categoryTestItem->getMark()){
                $arrParsCurrent[1]['id']=$categoryTestItem->getMark()->getID();
                $arrParsCurrent[1]['name']=$categoryTestItem->getMark()->getTitle();
            }
            if ($categoryTestItem->getGost()){
                $arrParsCurrent[2]['id']=$categoryTestItem->getGost()->getID();
                $arrParsCurrent[2]['name']=$categoryTestItem->getGost()->getTitle();
            }
            if ($categoryTestItem->getSize()){
                $arrParsCurrent[3]['id']=$categoryTestItem->getSize()->getID();
                $arrParsCurrent[3]['name']=$categoryTestItem->getSize()->getTitle();
            }
            if ($categoryTestItem->getClass()){
                $arrParsCurrent[4]['id']=$categoryTestItem->getClass()->getID();
                $arrParsCurrent[4]['name']=$categoryTestItem->getClass()->getTitle();
            }
            if ($categoryTestItem->getType()){
                $arrParsCurrent[5]['id']=$categoryTestItem->getType()->getID();
                $arrParsCurrent[5]['name']=$categoryTestItem->getType()->getTitle();
            }
            if ($categoryTestItem->getVid()){
                $arrParsCurrent[6]['id']=$categoryTestItem->getVid()->getID();
                $arrParsCurrent[6]['name']=$categoryTestItem->getVid()->getTitle();
            }

            $pars = $em->getRepository('MetalProductsBundle:ProductParameterValue')->matchAttributesForTitle($categoryTestItem->getCategory()->getID(),$categoryTestItem->getTitle(),$categoryTestItem->getSizeString());
            foreach ($pars as $par){
                $arrPars[$par['categ']]['id'] = $par['parameterOptionId'];
                $arrPars[$par['categ']]['name'] = $par['name'];
            }

            foreach($arrParsCurrent as $key=>$val){
                if ($val['id'] != $arrPars[$key]['id']){
                    $mismatchPar[] = $val['typeName'].': old - (id='.$val['id'].') "'.$val['name'].'"; new- (id='.$arrPars[$key]['id'].') "'.$arrPars[$key]['name'].'"';
                }
            }

            if (count($mismatchPar)>0){
                $mismatch[] = 'id='.$categoryTestItem->getID().', "'.$categoryTestItem->getTitle().'", р-р - "'.$categoryTestItem->getSizeString().'"<br/>'.join("<br/>",$mismatchPar);
            }
        }

        if (count($mismatch) ==0){
            $this->addFlash('sonata_flash_success','Проверка определения параметров - ок для всех выбранных позиций');
        }
        else{
            foreach ($mismatch as $err){
                $this->addFlash('sonata_flash_error',$err);
            }
        }

        return new RedirectResponse(
            $this->generateUrl(
                'admin_metal_categories_categorytestitem_list'
            )
        );
    }

}
