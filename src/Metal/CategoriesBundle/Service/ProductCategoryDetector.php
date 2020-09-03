<?php

namespace Metal\CategoriesBundle\Service;

use Doctrine\ORM\EntityManager;
use Metal\CategoriesBundle\Entity\Category;
use Metal\CategoriesBundle\Repository\CategoryExtendedRepository;

class ProductCategoryDetector implements CategoryDetectorInterface
{
    const DEFAULT_CATEGORY_ID = 488;

    public static $patternReplacements = array(
        '%begin%' => '(?:^|[\s.,;])',
        '%end%' => '(?:[\s;,.]|$)',
    );

    /**
     * @var EntityManager
     */
    protected $em;

    /**
     * @var CategoryExtendedRepository
     */
    protected $categoryExtendedRepository;

    /**
     * @var ExpressionLanguage
     */
    protected $expressionLanguage;

    /**
     * @var Category[] key is Category.id
     */
    private $categoriesCache = array();

    public function __construct(EntityManager $em)
    {
        $this->em = $em;
        $this->categoryExtendedRepository = $em->getRepository('MetalCategoriesBundle:CategoryExtended');
    }

    /**
     * @param string $title
     *
     * @return Category
     */
    public function getCategoryByTitle($title)
    {
        $title = mb_strtolower(preg_replace('/[\/%«»]/ui', '', $title));
        $title = preg_replace('/\s+/u', ' ', $title);

        $shortTitle = implode(' ', array_slice(explode(' ', $title), 0, 2));

        $categories = $this->findCategoriesByExtendsPatterns($shortTitle);

        if (!$categories && $shortTitle !== $title) {
            $categories = $this->findCategoriesByExtendsPatterns($title);
        }

        $categories = $categories ?: $this->getCategoriesByIds(array(static::DEFAULT_CATEGORY_ID));

        return reset($categories);
    }

    public function getCategoriesByText($text)
    {
        return array_values($this->findCategoriesByExtendsPatterns(preg_replace('/\s+/u', ' ', $text)));
    }

    public function matchCategory($categoryId, $title)
    {
        $language = $this->getExpressionLanguage();
        $pattern = $this->categoryExtendedRepository->getCategoryExtendedPattern($categoryId);

        return $language->evaluate($pattern, array('title' => $title, 'category_detector' => $this));
    }

    /**
     * @param $string
     *
     * @return Category[]
     */
    protected function findCategoriesByExtendsPatterns($string)
    {
        $language = $this->getExpressionLanguage();
        $matchedCategoriesIds = array();
        //$resultCode = $language->evaluate("(match(title, 'горошек') and not match_category(35, title))", array('title'));
        //$resultCode = $language->evaluate("(match(title, 'горошек') and not match_category(35, title))", array('title' => 'Горошек Рельс', 'category_detector' => $this));
        //vdc($resultCode);

        foreach ($this->categoryExtendedRepository->getCategoriesExtendedPatterns() as $categoryId => $pattern) {
            if (!$language->evaluate($pattern, array('title' => $string, 'category_detector' => $this))) {
                continue;
            }

            $matchedCategoriesIds[$categoryId] = true;
        }

        return $this->getCategoriesByIds(array_keys($matchedCategoriesIds));
    }

    /**
     * @param array $categoriesIds
     *
     * @return Category[]
     */
    private function getCategoriesByIds(array $categoriesIds)
    {
        $categories = array();
        $notLoadedCategoriesIds = array();
        foreach ($categoriesIds as $categoryId) {
            if (empty($this->categoriesCache[$categoryId])) {
                $notLoadedCategoriesIds[$categoryId] = true;
            } else {
                $categories[] = $this->categoriesCache[$categoryId];
            }
        }

        if ($notLoadedCategoriesIds) {
            $newCategories = $this->em->getRepository('MetalCategoriesBundle:Category')
                ->findBy(array('id' => array_keys($notLoadedCategoriesIds)));

            foreach ($newCategories as $newLoadedCategory) {
                $this->categoriesCache[$newLoadedCategory->getId()] = $newLoadedCategory;
                $categories[] = $newLoadedCategory;
            }
        }

        return $categories;
    }

    /**
     * @return ExpressionLanguage
     */
    public function getExpressionLanguage()
    {
        if (null === $this->expressionLanguage) {
            if (!class_exists('Symfony\Component\ExpressionLanguage\ExpressionLanguage')) {
                throw new \RuntimeException('Unable to use expressions as the Symfony ExpressionLanguage component is not installed.');
            }
            $this->expressionLanguage = new ExpressionLanguage();
        }

        return $this->expressionLanguage;
    }

    public function getDefaultCategoryId()
    {
        return self::DEFAULT_CATEGORY_ID;
    }
}
