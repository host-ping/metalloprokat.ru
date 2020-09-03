<?php

namespace Brouzie\Bridge\Twig\Extension;

use Sonata\IntlBundle\Templating\Helper\NumberHelper;
use Symfony\Component\Translation\MessageSelector;
use Twig\Extension\AbstractExtension;
use Twig\TwigFilter;

class StringExtension extends AbstractExtension
{
    private $numberHelper;

    private $messageSelector;

    public function __construct(NumberHelper $numberHelper)
    {
        $this->numberHelper = $numberHelper;
        $this->messageSelector = new MessageSelector();
    }

    public function getFilters()
    {
        return [
            new TwigFilter('lcfirst', [$this, 'lcfirst']),
            new TwigFilter('pluralize', [$this, 'pluralize']),
            new TwigFilter('format_number', [$this, 'formatNumber']),
        ];
    }

    public function lcfirst($string)
    {
        return mb_strtolower(mb_substr($string, 0, 1)).mb_substr($string, 1);
    }

    public function pluralize($template, $number)
    {
        return strtr(
            $this->messageSelector->choose($template, $number, 'ru'),
            [
                '%count%' => $number,
                '%count_formatted%' => $this->formatNumber($number),
            ]
        );
    }

    public function formatNumber($number)
    {
        return $this->numberHelper->formatDecimal($number);
    }
}
