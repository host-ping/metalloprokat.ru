<?php

namespace Metal\ContentBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity(repositoryClass="Metal\ContentBundle\Repository\ContentEntryRepository")
 */
class Question extends AbstractContentEntry
{
    public function getKind()
    {
        return 'question';
    }
}
