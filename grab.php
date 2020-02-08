<?php

set_time_limit (0);

$db = require 'boot.php';

require 'src/Model.php';
require 'src/Grabber.php';

$phoneToRegion = new \Niidpo\Model ($db);
$phoneToRegion->createSchema ();

$rows = \Niidpo\Grabber::grab ();
$phoneToRegion->fillTable ($rows);

echo count ($rows); // 4392
