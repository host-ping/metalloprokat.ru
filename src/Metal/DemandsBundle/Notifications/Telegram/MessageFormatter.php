<?php

namespace Metal\DemandsBundle\Notifications\Telegram;

use Metal\DemandsBundle\Notifications\Model\DemandInfo;

class MessageFormatter
{
    private $twig;

    public function __construct(\Twig_Environment $twig)
    {
        $this->twig = $twig;
    }

    public function getMessage(DemandInfo $demandInfo): string
    {
        $tags = array_map(\Closure::fromCallable([$this, 'formatTag']), $this->getTags($demandInfo));

        return $this->twig->render(
            '@MetalDemands/notifications/telegram/new_demand.md.twig',
            [
                'demandInfo' => $demandInfo,
                'tags' => $tags,
            ]
        );
    }

    private function getTags(DemandInfo $demandInfo): array
    {
        $tags = [];

        $tags[] = $demandInfo->getCityTitle();
        if ($demandInfo->getRegionTitle()) {
            $tags[] = $demandInfo->getRegionTitle();
        }

        foreach ($demandInfo->getCategories() as $category) {
            $tags[] = $category->getTitle();
        }

        return $tags;
    }

    private function formatTag(string $input): string
    {
        $tag = '#'.preg_replace(['/\s+/ui', '/\W+/ui'], ['_', ''], $input);
        $tag = str_replace('_', '\_', $tag);

        return $tag;
    }
}
