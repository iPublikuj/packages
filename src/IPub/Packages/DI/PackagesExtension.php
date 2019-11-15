<?php
/**
 * PackagesExtension.php
 *
 * @copyright      More in license.md
 * @license        https://www.ipublikuj.eu
 * @author         Adam Kadlec <adam.kadlec@ipublikuj.eu>
 * @package        iPublikuj:Packages!
 * @subpackage     DI
 * @since          1.0.0
 *
 * @date           27.05.16
 */

declare(strict_types = 1);

namespace IPub\Packages\DI;

use Nette;
use Nette\DI;

use IPub\Packages;
use IPub\Packages\Commands;
use IPub\Packages\Events;
use IPub\Packages\Helpers;
use IPub\Packages\Loaders;
use IPub\Packages\Repository;

/**
 * Packages extension container
 *
 * @package        iPublikuj:Packages!
 * @subpackage     DI
 *
 * @author         Adam Kadlec <adam.kadlec@ipublikuj.eu>
 */
final class PackagesExtension extends DI\CompilerExtension
{
	/**
	 * Extension default configuration
	 *
	 * @var array
	 */
	private $defaults = [
		'path'          => NULL,                        // Paths where to search for packages
		'dirs'          => [                            // Define path to folders
			'configDir' => '%appDir%/config',        // Path where is stored app configuration
			'vendorDir' => '%appDir%/../vendor',     // Path to composer vendor folder
			'tempDir'   => '%tempDir%',              // Path to temporary folder
		],
		'configFile'    => 'config.neon',               // Filename with enabled packages extensions
		'loader'        => [
			'packageFiles' => [
				'package.php',
			],
		],
		'sources'       => [],
		'symfonyEvents' => FALSE,
	];

	/**
	 * @return void
	 */
	public function loadConfiguration() : void
	{
		// Get container builder
		$builder = $this->getContainerBuilder();
		/** @var array $configuration */
		if (method_exists($this, 'validateConfig')) {
			$configuration = $this->validateConfig($this->defaults);
		} else {
			$configuration = $this->getConfig($this->defaults);
		}

		/**
		 * Load packages configuration
		 */

		$builder->parameters['packages'] = [];

		if (is_file($configuration['dirs']['configDir'] . DIRECTORY_SEPARATOR . 'packages.php')) {
			$packages = require $configuration['dirs']['configDir'] . DIRECTORY_SEPARATOR . 'packages.php';

			foreach ($packages as $name => $data) {
				$builder->parameters['packages'][$name] = $data;
			}
		}

		/**
		 * Register services
		 */

		$builder->addDefinition($this->prefix('loader'))
			->setType(Loaders\Loader::class)
			->setArguments([
				'packageFiles'    => $configuration['loader']['packageFiles'],
				'metadataSources' => $configuration['sources'],
				'vendorDir'       => $configuration['dirs']['vendorDir'],
			])
			->addTag('cms.packages');

		$repository = $builder->addDefinition($this->prefix('repository'))
			->setType(Repository\Repository::class)
			->addTag('cms.packages');

		if ($configuration['path']) {
			$repository->addSetup('addPath', [$configuration['path']]);
		}

		$builder->addDefinition($this->prefix('manager'))
			->setType(Packages\PackagesManager::class)
			->setArguments([
				'vendorDir' => $configuration['dirs']['vendorDir'],
				'configDir' => $configuration['dirs']['configDir'],
			])
			->addTag('cms.packages');

		$builder->addDefinition($this->prefix('pathResolver'))
			->setType(Helpers\PathResolver::class)
			->addTag('cms.packages');

		$builder->addDefinition($this->prefix('scripts.configuration'))
			->setType(Packages\Scripts\ConfigurationScript::class)
			->setArguments([
				'configDir'  => $configuration['dirs']['configDir'],
				'configFile' => $configuration['configFile'],
			])
			->addTag('cms.packages');

		// Define all console commands
		$commands = [
			'packagesSync'     => Commands\SyncCommand::class,
			'packagesList'     => Commands\ListCommand::class,
			'packageEnable'    => Commands\EnableCommand::class,
			'packageDisable'   => Commands\DisableCommand::class,
			'packageInstall'   => Commands\InstallCommand::class,
			'packageUninstall' => Commands\UninstallCommand::class,
		];

		foreach ($commands as $name => $cmd) {
			$builder->addDefinition($this->prefix('commands' . lcfirst($name)))
				->setType($cmd);
		}
	}

