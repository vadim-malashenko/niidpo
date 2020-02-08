<?php

$db = require 'boot.php';

require 'src/Model.php';

$phoneToRegion = new \Niidpo\Model ($db);
$cityRegion = $phoneToRegion->getCityRegionByPhone ('9098670263');

echo $cityRegion; // Хабаровск, Хабаровский Край
