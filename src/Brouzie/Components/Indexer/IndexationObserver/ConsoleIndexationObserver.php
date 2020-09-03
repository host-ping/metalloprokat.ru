<?php

namespace Brouzie\Components\Indexer\IndexationObserver;

use Brouzie\Components\Indexer\IndexationObserver;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Style\SymfonyStyle;

class ConsoleIndexationObserver implements IndexationObserver
{
    private $io;

    private $index;

    private $totalIdsCount;

    private $processedIdsCount;

    public function __construct(InputInterface $input, OutputInterface $output, string $index)
    {
        $this->io = new SymfonyStyle($input, $output);
        $this->index = $index;
    }

    public function onClear(): void
    {
        $this->io->comment(sprintf('Truncating index "%s"', $this->index));
    }

    public function onCount(): void
    {
        $this->io->comment('Performing total count calculation');
    }

    public function setTotalIdsCount(int $totalIdsCount): void
    {
        $this->totalIdsCount = $totalIdsCount;
        $this->io->writeln(sprintf('%d item(s) to process', $totalIdsCount));
    }

    public function onIndexationStart(): void
    {
        $this->io->comment(sprintf('Performing indexation of "%s"', $this->index));
    }

    public function onIndexationProgress(int $incProcessedIdsCount): void
    {
        $this->processedIdsCount += $incProcessedIdsCount;

        if (!$this->totalIdsCount) {
            $this->io->writeln(sprintf('%d item(s) processed', $this->processedIdsCount));

            return;
        }

        $donePercentage = $this->processedIdsCount / $this->totalIdsCount * 100;
        $this->io->writeln(
            sprintf(
                '%d item(s) of %d processed (%.2f%%)',
                $this->processedIdsCount,
                $this->totalIdsCount,
                $donePercentage
            )
        );
    }

    public function onIndexationDone(): void
    {
        $this->io->success('Indexation done.');
    }
}
