<?php
/**
 * PackagesExtensions.php
 *
 * @copyright      More in license.md
 * @license        https://www.ipublikuj.eu
 * @author         Adam Kadlec https://www.ipublikuj.eu
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
use Nette\Utils;
use Nette\Neon;
use Nette\PhpGenerator as Code;

use Kdyby\Console;

use IPub\Packages;
use IPub\Packages\Commands;
use IPub\Packages\Entities;
use IPub\Packages\Exceptions;
use IPub\Packages\Helpers;
use IPub\Packages\Installers;
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
final class PackagesExtensions extends DI\CompilerExtension
{
	/**
	 * Extension default configuration
	 *
	 * @var array
	 */
	private $defaults = [
		'path'       => NULL,                        // Paths where to search for packages
		'dirs'       => [                            // Define path to folders
			'configDir' => '%appDir%/config',        // Path where is stored app configuration
			'vendorDir' => '%appDir%/../vendor',     // Path to composer vendor folder
			'tempDir'   => '%tempDir%',              // Path to temporary folder
		],
		'configFile' => 'config.neon',               // Filename with enabled packages extensions
		'loader'     => [
			'packageFiles' => [
				'package.php',
			],
		],
		'sources'    => [
			'https://raw.github.com/ipublikuj/packages-metadata/master/metadata.json'
		],
	];

	/**
	 * @return void
	 */
	public function loadConfiguration() : void
	{
		// Get container builder
		$builder = $this->getContainerBuilder();
		// Get extension configuration
		$configuration = $this->getConfig($this->defaults);

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
				->setType($cmd)
				->addTag(Console\DI\ConsoleExtension::TAG_COMMAND);
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

		// Get packages manager
		$manager = $builder->getDefinition($this->prefix('manager'));

		foreach ($builder->findByType(Packages\Scripts\IScript::class) as $serviceDefinition) {
			$manager->addSetup('addScript', [$serviceDefinition->getType(), $serviceDefinition]);
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
			$compiler->addExtension($extensionName, new PackagesExtensions());
		};
	}
}
