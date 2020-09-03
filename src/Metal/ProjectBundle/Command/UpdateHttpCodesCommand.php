<?php

namespace Metal\ProjectBundle\Command;

use Buzz\Bundle\BuzzBundle\Exception\ResponseException;
use Buzz\Exception\RequestException;
use Symfony\Bundle\FrameworkBundle\Command\ContainerAwareCommand;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

class UpdateHttpCodesCommand extends ContainerAwareCommand
{
    protected function configure()
    {
        $this->setName('metal:project:update-http-codes');
    }

    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $conn = $this->getContainer()->get('doctrine.dbal.default_connection');
        $browser = $this->getContainer()->get('buzz')->getBrowser('downloader');

        $requests = $conn->fetchAll("SELECT * FROM ban_request WHERE method = 'GET' AND code IS NULL LIMIT 5000");
        foreach ($requests as $request) {
            try {
                $code = $browser->head($request['uri'])->getStatusCode();
            } catch (RequestException $e) {
                $code = 0;
            } catch (ResponseException $e) {
                $code = $e->getResponse()->getStatusCode();
            }
            $conn->update('ban_request', array('code' => $code), array('id' => $request['id']));
        }
    }
}
