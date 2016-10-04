<?php

/**
 * This file is part of Symnedi.
 * Copyright (c) 2014 Tomas Votruba (http://tomasvotruba.cz)
 */

namespace Symnedi\SymfonyBundlesExtension\Transformer\DI;

use Nette\Configurator;
use Nette\DI\Container;
use Nette\DI\ContainerBuilder;
use Symnedi\SymfonyBundlesExtension\Transformer\ArgumentsTransformer;


final class TransformerFactory
{

	/**
	 * @var ContainerBuilder
	 */
	private $containerBuilder;

	/**
	 * @var string
	 */
	private $tempDir;


	public function __construct(ContainerBuilder $containerBuilder, string $tempDir)
	{
		$this->containerBuilder = $containerBuilder;
		$this->tempDir = $tempDir;
	}


	public function create() : Container
	{
		$configurator = new Configurator;
		$configurator->addConfig(__DIR__ . '/services.neon');
		$configurator->setTempDirectory($this->tempDir);
		if (class_exists('Nette\Bridges\ApplicationDI\ApplicationExtension')) {
			$configurator->addConfig(__DIR__ . '/setup.neon');
		}
		$container = $configurator->createContainer();

		/** @var ArgumentsTransformer $argumentsTransformer */
		$argumentsTransformer = $container->getByType(ArgumentsTransformer::class);
		$argumentsTransformer->setContainerBuilder($this->containerBuilder);

		return $container;
	}

}
