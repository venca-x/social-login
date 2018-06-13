<?php
declare(strict_types=1);

require __DIR__ . '/../../vendor/autoload.php';

if (!class_exists('Tester\Assert')) {
	echo "Install Nette Tester using `composer update --dev`\n";
	exit(1);
}

Tester\Environment::setup();

$configurator = new Nette\Configurator;
$configurator->setDebugMode(false);
$configurator->setTempDirectory(__DIR__ . '/temp');
$configurator->createRobotLoader()
	->addDirectory(__DIR__ . '/../../src/')
	->register();

$configurator->addConfig(__DIR__ . '/config/config.neon');

return $configurator->createContainer();
