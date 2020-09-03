<?php

namespace Metal\ProjectBundle\Util;

use Doctrine\DBAL\Connection;

class InsertUtil
{
    /**
     * @param Connection $conn
     * @param string $table Таблица, в которую вставляем данные
     * @param array $data Массив ассоциативных массивов для вставки array(array('column1' => 'value1'), array('column1' => 'value2'))
     * @param array $updateColumns Список колонок, значения которых нужно обновить в случае дублирования ключа
     * @param int $batchSize По сколько записей вставлять
     *
     * @return int
     */
    public static function insertMultipleOrUpdate(Connection $conn, $table, array $data, array $updateColumns = array(), $batchSize = null)
    {
        if (0 === count($data)) {
            return null;
        }

        if ($batchSize) {
            $callable = function (array $dataSlice) use ($conn, $table, $updateColumns) {
                self::insertMultipleOrUpdate($conn, $table, $dataSlice, $updateColumns);
            };
            $results = self::processBatch($data, $callable, $batchSize);

            return array_sum($results);
        }

        $columns = array_map(array($conn, 'quoteIdentifier'), array_keys($data[0]));

        $sql = 'INSERT INTO '.$conn->quoteIdentifier($table).' ('.implode(', ', $columns).') VALUES ';

        $values = array();
        $params = array();
        $types = array();
        foreach ($data as $row) {
            $values[] = '('.implode(', ', array_fill(0, count($row), '?')).') ';
            foreach ($row as $item) {
                $params[] = $item;
                $types[] = \PDO::PARAM_STR; //TODO: реализовать
            }
        }

        $sql .= implode(', ', $values);

        if ($updateColumns) {
            $updateColumnsArray = array();
            foreach ($updateColumns as $updateColumn) {
                $updateColumn = $conn->quoteIdentifier($updateColumn);
                $updateColumnsArray[] = $updateColumn.' = VALUES('.$updateColumn.') ';
            }

            $sql .= ' ON DUPLICATE KEY UPDATE '.implode(', ', $updateColumnsArray);
        }

        return $conn->executeUpdate($sql, $params, $types);
    }

    public static function processBatch(array $data, callable $processor, $batchSize)
    {
        $results = array();
        while (!empty($data)) {
            $results[] = $processor(array_splice($data, 0, $batchSize));
        }

        return $results;
    }
}
