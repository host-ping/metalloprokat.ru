<?php

namespace Metal\ContentBundle\Helper;

use Brouzie\Bundle\HelpersBundle\Helper\HelperAbstract;
use Metal\ContentBundle\Entity\Category;
use Metal\ContentBundle\Entity\ValueObject\SubjectTypeProvider;

class SeoHelper extends HelperAbstract
{
    const META_TAG_NOINDEX_NOFOLLOW = '<meta name="robots" content="noindex, nofollow"/>';

    public function getMetaTitleForAllQuestionsPage()
    {
        $subjectTitle = null;
        $subjectId = $this->getRequest()->query->get('subject');
        if ($subject = SubjectTypeProvider::create($subjectId)) {
            $subjectTitle = ' — '.$subject->getTitle();
        }
        return 'Строительный портал '.$this->container->getParameter('project.title').' - любые вопросы касательно строительства и ремонта своими руками'.$subjectTitle;
    }

    public function getMetaTitleForContentEntryPages(Category $category)
    {
        $subjectTitle = null;
        $subjectId = $this->getRequest()->query->get('subject');
        if ($subject = SubjectTypeProvider::create($subjectId)) {
            $subjectTitle = $subject->getTitle().' - ';
        }

        $categoryTitle = $category->getMetadata()->getTitle()?: $category->getParent()->getMetadata()->getTitle();

        return $subjectTitle.$categoryTitle;
    }

    public function getH1TitleForContentEntryPages(Category $category)
    {
        $subjectId = $this->getRequest()->query->get('subject');
        if ($subject = SubjectTypeProvider::create($subjectId)) {
            return $subject->getTitle().' в '.$category->getTitlePrepositional();
        }

        return $category->getTitle();
    }

    public function getCanonicalUrlForContentEntryView()
    {
        $request = $this->getRequest();

        if ($request->getRequestUri() == $request->getPathInfo()) {
            return null;
        }

        $route = $request->attributes->get('_route');
        $routeParameters = $request->attributes->get('_route_params');

        return $this->container->get('router')->generate($route, $routeParameters, true);
    }

    public function getAdditionalMetaTagsForContentTagView()
    {
        return self::META_TAG_NOINDEX_NOFOLLOW;
    }

    public function getAdditionalMetaTagsForContentTags()
    {
        return self::META_TAG_NOINDEX_NOFOLLOW;
    }

    public function getAdditionalMetaTagsForContentQuestions()
    {
        return self::META_TAG_NOINDEX_NOFOLLOW;
    }
}
