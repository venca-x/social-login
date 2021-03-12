<?php

declare(strict_types=1);

namespace Test;

use Nette\Configurator;


class Bootstrap
{
	public static function boot(): Configurator
	{
		$configurator = new Configurator;

		$configurator->setTimeZone('Europe/Prague');
		$configurator->setTempDirectory(__DIR__ . '/temp');

		$configurator->createRobotLoader()
			->addDirectory(__DIR__ . '/../../src/')
			->register();

		$configurator
			->addConfig(__DIR__ . '/config/config.neon');

		return $configurator;
	}


	public static function bootForTests(): Configurator
	{
		$configurator = self::boot();
		\Tester\Environment::setup();
		return $configurator;
	}
}
