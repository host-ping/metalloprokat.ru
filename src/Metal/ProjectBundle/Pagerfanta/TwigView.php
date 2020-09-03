<?php

namespace Metal\ProjectBundle\Pagerfanta;

use Pagerfanta\PagerfantaInterface;
use Pagerfanta\View\ViewInterface;

class TwigView implements ViewInterface
{
    protected $twig;

    public function __construct(\Twig_Environment $twig)
    {
        $this->twig = $twig;
    }

    /**
     * {@inheritdoc}
     */
    public function render(PagerfantaInterface $pagerfanta, $routeGenerator, array $options = array())
    {
        $rangePage = $this->calculateStartAndEndPage($pagerfanta->getCurrentPage(), $pagerfanta->getNbPages());

        return $this->twig->render(
            '@MetalProject/Pagerfanta/pagination.html.twig',
            array(
                'rangePage' => $rangePage,
                'pagerfanta' => $pagerfanta,
            )
        );
    }

    /**
     * {@inheritdoc}
     */
    public function getName()
    {
        return 'twig';
    }

    private function calculateStartAndEndPage($currentPage, $nbPages)
    {
        $startPage = $currentPage - 10;
        $endPage = $currentPage + 10;

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
        );
    }
}
