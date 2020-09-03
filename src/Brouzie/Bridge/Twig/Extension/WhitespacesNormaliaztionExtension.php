<?php

namespace Brouzie\Bridge\Twig\Extension;

use Twig\Extension\AbstractExtension;
use Twig\TwigFilter;

class WhitespacesNormaliaztionExtension extends AbstractExtension
{
    public function getFilters()
    {
        return [new TwigFilter('normalize_whitespace', [$this, 'normalizeWhitespace'], ['is_safe' => ['html']])];
    }

    public function normalizeWhitespace($value)
    {
        return trim(preg_replace('/\s+/u', ' ', $value));
    }
}
