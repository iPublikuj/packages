<?php
/**
 * PackagesManager.php
 *
 * @copyright      More in license.md
 * @license        http://www.ipublikuj.eu
 * @author         Adam Kadlec http://www.ipublikuj.eu
 * @package        iPublikuj:Packages!
 * @subpackage     common
 * @since          1.0.0
 *
 * @date           30.05.15
 */

declare(strict_types = 1);

namespace IPub\Packages;

use Nette;
use Nette\Utils;

use IPub;
use IPub\Packages;
use IPub\Packages\DependencyResolver;
use IPub\Packages\Entities;
use IPub\Packages\Exceptions;
use IPub\Packages\Installers;
use IPub\Packages\Repository;
use IPub\Packages\Scripts;

/**
 * Packages manager
 *
 * @package        iPublikuj:Packages!
 * @subpackage     common
 *
 * @author         Adam Kadlec <adam.kadlec@ipublikuj.eu>
 *
 * @method onEnable(PackagesManager $manager, Entities\IPackage $package)
 * @method onDisable(PackagesManager $manager, Entities\IPackage $package)
 * @method onUpgrade(PackagesManager $manager, Entities\IPackage $package)
 * @method onInstall(PackagesManager $manager, Entities\IPackage $package)
 * @method onUninstall(PackagesManager $manager, Entities\IPackage $package)
 * @method onRegister(PackagesManager $manager, Entities\IPackage $package)
 * @method onUnregister(PackagesManager $manager, Entities\IPackage $package)
 */
final class PackagesManager extends Nette\Object implements IPackagesManager
{
	/**
	 * Define package metadata keys
	 */
	const PACKAGE_STATUS = 'status';
	const PACKAGE_METADATA = 'metadata';

	/**
	 * Define actions
	 */
	const ACTION_ENABLE = 'enable';
	const ACTION_DISABLE = 'disable';
	const ACTION_REGISTER = 'register';
	const ACTION_UNREGISTER = 'unregister';

	/**
	 * @var callable[]
	 */
	public $onEnable = [];

	/**
	 * @var callable[]
	 */
	public $onDisable = [];

	/**
	 * @var callable[]
	 */
	public $onUpgrade = [];

	/**
	 * @var callable[]
	 */
	public $onInstall = [];

	/**
	 * @var callable[]
	 */
	public $onUninstall = [];

	/**
	 * @var callable[]
	 */
	public $onRegister = [];

	/**
	 * @var callable[]
	 */
	public $onUnregister = [];

	/**
	 * @var string
	 */
	private $vendorDir;

	/**
	 * @var string
	 */
	private $configDir;

	/**
	 * @var Repository\IRepository
	 */
	private $repository;

	/**
	 * @var Installers\IInstaller|NULL
	 */
	private $installer;

	/**
	 * @var Nette\DI\Container
	 */
	private $container;

	/**
	 * @var DependencyResolver\Solver
	 */
	private $dependencySolver;

	/**
	 * @var Utils\ArrayHash
	 */
	private $packagesConfig;

	/**
	 * @var Scripts\IScript[]
	 */
	private $scripts = [];

	/**
	 * @param string $vendorDir
	 * @param string $configDir
	 * @param Repository\IRepository $repository
	 * @param Installers\IInstaller|NULL $installer
	 */
	public function __construct(
		string $vendorDir,
		string $configDir,
		Repository\IRepository $repository,
		Installers\IInstaller $installer = NULL
	) {
		$this->vendorDir = $vendorDir;
		$this->configDir = $configDir;

		$this->repository = $repository;
		$this->installer = $installer;
	}

	/**
	 * {@inheritdoc}
	 */
	public function getStatus(Entities\IPackage $package) : string
	{
		$packageConfig = $this->getPackagesConfig();

		if (!$packageConfig->offsetExists($package->getName()) || !isset($packageConfig[$package->getName()][self::PACKAGE_STATUS])) {
			return Entities\IPackage::STATE_UNREGISTERED;
		}

		return $packageConfig[$package->getName()][self::PACKAGE_STATUS];
	}

