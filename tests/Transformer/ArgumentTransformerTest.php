<?php

namespace Symnedi\SymfonyBundlesExtension\Tests\Transformer;

use Nette\DI\ContainerBuilder;
use PHPUnit_Framework_TestCase;
use Prophecy\Argument;
use stdClass;
use Symfony\Component\DependencyInjection\Reference;
use Symnedi\SymfonyBundlesExtension\Transformer\ArgumentsTransformer;


class ArgumentTransformerTest extends PHPUnit_Framework_TestCase
{

	/**
	 * @var ArgumentsTransformer
	 */
	private $argumentsTransformer;

	/**
	 * @var ContainerBuilder
	 */
	private $containerBuilder;


	protected function setUp()
	{
		$this->argumentsTransformer = new ArgumentsTransformer;
		$this->containerBuilder = new ContainerBuilder;
		$this->argumentsTransformer->setContainerBuilder($this->containerBuilder);

		$this->containerBuilder->addDefinition('someService')
			->setClass(stdClass::class);
	}


	public function testReferences()
	{
		$symfonyArguments = [new Reference('name')];
		$netteArguments = $this->argumentsTransformer->transformFromSymfonyToNette($symfonyArguments);
		$this->assertSame(['@name'], $netteArguments);

		$symfonyArguments = [new Reference('@stdClass')];
		$netteArguments = $this->argumentsTransformer->transformFromSymfonyToNette($symfonyArguments);
		$this->assertSame(['@someService'], $netteArguments);
	}

}