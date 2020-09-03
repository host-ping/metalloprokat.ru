<?php

namespace Metal\ProjectBundle\Controller;

use Metal\CategoriesBundle\Entity\Category;
use Metal\CategoriesBundle\Entity\ParameterGroup;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;

class ParseController extends Controller
{
    public function parseCategoryAttributesAction(Request $request)
    {
        $categoryRepository = $this->getDoctrine()->getManager()->getRepository('MetalCategoriesBundle:Category');

        if (!$request->isMethod('POST')) {
            return $this->render('MetalProjectBundle:Default:parse.html.twig', array('categories' => $categoryRepository->getCategoriesAsSimpleArray()));
        }

        $rows = preg_split('/\r\n|\r|\n/', trim($request->request->get('text')));
        $category = $categoryRepository->find($request->request->get('category'));

//        $rows = array();
//        $rows[] = 'Труба бесшовная 73х4-18. Ст.3, 10-20, 20Х-40Х, 45, 30ХГСА, 09Г2С, 12Х1МФ.';
//        $rows[] = 'Труба бесшовная 57,76х3-10. Ст.10-20, 09Г2С, 45, 40Х, 30ХГСА. ГОСТ 8732-75';

//        $rows[] = "Труба бесшовная\tСт.3, ст 10, ст 20, 20Х-40Х, 45, 30ХГСА, 09Г2С, 12Х1МФ.\t300\t73х4-18.";
//        $rows[] = "Труба бесшовная\tСт.10-20, 09Г2С, 45, 40Х, 30ХГСА. ГОСТ 8732-75\t40\t57х3-10";
//        $rows[] = "Труба бесшовная\tСт.10-20, 09Г2С, 45, 40Х, 30ХГСА. ГОСТ 8732-75\t50\t76х3-10";

        $normalizedRows = array();
        $productParameterValueRepository = $this->get('doctrine')->getRepository('MetalProductsBundle:ProductParameterValue');

        foreach ($rows as $row) {
            $cols = array_combine(array('title', 'attributes', 'price', 'size'), array_slice(explode("\t", trim($row)), 0, 4));
            $cols['size'] = preg_replace('/[*ХхXx]/', 'х', $cols['size']);
            $cols['size'] = preg_replace("/\s*(х)\s*/", "$1", $cols['size']);
            $matchedAttributes = $productParameterValueRepository->matchAttributesForTitle($category, $cols['attributes'], false);
            $matchedAttributesGroups = array();
            foreach ($matchedAttributes as $attr) {
                $matchedAttributesGroups[$attr['categ']][] = $attr;
            }

            $normalizedAttributes = array();
            if (isset($matchedAttributesGroups[ParameterGroup::PARAMETER_MARKA])) {
                foreach ($matchedAttributesGroups[ParameterGroup::PARAMETER_MARKA] as $attr) {
                    $normalizedAttribute = array();
                    $normalizedAttribute[] = $attr['name'];

                    if (isset($matchedAttributesGroups[ParameterGroup::PARAMETER_GOST])) {
                        $normalizedAttribute[] = ' ';
                        foreach ($matchedAttributesGroups[ParameterGroup::PARAMETER_GOST] as $gostAttr) {
                            $normalizedAttribute[] = 'ГОСТ '.$gostAttr['name'];
                        }
                    }

                    $normalizedAttributes[] = implode('', $normalizedAttribute);
                }
            }

            //vd($normalizedAttributes);

            $normalizedRows[] = array(
                'original' => $row,
                'parts'    => $cols,
                'normalizedAttributes' => $normalizedAttributes,
            );

//            vd($normalizedRows);
//            vd($row, $matchedAttributesGroups);
        }

        return $this->render(
            'MetalProjectBundle:Default:parse.html.twig',
            array('rows' => $normalizedRows, 'categories' => $categoryRepository->getCategoriesAsSimpleArray())
        );
    }

}
