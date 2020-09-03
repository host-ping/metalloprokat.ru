<?php

namespace Metal\ProjectBundle\Command;

use Symfony\Bundle\FrameworkBundle\Command\ContainerAwareCommand;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

class ForeignKeysCheckerCommand extends ContainerAwareCommand
{
    protected function configure()
    {
        $this->setName('metal:project:foreign-keys-checker');
    }

    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $output->writeln(sprintf('%s: Start command "%s"', date('d.m.Y H:i:s'), $this->getName()));

        $container = $this->getContainer();

        $connection = $container->get('doctrine.dbal.default_connection');
        $connection->getConfiguration()->setSQLLogger(null);

        $foreignKeys = $connection->createQueryBuilder()
            ->from('information_schema.KEY_COLUMN_USAGE')
            ->select('TABLE_NAME')
            ->addSelect('COLUMN_NAME')
            ->addSelect('REFERENCED_TABLE_NAME')
            ->addSelect('REFERENCED_COLUMN_NAME')
            ->where('TABLE_SCHEMA = :database')
            ->setParameter('database', $connection->getDatabase())
            ->andWhere('REFERENCED_TABLE_SCHEMA IS NOT NULL')
            ->execute()
            ->fetchAll()
        ;

        foreach ($foreignKeys as $foreignKey) {
            $output->writeln(sprintf('%s: Check foreign key "%s" in table "%s"', date('d.m.Y H:i:s'), $foreignKey['COLUMN_NAME'], $foreignKey['TABLE_NAME']));

            if ($foreignKey['TABLE_NAME'] === $foreignKey['REFERENCED_TABLE_NAME']) {
                $fkResultQb = $connection->createQueryBuilder()
                    ->select('COUNT(table1.'.$foreignKey['COLUMN_NAME'].') AS _count')
                    ->from($foreignKey['TABLE_NAME'], 'table1')
                    ->leftJoin(
                        'table1',
                        $foreignKey['REFERENCED_TABLE_NAME'],
                        'table2',
                        'table1.'.$foreignKey['COLUMN_NAME'].' = table2.'.$foreignKey['REFERENCED_COLUMN_NAME']
                    )
                    ->where('table1.'.$foreignKey['COLUMN_NAME'].' IS NOT NULL')
                    ->andWhere('table2.'.$foreignKey['REFERENCED_COLUMN_NAME'].' IS NULL');
            } else {
                $fkResultQb = $connection->createQueryBuilder()
                    ->select('COUNT('.$foreignKey['TABLE_NAME'].'.'.$foreignKey['COLUMN_NAME'].') AS _count')
                    ->from($foreignKey['TABLE_NAME'])
                    ->leftJoin(
                        $foreignKey['TABLE_NAME'],
                        $foreignKey['REFERENCED_TABLE_NAME'],
                        $foreignKey['REFERENCED_TABLE_NAME'],
                        $foreignKey['TABLE_NAME'].'.'.$foreignKey['COLUMN_NAME'].' = '.$foreignKey['REFERENCED_TABLE_NAME'].'.'.$foreignKey['REFERENCED_COLUMN_NAME']
                    )
                    ->where($foreignKey['TABLE_NAME'].'.'.$foreignKey['COLUMN_NAME'].' IS NOT NULL')
                    ->andWhere($foreignKey['REFERENCED_TABLE_NAME'].'.'.$foreignKey['REFERENCED_COLUMN_NAME'].' IS NULL');
            }

            $fkResult = $fkResultQb->execute()->fetchColumn();
            if($fkResult > 0) {
                //TODO: Писать куда-то в файл битые строки
                $output->writeln(sprintf('%s: Found %d Invalid Foreign Keys', date('d.m.Y H:i:s'), $fkResult));
            } else {
                $output->writeln(sprintf('%s: Not found Invalid Foreign Keys', date('d.m.Y H:i:s')));
            }
        }

        $output->writeln(sprintf('%s: Done command "%s"', date('d.m.Y H:i:s'), $this->getName()));
    }
}
