<?php

namespace Metal\CatalogBundle\Command;

use Doctrine\ORM\EntityManager;
use Metal\CatalogBundle\Entity\ProductAttributeValue;
use Symfony\Bundle\FrameworkBundle\Command\ContainerAwareCommand;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

class FixCollisionProductCountryCommand extends ContainerAwareCommand
{
    protected function configure()
    {
        $this
            ->setName('metal:catalog:fix-collision-product-country')
        ;
    }

    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $output->writeln(sprintf('Start command %s at %s', $this->getName(), date('Y-m-d H:i')));

        $em = $this->getContainer()->get('doctrine')->getManager();
        /* @var $em EntityManager */
        $em->getConfiguration()->setSQLLogger(null);
        $em->getConnection()->getConfiguration()->setSQLLogger(null);
        $conn = $em->getConnection();


        $productsWithoutCountryAttribute = $conn->fetchAll('
            SELECT cpc.product_id, cpc.city_id, c.country_id, COUNT(cpc.city_id) AS count_cities, country.Country_Name FROM catalog_product_city cpc
            JOIN Classificator_Region c ON cpc.city_id = c.Region_ID
            JOIN Classificator_Country country ON country.Country_ID = c.country_id
               WHERE cpc.product_id
                  NOT IN
                  (SELECT cpav.product_id FROM  catalog_product_attribute_value cpav
                      JOIN attribute_value av ON cpav.attribute_value_id = av.id
                      WHERE av.attribute_id = 2
                  )
            GROUP BY c.country_id, cpc.product_id
        ');

        $productsToUpdate = array();

        foreach ($productsWithoutCountryAttribute as $item) {
            if (in_array($item['product_id'], array_keys($productsToUpdate))) {
                if ($productsToUpdate[$item['product_id']]['count'] < $item['count_cities']) {
                    $productsToUpdate[$item['product_id']]['count'] = (int)$item['count_cities'];
                    $productsToUpdate[$item['product_id']]['country_id'] = (int)$item['country_id'];
                    $productsToUpdate[$item['product_id']]['title'] = $item['Country_Name'];
                }
            } else {
                $productsToUpdate[$item['product_id']]['count'] = (int)$item['count_cities'];
                $productsToUpdate[$item['product_id']]['country_id'] = (int)$item['country_id'];
                $productsToUpdate[$item['product_id']]['title'] = $item['Country_Name'];
            }
        }


        $findAttributes = array();
        foreach ($productsToUpdate as $productId => $productCountry) {
            $product = $em->getRepository('MetalCatalogBundle:Product')->find($productId);
            $attributeValue = null;
            if (!in_array($productCountry['title'], array_keys($findAttributes))) {
                $attributeValue = $em->getRepository('MetalAttributesBundle:AttributeValue')->findOneBy(array('value' => $productCountry['title'], 'attribute' => 2));
                $findAttributes[$productCountry['title']] = $attributeValue;
            }

            if ($findAttributes) {
                $productAttributeValue = new ProductAttributeValue();
                $productAttributeValue->setAttributeValue($findAttributes[$productCountry['title']]);
                $product->addProductAttributeValue($productAttributeValue);

                $em->persist($productAttributeValue);
                $em->flush();

                $output->writeln(sprintf('Product id %d, Attribute Value id %d', $product->getId(), $findAttributes[$productCountry['title']]->getId()));
            } else {
                $output->writeln(sprintf('Product id %d, Not found Attribute Value', $product->getId()));
            }

        }

        $output->writeln(sprintf('End command %s at %s', $this->getName(), date('Y-m-d H:i')));
    }
}