	/**
	 * {@inheritdoc}
	 */
	public function beforeCompile() : void
	{
		parent::beforeCompile();

		// Get container builder
		$builder = $this->getContainerBuilder();
		/** @var array $configuration */
		if (method_exists($this, 'validateConfig')) {
			$configuration = $this->validateConfig($this->defaults);
		} else {
			$configuration = $this->getConfig($this->defaults);
		}

		// Get packages manager
		$manager = $builder->getDefinition($this->prefix('manager'));

		foreach ($builder->findByType(Packages\Scripts\IScript::class) as $serviceDefinition) {
			$manager->addSetup('addScript', [$serviceDefinition->getType(), $serviceDefinition]);
		}

		if ($configuration['symfonyEvents'] === TRUE) {
			$dispatcher = $builder->getDefinition($builder->getByType(EventDispatcher\EventDispatcherInterface::class));

			$packagesManager = $builder->getDefinition($builder->getByType(Packages\PackagesManager::class));
			assert($packagesManager instanceof DI\ServiceDefinition);

			$packagesManager->addSetup('?->onEnable[] = function() {?->dispatch(new ?(...func_get_args()));}', [
				'@self',
				$dispatcher,
				new Nette\PhpGenerator\PhpLiteral(Events\EnableEvent::class),
			]);
			$packagesManager->addSetup('?->onDisable[] = function() {?->dispatch(new ?(...func_get_args()));}', [
				'@self',
				$dispatcher,
				new Nette\PhpGenerator\PhpLiteral(Events\DisableEvent::class),
			]);
			$packagesManager->addSetup('?->onUpgrade[] = function() {?->dispatch(new ?(...func_get_args()));}', [
				'@self',
				$dispatcher,
				new Nette\PhpGenerator\PhpLiteral(Events\UpgradeEvent::class),
			]);
			$packagesManager->addSetup('?->onInstall[] = function() {?->dispatch(new ?(...func_get_args()));}', [
				'@self',
				$dispatcher,
				new Nette\PhpGenerator\PhpLiteral(Events\InstallEvent::class),
			]);
			$packagesManager->addSetup('?->onUninstall[] = function() {?->dispatch(new ?(...func_get_args()));}', [
				'@self',
				$dispatcher,
				new Nette\PhpGenerator\PhpLiteral(Events\UninstallEvent::class),
			]);
			$packagesManager->addSetup('?->onRegister[] = function() {?->dispatch(new ?(...func_get_args()));}', [
				'@self',
				$dispatcher,
				new Nette\PhpGenerator\PhpLiteral(Events\RegisterEvent::class),
			]);
			$packagesManager->addSetup('?->onUnregister[] = function() {?->dispatch(new ?(...func_get_args()));}', [
				'@self',
				$dispatcher,
				new Nette\PhpGenerator\PhpLiteral(Events\UnregisterEvent::class),
			]);
		}
	}

	/**
	 * @param Nette\Configurator $config
	 * @param string $extensionName
	 *
	 * @return void
	 */
	public static function register(Nette\Configurator $config, string $extensionName = 'packages') : void
	{
		$config->onCompile[] = function (Nette\Configurator $config, Nette\DI\Compiler $compiler) use ($extensionName) {
			$compiler->addExtension($extensionName, new PackagesExtension());
		};
	}
}
