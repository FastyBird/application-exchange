<?php declare(strict_types = 1);

namespace Tests\Cases;

use FastyBird\ApplicationExchange;
use Nette;
use Nette\DI;
use Ninjify\Nunjuck\TestCase\BaseMockeryTestCase;

abstract class BaseTestCase extends BaseMockeryTestCase
{

	/** @var DI\Container */
	protected DI\Container $container;

	/**
	 * @param string|null $additionalConfig
	 *
	 * @return Nette\DI\Container
	 */
	protected function createContainer(?string $additionalConfig = null): Nette\DI\Container
	{
		$rootDir = __DIR__ . '/../../';

		$config = new Nette\Configurator();
		$config->setTempDirectory(TEMP_DIR);

		$config->addParameters(['container' => ['class' => 'SystemContainer_' . md5((string) time())]]);
		$config->addParameters(['appDir' => $rootDir, 'wwwDir' => $rootDir]);

		$config->addConfig(__DIR__ . '/../../common.neon');

		if ($additionalConfig && file_exists($additionalConfig)) {
			$config->addConfig($additionalConfig);
		}

		ApplicationExchange\DI\ApplicationExchangeExtension::register($config);

		return $config->createContainer();
	}

	/**
	 * @param string $serviceType
	 * @param object $serviceMock
	 *
	 * @return void
	 */
	protected function mockContainerService(
		string $serviceType,
		object $serviceMock
	): void {
		$foundServiceNames = $this->container->findByType($serviceType);

		foreach ($foundServiceNames as $serviceName) {
			$this->replaceContainerService($serviceName, $serviceMock);
		}
	}

	/**
	 * @param string $serviceName
	 * @param object $service
	 *
	 * @return void
	 */
	private function replaceContainerService(string $serviceName, object $service): void
	{
		$this->container->removeService($serviceName);
		$this->container->addService($serviceName, $service);
	}

}
