<?php

$autoloadPaths = array(
	__DIR__ . '/../vendor/autoload.php',
	__DIR__ . '/../../../../vendor/autoload.php'
);

foreach ($autoloadPaths as $path) {
	if (file_exists($path)) {
		require_once $path;
		break;
	}
}