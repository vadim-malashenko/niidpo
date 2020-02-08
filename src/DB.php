<?php

namespace Niidpo;

class DB {

    const OPTIONS = [
        \PDO::ATTR_EMULATE_PREPARES => false,
        \PDO::ATTR_ERRMODE => \PDO::ERRMODE_EXCEPTION
    ];

    const TYPE = [
        'INT' => \PDO::PARAM_INT,
        'VARCHAR' => \PDO::PARAM_STR
    ];

    private $db = null;

    public function __construct (string $host, int $port, string $db, string $user, string $password, array $options = null) {

        if ($options === null) {
            $options = static::OPTIONS;
        }

        try {
            $this->db = new \PDO ("mysql:host=$host;port=$port;dbname=$db;charset=utf8mb4", $user, $password, $options);
        }
        catch (\PDOException $ex) {
            die ('Database connection error');
        }
    }

    public function exists (string $table) : bool {

        try {
            $result = $this->db->query ("SELECT 1 FROM $table LIMIT 1");
        }
        catch (\Exception $ex) {
            return false;
        }

        return $result !== false;
    }

    public function create (string $table, array $scheme) : bool {

        $columns = [];

        foreach ($scheme as $name => $type) {
            $columns [] = "$name $type";
        }

        $columns = implode (',', $columns);

        try {
            $this->db->exec("CREATE TABLE IF NOT EXISTS $table ($columns)");
            return true;
        }
        catch (\Exception $ex) {
            return false;
        }
    }

    public function insert (string $table, array $scheme, array $rows) : int {

        if (count ($rows) == 0) {
            return 0;
        }

        $insertedRowsCount = 0;
        $columns = array_keys ($scheme);

        $values = array_reduce ($columns, function ($values, $column) {
            $values [] = ":$column";
            return $values;
        }, []);

        $values = implode (',', $values);
        $st = $this->db->prepare ("INSERT INTO $table VALUES ($values)");

        foreach ($scheme as $column => $type) {
            $$column = null;
            $st->bindParam (":$column", $$column, static::TYPE [preg_replace ('#[^a-z]+#i', '', $type)]);
        }

        foreach ($rows as $item) {
            foreach ($columns as $i => $column) {
                $$column = $item [$i];
            }
            $insertedRowsCount += ($st->execute () !== false);
        }

        return $insertedRowsCount;
    }

    public function select (string $table, array $columns, string $condition) : array {

        $sql = "SELECT " . implode (', ', $columns) . " FROM $table WHERE $condition";
        $rows = $this->db->query ($sql)->fetchAll(\PDO::FETCH_ASSOC);

        return $rows;
    }

    public function delete (string $table) : bool {

        $result = $this->db->exec ("DELETE FROM $table WHERE 1");

        return $result;
    }
}
