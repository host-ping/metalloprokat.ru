<?php

namespace Metal\ProductsBundle\Command;

use Buzz\Browser;
use Buzz\Bundle\BuzzBundle\Buzz\Buzz;

use Doctrine\DBAL\Connection;

use Metal\ProductsBundle\Entity\ValueObject\CurrencyProvider;
use Metal\TerritorialBundle\Entity\Country;

use Symfony\Bundle\FrameworkBundle\Command\ContainerAwareCommand;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;

class UpdateExchangeRatesCommand extends ContainerAwareCommand
{
    /**
     * @var Connection
     */
    private $conn;

    /**
     * @var Browser
     */
    private $browser;

    /**
     * @var OutputInterface
     */
    private $output;

    protected function configure()
    {
        $this->setName('metal:products:update-exchange-rates');
        $this->addOption('normalize-price', null, InputOption::VALUE_NONE);
    }

    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $output->writeln(sprintf('Start command %s at %s', $this->getName(), date('Y-m-d H:i')));

        $buzz = $this->getContainer()->get('buzz');
        /* @var $buzz Buzz */
        $this->browser = $buzz->getBrowser('downloader');
        $this->browser->getClient()->setMaxRedirects(false);
        $this->browser->getClient()->setTimeout(10);

        $this->output = $output;
        $this->conn = $this->getContainer()->get('doctrine')->getManager()->getConnection();

        $countries = array(
            Country::COUNTRY_ID_RUSSIA => 'Russia',
            Country::COUNTRY_ID_UKRAINE => 'Ukraine',
            Country::COUNTRY_ID_BELORUSSIA => 'By',
            Country::COUNTRY_ID_KAZAKHSTAN => 'Kz',
        );

        foreach ($countries as $countryId => $country) {
            try {
                $rates = call_user_func(array($this, sprintf('getRatesFor%s', $country)));
                $this->insertRates($rates, $countryId);
            } catch (\Exception $e) {
                $output->writeln(sprintf('Не удалось получить курсы валют для страны с кодом id=%d, ошибка: "%s"', $countryId, $e->getMessage()));
            }
        }

        if ($input->getOption('normalize-price')) {
            $output->writeln(sprintf('%s: UPDATE Message142 normalized_price = Price', date('Y-m-d H:i')));

            $this->conn->executeUpdate('UPDATE Message142 p SET p.normalized_price = p.Price');

            $output->writeln(sprintf('%s: Normalized price', date('Y-m-d H:i')));
            $this->conn->executeUpdate('
                UPDATE Message142 p
                JOIN Message75 c ON p.Company_ID = c.Message_ID
                JOIN exchange_rate er ON c.country_id = er.country_id AND p.currency_id = er.currency_id AND er.is_last = true
                SET p.normalized_price = p.Price * er.course'
            );
        }

        $output->writeln(sprintf('End command %s at %s', $this->getName(), date('Y-m-d H:i')));
    }

    private function getRatesForRussia()
    {
        $content = $this->browser->get('http://www.cbr.ru/scripts/XML_daily.asp')->getContent();
        $xml = simplexml_load_string($content);
        unset($content);

        $rates = array();
        foreach ($xml as $item) {
            $rates[(string)$item->CharCode] = (string)$item->Value / (string)$item->Nominal;
        }

        return $rates;
    }

    private function getRatesForUkraine()
    {
        $content = $this->browser->get('http://bank-ua.com/export/currrate.xml')->getContent();
        $xml = simplexml_load_string($content);
        unset($content);

        $rates = array();
        foreach ($xml as $item) {
            $rates[(string)$item->char3] = (string)$item->rate / (string)$item->size;
        }

        return $rates;
    }

    private function getRatesForBy()
    {
        $content = $this->browser->get('http://www.nbrb.by/Services/XmlExRates.aspx')->getContent();
        $xml = simplexml_load_string($content);
        unset($content);

        $rates = array();
        foreach ($xml as $item) {
            $rates[(string)$item->CharCode] = (string)$item->Rate / (string)$item->Scale;
        }

        return $rates;
    }

    private function getRatesForKz()
    {
        $content = $this->browser->get('http://www.nationalbank.kz/rss/rates_all.xml')->getContent();
        $xml = simplexml_load_string($content);
        unset($content);

        $rates = array();
        foreach ($xml->channel->item as $item) {
            $rates[(string)$item->title] = (string)$item->description / (string)$item->quant;
        }

        return $rates;
    }

    private function insertRates($rates, $countryId)
    {
        $requiredRates = CurrencyProvider::getAllTypesEnAsSimpleArray();

        foreach ($requiredRates as $currencyId => $currencyCode) {
            if (!isset($rates[$currencyCode])) {
                continue;
            }

            $this->conn->executeUpdate(
                'UPDATE exchange_rate SET is_last = :false WHERE country_id = :country_id AND currency_id = :currency_id',
                array(
                    'false' => false,
                    'country_id' => $countryId,
                    'currency_id' => $currencyId
                )
            );

            $this->conn->executeQuery(
                'INSERT INTO exchange_rate (country_id, currency_id, updated_at, course, is_last)
                  VALUES
                  (:country_id, :currency_id, :updated_at, :course, :is_last)
                  ON DUPLICATE KEY UPDATE
                  updated_at = :updated_at,
                  course = :course,
                  is_last = :is_last
                  ',
                array(
                    'country_id' => $countryId,
                    'currency_id' => $currencyId,
                    'updated_at' => new \DateTime(),
                    'course' => $rates[$currencyCode],
                    'is_last' => true,
                ),
                array(
                    'updated_at' => 'date'
                )
            );

            $this->output->writeln(sprintf('Set course "%s" for currency "%s" where country_id = %d', $rates[$currencyCode], $currencyCode, $countryId));
        }
    }
}
