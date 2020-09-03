<?php

namespace Metal\AttributesBundle\Command;


use Metal\CategoriesBundle\Entity\ParameterGroup;
use Metal\ProjectBundle\Helper\TextHelperStatic;
use Symfony\Bundle\FrameworkBundle\Command\ContainerAwareCommand;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

class SetAttributeValueAccusativeCommand extends ContainerAwareCommand
{
    public function configure()
    {
        $this->setName('metal:attributes:set-value-accusative');
    }

    public function execute(InputInterface $input, OutputInterface $output)
    {
        $output->writeln(sprintf('Start command %s at %s', $this->getName(), date('Y-m-d H:i:s')));
        $conn = $this->getContainer()->get('doctrine.dbal.default_connection');

        $paramValues = $conn->fetchAll(
            '
            SELECT id,  value AS attributeValue
            FROM attribute_value
            WHERE attribute_id IN (:param_tip, :param_vid) AND value_accusative IS NULL
        ',
            array(
                'param_tip' => ParameterGroup::PARAMETER_TIP,
                'param_vid' => ParameterGroup::PARAMETER_VID
            )
        );

        foreach ($paramValues as $value) {
            if (preg_match("/[А-Яа-яa-z ]$/", $value['attributeValue'])) {
                $valueAccusative = TextHelperStatic::declinePhraseAccusative($value['attributeValue']);
                $id = $value['id'];

                $conn->executeUpdate(
                    '
                UPDATE attribute_value SET value_accusative = :value_accusative WHERE id = :id AND value_accusative IS NULL
            ',
                    array(
                        'value_accusative' => $valueAccusative,
                        'id' => $id
                    )
                );

                $conn->executeUpdate(
                    '
                    UPDATE Message155 SET title_accusative = :value_accusative WHERE Message_ID = :id AND title_accusative IS NULL
                ',
                    array(
                        'value_accusative' => $valueAccusative,
                        'id' => $id
                    )
                );
            }
        }

        $output->writeln(sprintf('End command %s at %s', $this->getName(), date('Y-m-d H:i:s')));
    }
}