	/**
	 * {@inheritdoc}
	 */
	public function getVersion(Entities\IPackage $package) : string
	{
		if (!$path = $package->getPath()) {
			throw new \RuntimeException('Package path is missing.');
		}

		if (!file_exists($file = $path . DIRECTORY_SEPARATOR . 'composer.json')) {
			throw new \RuntimeException('\'composer.json\' is missing.');
		}

		$packageData = Utils\Json::decode(file_get_contents($file), Utils\Json::FORCE_ARRAY);

		if (isset($packageData['version'])) {
			return $packageData['version'];
		}

		if (file_exists($file = $this->vendorDir . DIRECTORY_SEPARATOR . 'composer' . DIRECTORY_SEPARATOR . 'installed.json')) {
			$installed = Utils\Json::decode(file_get_contents($file), Utils\Json::FORCE_ARRAY);

			foreach ($installed as $packageData) {
				if ($packageData['name'] === $package->getName()) {
					return $packageData['version'];
				}
			}
		}

		return '0.0.0';
	}

	/**
	 * {@inheritdoc}
	 */
	public function comparePackages(Entities\IPackage $first, Entities\IPackage $second, string $operator = '==') : bool
	{
		return strtolower($first->getName()) === strtolower($second->getName()) &&
		version_compare(strtolower($this->getVersion($first)), strtolower($this->getVersion($second)), $operator);
	}

	/**
	 * {@inheritdoc}
	 */
	public function addScript(string $name, Scripts\IScript $service)
	{
		$this->scripts[$name] = $service;
	}

	/**
	 * {@inheritdoc}
	 */
	public function registerAvailable() : array
	{
		$actions = [];

		$installedPackages = array_keys($this->repository->getPackages());
		$registeredPackages = array_keys((array) $this->getPackagesConfig());

		foreach (array_diff($installedPackages, $registeredPackages) as $name) {
			/** @var Entities\IPackage $package */
			if ($package = $this->repository->findPackage($name)) {
				$this->register($package);
				$actions[] = [$name => self::ACTION_REGISTER];
			}
		}

		return $actions;
	}

	/**
	 * {@inheritdoc}
	 */
	public function enableAvailable() : array
	{
		$actions = [];

		while (TRUE) {
			/** @var Entities\IPackage[] $packages */
			$packages = $this->repository->filterPackages(function (Entities\IPackage $package) {
				return $this->getStatus($package) === Entities\IPackage::STATE_DISABLED;
			});

			if (!count($packages)) {
				break;
			}

			/** @var Entities\IPackage $package */
			$package = reset($packages);

			foreach ($this->testEnable($package)->getSolutions() as $job) {
				if ($job->getAction() === DependencyResolver\Job::ACTION_ENABLE) {
					$this->enable($job->getPackage());
					$actions[] = [$job->getPackage()->getName() => self::ACTION_ENABLE];

				} elseif ($job->getAction() === DependencyResolver\Job::ACTION_DISABLE) {
					$this->disable($job->getPackage());
					$actions[] = [$job->getPackage()->getName() => self::ACTION_DISABLE];
				}
			}

			$this->enable($package);

			$this->dependencySolver = NULL;

			$actions[] = [$package->getName() => self::ACTION_ENABLE];
		}

		return $actions;
	}

	/**
	 * {@inheritdoc}
	 */
	public function disableAbsent() : array
	{
		$actions = [];

		$installedPackages = array_keys($this->repository->getPackages());
		$registeredPackages = array_keys((array) $this->getPackagesConfig());

		foreach (array_diff($registeredPackages, $installedPackages) as $name) {
			/** @var Entities\IPackage $package */
			if ($package = $this->repository->findPackage($name)) {
				if ($this->getStatus($package) === Entities\IPackage::STATE_ENABLED) {
					$this->disable($package);
					$actions[] = [$name => self::ACTION_DISABLE];
				}

				$this->disable($package);

				$actions[] = [$package->getName() => self::ACTION_DISABLE];
			}
		}

		return $actions;
	}

	/**
	 * {@inheritdoc}
	 */
	public function install(string $name)
	{
		// Check if installer service is created
		if ($this->installer === NULL) {
			new Exceptions\InvalidStateException('Packages installer service is not created.');
		}

		// Check if package is not already installed
		if ($this->repository->findPackage($name)) {
			throw new Exceptions\InvalidArgumentException(sprintf('Package "%s" is already installed', $name));
		}

		$this->installer->install($name);

		// Reload repository after installation
		$this->repository->reload();

		// Get newly installed package
		if (!$package = $this->repository->findPackage($name)) {
			throw new Exceptions\InvalidArgumentException(sprintf('Package "%s" could not be found.', $name));
		}

		// Process all package scripts
		foreach ($package->getScripts() as $class) {
			try {
				$script = $this->getScript($class);
				$script->install($package);

			} catch (\Exception $e) {
				foreach ($package->getScripts() as $class2) {
					if ($class === $class2) {
						break;
					}

					$script = $this->getScript($class2);
					$script->uninstall($package);
				}

				throw new Exceptions\InvalidStateException($e->getMessage());
			}
		}

		$this->register($package);

		$this->onInstall($this, $package);
	}

