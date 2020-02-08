<?php

$db = require 'boot.php';

require 'src/Model.php';

$phoneToRegion = new \Niidpo\Model ($db);
$cityRegion = $phoneToRegion->getCityRegionByPhone ('+7 (909) 867-02-63');

echo $cityRegion; // Хабаровск, Хабаровский Край
