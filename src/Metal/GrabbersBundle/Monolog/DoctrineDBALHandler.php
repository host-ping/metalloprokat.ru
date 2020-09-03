<?php

namespace Metal\GrabbersBundle\Monolog;

use Doctrine\DBAL\Connection;
use Monolog\Logger;
use Monolog\Handler\AbstractProcessingHandler;

class DoctrineDBALHandler extends AbstractProcessingHandler
{
    private $conn;

    public function __construct(Connection $conn, $level = Logger::DEBUG, $bubble = true)
    {
        $this->conn = $conn;

        //TODO: this doesn't work correctly prior to https://github.com/symfony/MonologBundle/issues/116
        parent::__construct($level, $bubble);
    }

    protected function write(array $record)
    {
        $this->conn->insert(
            'grabber_log',
            array(
                'site_id' => isset($record['context']['site_id']) ? $record['context']['site_id'] : 0,
                'level' => $record['level'],
                'message' => $record['message'],
                'created_at' => $record['datetime'],
                'context' => $record['context'],
            ),
            array(
                'created_at' => 'datetime',
                'context' => 'json_array',
            )
        );
    }
}