	/**
	 * {@inheritdoc}
	 */
	public function uninstall(string $name)
	{
		// Check if installer service is created
		if ($this->installer === NULL) {
			new Exceptions\InvalidStateException('Packages installer service is not created.');
		}

		// Check if package is installed
		if (!$package = $this->repository->findPackage($name)) {
			throw new Exceptions\InvalidArgumentException(sprintf('Package "%s" is already uninstalled', $name));
		}

		// If package is still enabled, disable it first
		if ($this->getStatus($package) === Entities\IPackage::STATE_ENABLED) {
			$this->disable($package);
		}

		// Process all package scripts
		foreach ($package->getScripts() as $class) {
			try {
				$script = $this->getScript($class);
				$script->uninstall($package);

			} catch (\Exception $e) {
				foreach ($package->getScripts() as $class2) {
					if ($class === $class2) {
						break;
					}

					$script = $this->getScript($class2);
					$script->install($package);
				}

				throw new Exceptions\InvalidStateException($e->getMessage());
			}
		}

		$this->unregister($package);

		if ($this->installer->isInstalled($package->getName())) {
			$this->installer->uninstall($package->getName());

		} else {
			if (!$path = $package->getPath()) {
				throw new Exceptions\InvalidStateException('Package path is missing.');
			}

			$this->output->writeln("Removing package folder.");

			Utils\FileSystem::delete($path);
		}

		// Reload repository after uninstallation
		$this->repository->reload();

		$this->onUninstall($this, $package);
	}

	/**
	 * {@inheritdoc}
	 */
	public function enable(Entities\IPackage $package)
	{
		if ($this->getStatus($package) === Entities\IPackage::STATE_ENABLED) {
			throw new Exceptions\InvalidArgumentException(sprintf('Package \'%s\' is already enabled', $package->getName()));
		}

		$dependencyResolver = $this->getDependencySolver();
		$dependencyResolver->testEnable($package);

		foreach ($package->getScripts() as $class) {
			try {
				$installer = $this->getScript($class);
				$installer->enable($package);

			} catch (\Exception $ex) {
				foreach ($package->getScripts() as $class2) {
					if ($class === $class2) {
						break;
					}

					$installer = $this->getScript($class2);
					$installer->disable($package);
				}

				throw new Exceptions\InvalidStateException($ex->getMessage());
			}
		}

		$this->setStatus($package, Entities\IPackage::STATE_ENABLED);

		$this->onEnable($this, $package);
	}

	/**
	 * {@inheritdoc}
	 */
	public function disable(Entities\IPackage $package)
	{
		if ($this->getStatus($package) === Entities\IPackage::STATE_DISABLED) {
			throw new Exceptions\InvalidArgumentException(sprintf('Package \'%s\' is already disabled', $package->getName()));
		}

		$dependencyResolver = $this->getDependencySolver();
		$dependencyResolver->testDisable($package);

		foreach ($package->getScripts() as $class) {
			try {
				$installer = $this->getScript($class);
				$installer->disable($package);

			} catch (\Exception $e) {
				foreach ($package->getScripts() as $class2) {
					if ($class === $class2) {
						break;
					}

					$installer = $this->getScript($class2);
					$installer->enable($package);
				}

				throw new Exceptions\InvalidStateException($e->getMessage());
			}
		}

		$this->setStatus($package, Entities\IPackage::STATE_DISABLED);

		$this->onDisable($this, $package);
	}

	/**
	 * {@inheritdoc}
	 */
	public function testEnable(Entities\IPackage $package) : DependencyResolver\Problem
	{
		$problem = new DependencyResolver\Problem;

		$dependencyResolver = $this->getDependencySolver();
		$dependencyResolver->testEnable($package, $problem);

		return $problem;
	}

	/**
	 * {@inheritdoc}
	 */
	public function testDisable(Entities\IPackage $package) : DependencyResolver\Problem
	{
		$problem = new DependencyResolver\Problem;

		$dependencyResolver = $this->getDependencySolver();
		$dependencyResolver->testDisable($package, $problem);

		return $problem;
	}

