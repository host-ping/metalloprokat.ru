<?php

namespace Metal\ProjectBundle\Pagerfanta;

use Pagerfanta\PagerfantaInterface;
use Pagerfanta\View\ViewInterface;

class TwigPaginationView implements ViewInterface
{
    const PAGE_RANGE = 4;

    private $twig;

    public function __construct(\Twig_Environment $twig)
    {
        $this->twig = $twig;
    }

    public function render(PagerfantaInterface $pagerfanta, $routeGenerator, array $options = array())
    {
        $startAndEndPage = $this->calculateStartAndEndPage($pagerfanta->getCurrentPage(), $pagerfanta->getNbPages());

        return $this->twig->render(
            '@MetalProject/Pagerfanta/twig_pagination.html.twig',
            array(
                'startAndEndPage' => $startAndEndPage,
                'pagerfanta' => $pagerfanta,
                'routeParameters' => isset($options['routeParameters']) ? $options['routeParameters'] : array(),
            )
        );
    }

    private function calculateStartAndEndPage($currentPage, $nbPages)
    {
        $startPage = $currentPage - self::PAGE_RANGE;
        $endPage = $currentPage + self::PAGE_RANGE;
        $additionalPage = self::PAGE_RANGE - $currentPage;

        if ($additionalPage > 0) {
            $startPage -= $additionalPage;
            $endPage += $additionalPage;
        }

        if ($startPage < 1) {
            $startPage = 1;
            $endPage = min($endPage + (1 - $startPage), $nbPages);
        }

        if ($endPage > $nbPages) {
            $startPage = max($startPage - ($endPage - $nbPages), 1);
            $endPage = $nbPages;
        }

        return array(
            'current_page' => $currentPage,
            'start_page' => $startPage,
            'end_page' => $endPage,
            'nb_pages' => $nbPages,
            'first_page' => 1,
        );
    }

    /**
     * {@inheritdoc}
     */
    public function getName()
    {
        return 'twig';
    }
}
