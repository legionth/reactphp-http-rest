<?php

$loader = @include __DIR__ . '/../vendor/autoload.php';
if (!$loader) {
	$loader = require __DIR__ . '/../../../../vendor/autoload.php';
}
$loader->addPsr4('Tests\\Legionth\\React\\Http\\Rest\\', __DIR__);
