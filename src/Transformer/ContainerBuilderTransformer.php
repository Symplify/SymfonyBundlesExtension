<?php

/**
 * This file is part of Symnedi.
 * Copyright (c) 2014 Tomas Votruba (http://tomasvotruba.cz)
 */

namespace Symnedi\SymfonyBundlesExtension\Transformer;

use Nette\DI\ContainerBuilder as NetteContainerBuilder;
use ReflectionClass;
use Symfony\Component\DependencyInjection\ContainerBuilder as SymfonyContainerBuilder;
use Symfony\Component\DependencyInjection\Definition;
use Symnedi\SymfonyBundlesExtension\Utils\Naming;


class ContainerBuilderTransformer
{

	/**
	 * @var ServiceDefinitionTransformer
	 */
	private $serviceDefinitionTransformer;


	public function __construct(ServiceDefinitionTransformer $serviceDefinitionTransformer)
	{
		$this->serviceDefinitionTransformer = $serviceDefinitionTransformer;
	}


	public function transformFromNetteToSymfony(
		NetteContainerBuilder $netteContainerBuilder,
		SymfonyContainerBuilder $symfonyContainerBuilder
	) {
		$netteServiceDefinitions = $netteContainerBuilder->getDefinitions();

		foreach ($netteServiceDefinitions as $name => $netteServiceDefinition) {
			$symfonyServiceDefinition = $this->serviceDefinitionTransformer->transformFromNetteToSymfony(
				$netteServiceDefinition
			);
			$symfonyContainerBuilder->setDefinition($name, $symfonyServiceDefinition);
		}
	}


	public function transformFromSymfonyToNette(
		SymfonyContainerBuilder $symfonyContainerBuilder,
		NetteContainerBuilder $netteContainerBuilder
	) {
		$symfonyServiceDefinitions = $symfonyContainerBuilder->getDefinitions();

		foreach ($symfonyServiceDefinitions as $name => $symfonyServiceDefinition) {
			$name = Naming::sanitazeClassName($name);
			if ($this->canServiceBeAdded($netteContainerBuilder, $name)) {
				$netteContainerBuilder->addDefinition(
					$name,
					$this->serviceDefinitionTransformer->transformFromSymfonyToNette($symfonyServiceDefinition)
				);
			}
		}

		$this->transformParametersFromSymfonyToNette($symfonyContainerBuilder, $netteContainerBuilder);
	}


	private function transformParametersFromSymfonyToNette(
		SymfonyContainerBuilder $symfonyContainerBuilder,
		NetteContainerBuilder $netteContainerBuilder
	) {
		// transform parameters
		$parameterBag = $symfonyContainerBuilder->getParameterBag();
		foreach ($parameterBag->all() as $key => $value) {
			$netteContainerBuilder->parameters[$key] = $value;
		}
	}


	/**
	 * @param NetteContainerBuilder $netteContainerBuilder
	 * @param string $name
	 * @return bool
	 */
	private function canServiceBeAdded(NetteContainerBuilder $netteContainerBuilder, $name)
	{
		$serviceNames = $this->getLowercasedNames($netteContainerBuilder);
		if (in_array($name, $serviceNames)) {
			return FALSE;
		}

		return TRUE;
	}


	/**
	 * @return string[]
	 */
	private function getLowercasedNames(NetteContainerBuilder $netteContainerBuilder)
	{
		$names = array_keys($netteContainerBuilder->getDefinitions());
		foreach ($names as $key => $name) {
			$names[$key] = strtolower($name);
		}
		return $names;
	}

}
