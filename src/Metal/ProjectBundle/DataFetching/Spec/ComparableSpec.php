<?php

namespace Metal\ProjectBundle\DataFetching\Spec;

interface ComparableSpec
{
    /**
     * Compares current spec with given and returns the difference. Returns null if specifications are equal.
     *
     * @throws \InvalidArgumentException when got different type of spec
     */
    public function diff(ComparableSpec $spec): ?ComparableSpec;
}
