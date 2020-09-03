<?php

namespace Metal\ProjectBundle\Doctrine;

use Doctrine\DBAL\Connection;
use Doctrine\DBAL\Logging\SQLLogger;
use Doctrine\ORM\EntityManagerInterface;

class Utils
{
    /**
     * @var SQLLogger[]
     */
    private static $loggers = array();

    public static function disableLogger(EntityManagerInterface $em)
    {
        $emConfig = $em->getConfiguration();
        $hash = spl_object_hash($emConfig);
        self::$loggers[$hash] = $emConfig->getSQLLogger();
        $emConfig->setSQLLogger(null);

        self::disableConnLogger($em->getConnection());
    }

    public static function checkEmConnection(EntityManagerInterface $em)
    {
        self::checkConnection($em->getConnection());
    }

    public static function checkConnection(Connection $connection)
    {
        if ($connection->ping() === false) {
            $connection->close();
            $connection->connect();
        }
    }

    public static function disableConnLogger(Connection $conn)
    {
        $connConfig = $conn->getConfiguration();
        $hash = spl_object_hash($connConfig);

        if (!isset(self::$loggers[$hash])) {
            self::$loggers[$hash] = $connConfig->getSQLLogger();
            $connConfig->setSQLLogger(null);
        }
    }

    public static function restoreLogging(EntityManagerInterface $em)
    {
        $emConfig = $em->getConfiguration();
        $hash = spl_object_hash($emConfig);
        $emConfig->setSQLLogger(self::$loggers[$hash]);

        self::restoreConnLogging($em->getConnection());
    }

    public static function restoreConnLogging(Connection $conn)
    {
        $connConfig = $conn->getConfiguration();
        $hash = spl_object_hash($connConfig);

        if (isset(self::$loggers[$hash])) {
            $connConfig->setSQLLogger(self::$loggers[$hash]);
        }
    }
}
