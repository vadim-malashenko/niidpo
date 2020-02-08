<?php

require 'src/DB.php';

extract (parse_ini_file ('db.ini'));

$db = new \Niidpo\DB ($host, $port, $dbname, $user, $password);

return $db;