	/**
	 * @param Entities\IPackage $package
	 * @param string $state
	 */
	private function register(Entities\IPackage $package, string $state = Entities\IPackage::STATE_DISABLED)
	{
		$packagesConfig = $this->getPackagesConfig();

		if (!$packagesConfig->offsetExists($package->getName())) {
			// Create package config metadata
			$packagesConfig[$package->getName()] = [
				self::PACKAGE_STATUS   => $state,
				self::PACKAGE_METADATA => [
					'authors'     => array_merge((array) $package->getAuthors()),
					'description' => $package->getDescription(),
					'keywords'    => array_merge((array) $package->getKeywords()),
					'license'     => array_merge((array) $package->getLicense()),
					'require'     => $package->getRequire(),
					'extra'       => [
						'ipub' => [
							'configuration' => array_merge((array) $package->getConfiguration()),
							'scripts'       => $package->getScripts(),
						],
					],
				],
			];
		}

		$this->savePackagesConfig($packagesConfig);

		$this->onRegister($this, $package);
	}

	/**
	 * @param Entities\IPackage $package
	 */
	private function unregister(Entities\IPackage $package)
	{
		$packagesConfig = $this->getPackagesConfig();

		// Remove package info from configuration file
		unset($packagesConfig[$package->getName()]);

		$this->savePackagesConfig($packagesConfig);

		$this->onUnregister($this, $package);
	}

	/**
	 * @param Entities\IPackage $package
	 * @param string $status
	 */
	private function setStatus(Entities\IPackage $package, string $status)
	{
		if (!in_array($status, Entities\IPackage::STATUSES, TRUE)) {
			throw new Exceptions\InvalidArgumentException(sprintf('Status \'%s\' not exists.', $status));
		}

		$packagesConfig = $this->getPackagesConfig();

		// Check if package is registered
		if (!$packagesConfig->offsetExists($package->getName())) {
			throw new Exceptions\InvalidStateException(sprintf('Package "%s" is not registered. Please call ' . get_called_class() . '::registerAvailable first.', $package->getName()));
		}

		$packagesConfig[$package->getName()][self::PACKAGE_STATUS] = $status;

		$this->savePackagesConfig($packagesConfig);
	}

	/**
	 * @param string $class
	 *
	 * @return Scripts\IScript
	 * 
	 * @throws Exceptions\InvalidStateException
	 */
	private function getScript(string $class) : Scripts\IScript
	{
		if (isset($this->scripts[$class])) {
			return $this->scripts[$class];
		}

		throw new Exceptions\InvalidStateException(sprintf('Package script "%s" was not found.', $class));
	}

	/**
	 * @return DependencyResolver\Solver
	 */
	private function getDependencySolver() : DependencyResolver\Solver
	{
		if ($this->dependencySolver === NULL) {
			$this->createSolver();
		}

		return $this->dependencySolver;
	}

	/**
	 * @return void
	 */
	private function createSolver()
	{
		$this->dependencySolver = new DependencyResolver\Solver($this->repository, $this);
	}

	/**
	 * @return Utils\ArrayHash
	 */
	private function getPackagesConfig() : Utils\ArrayHash
	{
		if ($this->packagesConfig === NULL) {
			$config = new Nette\DI\Config\Adapters\PhpAdapter;

			if (!is_file($this->getPackageConfigPath())) {
				file_put_contents($this->getPackageConfigPath(), $config->dump([]));
			}

			$this->packagesConfig = Utils\ArrayHash::from($config->load($this->getPackageConfigPath()));
		}

		return $this->packagesConfig;
	}

	/**
	 * @param Utils\ArrayHash $packagesConfig
	 * 
	 * @throws Exceptions\NotWritableException
	 */
	private function savePackagesConfig(Utils\ArrayHash $packagesConfig)
	{
		$config = new Nette\DI\Config\Adapters\PhpAdapter;

		if (file_put_contents($this->getPackageConfigPath(), $config->dump(array_merge((array) $packagesConfig))) === FALSE) {
			throw new Exceptions\NotWritableException(sprintf('Packages configuration file "%s" is not writable.', $this->getPackageConfigPath()));
		};

		// Refresh packages config data
		$this->packagesConfig = $packagesConfig;
	}

	/**
	 * @return string
	 */
	private function getPackageConfigPath() : string
	{
		return $this->configDir . DIRECTORY_SEPARATOR . 'packages.php';
	}
}
