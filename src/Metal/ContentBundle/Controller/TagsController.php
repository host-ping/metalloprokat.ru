<?php

namespace Metal\ContentBundle\Controller;

use Doctrine\ORM\EntityManager;
use Doctrine\ORM\EntityManagerInterface;
use Metal\ContentBundle\Entity\ValueObject\StatusTypeProvider;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;

class TagsController extends Controller
{
    public function getSuggestAction(Request $request)
    {
        $em = $this->getDoctrine()->getManager();
        /* @var $em EntityManagerInterface */
        $items = $em
            ->createQueryBuilder()
            ->select('t.id, t.title AS label')
            ->from('MetalContentBundle:Tag', 't')
            ->where('t.statusTypeId = :checked')
            ->setParameter('checked', StatusTypeProvider::CHECKED)
            ->andWhere('t.title LIKE :q')
            ->setParameter('q', '%'.$request->query->get('q').'%')
            ->getQuery()
            ->getResult();

        return JsonResponse::create(
            array(
                'status' => 'OK',
                'more' => false,
                'items' => $items,
            )
        );

    }

    public function getSuggestTagsAction()
    {
        $em = $this->getDoctrine()->getManager();
        /* @var $em EntityManagerInterface */

        $tags = $em
            ->createQueryBuilder()
            ->select('t.id, t.title')
            ->from('MetalContentBundle:Tag', 't')
            ->where('t.statusTypeId = :checked')
            ->setParameter('checked', StatusTypeProvider::CHECKED)
            ->getQuery()
            ->getArrayResult();

        return JsonResponse::create($tags);
    }

    public function listAction()
    {
        $em = $this->getDoctrine()->getManager();
        /* @var $em EntityManager */

        $tags = $em
            ->createQueryBuilder()
            ->select('tag.id, tag.title')
            ->addSelect('COUNT(cet) AS contentEntriesCount')
            ->from('MetalContentBundle:Tag', 'tag')
            ->andWhere('tag.statusTypeId = :checked')
            ->setParameter('checked', StatusTypeProvider::CHECKED)
            ->join('MetalContentBundle:ContentEntryTag', 'cet', 'WITH', 'tag.id = cet.tag')
            ->groupBy('cet.tag')
            ->orderBy('tag.title')
            ->getQuery()
            ->getScalarResult();

        return $this->render(
            '@MetalContent/Tags/list.html.twig',
            array(
                'tags' => $tags
            )
        );
    }

}
