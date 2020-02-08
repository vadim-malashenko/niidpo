<?php

namespace Niidpo;

class Model {

    private $db;

    const TABLE = 'phone_to_region';

    const SCHEME = [
        'def' => 'VARCHAR(3)',
        'min' => 'INT',
        'max' => 'INT',
        'city' => 'VARCHAR(255)',
        'region' => 'VARCHAR(255)'
    ];

    public function __construct (DB $db) {

        $this->db = $db;
    }

    public function createSchema () : void {

        if ($this->db->exists (static::TABLE)) {
            $this->db->delete (static::TABLE);
        }

        if ($this->db->create (static::TABLE, static::SCHEME) === false) {
            throw new \Exception ("Create `" . static::TABLE . "` error");
        }
    }

    public function fillTable (array $rows) : void {

        $this->db->insert (static::TABLE, static::SCHEME, $rows);
    }

    public function getCityRegionByPhone (string $phone) : string {

        preg_match ('#(\d{3})(\d{7})#', $phone, $matches);

        if (count ($matches) < 3) {
            throw new \Exception ('Invalid phone number');
        }

        $def = $matches [1];
        $number = (int) $matches [2];
        $rows = $this->db->select (static::TABLE, ['city', 'region'], "def = $def AND min <= $number AND max >= $number");

        if (isset ($rows [0])) {

            $cityRegion =  implode (', ', $rows [0]);
        }
        else {

            $cityRegion = '';
        }

        return $cityRegion;
    }
}
