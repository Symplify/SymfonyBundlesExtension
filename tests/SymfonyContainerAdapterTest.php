<?php

namespace Symnedi\SymfonyBundlesExtension\Tests;

use Mockery;
use Nette\DI\Container;
use PHPUnit_Framework_TestCase;
use stdClass;
use Symfony\Component\DependencyInjection\Exception\InvalidArgumentException;
use Symfony\Component\DependencyInjection\Exception\ServiceNotFoundException;
use Symfony\Component\DependencyInjection\ScopeInterface;
use Symnedi\SymfonyBundlesExtension\Exception\UnsupportedApiException;
use Symnedi\SymfonyBundlesExtension\SymfonyContainerAdapter;


class SymfonyContainerAdapterTest extends PHPUnit_Framework_TestCase
{

	/**
	 * @var SymfonyContainerAdapter
	 */
	private $symfonyContainerAdapter;


	protected function setUp()
	{
		$containerMock = Mockery::mock(Container::class, [
			'getParameters' => ['someParameter' => 'someValue'],
		]);
		$containerMock->shouldReceive('hasService')->with('someService')->andReturn(TRUE);
		$containerMock->shouldReceive('hasService')->with('nonExistingService')->andReturn(FALSE);
		$containerMock->shouldReceive('getService')->with('someService')->andReturn('service');
		$this->symfonyContainerAdapter = new SymfonyContainerAdapter($containerMock);
	}


	public function testParameters()
	{
		$this->assertSame('someValue', $this->symfonyContainerAdapter->getParameter('someParameter'));
		$this->assertTrue($this->symfonyContainerAdapter->hasParameter('someParameter'));
	}


	public function testNonExistingParameters()
	{
		$this->setExpectedException(InvalidArgumentException::class);
		$this->symfonyContainerAdapter->getParameter('nonExistingParameter');
	}


	public function testServices()
	{
		$this->assertTrue($this->symfonyContainerAdapter->has('someService'));
		$this->assertSame('service', $this->symfonyContainerAdapter->get('someService'));
	}


	public function testNonExistingService()
	{
		$this->assertFalse($this->symfonyContainerAdapter->has('nonExistingService'));
		$this->setExpectedException(ServiceNotFoundException::class);
		$this->symfonyContainerAdapter->get('nonExistingService');
	}


	public function testUnsupportedMethodsAddScope()
	{
		$scopeMock = Mockery::mock(ScopeInterface::class);

		$this->setExpectedException(UnsupportedApiException::class);
		$this->symfonyContainerAdapter->addScope($scopeMock);
	}


	public function testUnsupportedMethodsEnterScope()
	{
		$this->setExpectedException(UnsupportedApiException::class);
		$this->symfonyContainerAdapter->enterScope('someScope');
	}


	public function testUnsupportedMethodsHasScope()
	{
		$this->setExpectedException(UnsupportedApiException::class);
		$this->symfonyContainerAdapter->hasScope('someScope');
	}


	public function testUnsupportedMethodsLeaveScope()
	{
		$this->setExpectedException(UnsupportedApiException::class);
		$this->symfonyContainerAdapter->leaveScope('someScope');
	}


	public function testUnsupportedMethodsIsScopeActive()
	{
		$this->setExpectedException(UnsupportedApiException::class);
		$this->symfonyContainerAdapter->isScopeActive('someScope');
	}


	public function testUnsupportedMethodsSet()
	{
		$this->setExpectedException(UnsupportedApiException::class);
		$this->symfonyContainerAdapter->set('someService', new stdClass);
	}


	public function testUnsupportedMethodsSetParameter()
	{
		$this->setExpectedException(UnsupportedApiException::class);
		$this->symfonyContainerAdapter->setParameter('someParameter', 'someValue');
	}

}
