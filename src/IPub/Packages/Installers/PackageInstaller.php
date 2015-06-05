<?php
/**
 * PackageInstaller.php
 *
 * @copyright	More in license.md
 * @license		http://www.ipublikuj.eu
 * @author		Adam Kadlec http://www.ipublikuj.eu
 * @package		iPublikuj:Packages!
 * @subpackage	Installers
 * @since		5.0
 *
 * @date		30.05.15
 */

namespace IPub\Packages\Installers;

use Nette;
use Nette\Utils;

use IPub;
use IPub\Packages\Entities;
use IPub\Packages\Exceptions;
use IPub\Packages\Loaders;
use IPub\Packages\Repository;

class PackageInstaller implements IInstaller
{
	/**
	 * @var Repository\IInstalledRepository
	 */
	protected $repository;

	/**
	 * @var Loaders\ILoader
	 */
	protected $loader;

	/**
	 * Initializes the installer.
	 *
	 * @param Loaders\ILoader $loader
	 * @param Repository\IInstalledRepository $repository
	 */
	public function __construct(Loaders\ILoader $loader = NULL, Repository\IInstalledRepository $repository)
	{
		$this->repository = $repository;
		$this->loader = $loader ?: new Loaders\JsonLoader;
	}

	/**
	 * {@inheritdoc}
	 */
	public function install($packageFile)
	{
		$package = $this->loader->load($packageFile);

		if ($this->repository->hasPackage($package)) {
			throw new Exceptions\LogicException('Package is already installed: '. $package);
		}

		Utils\FileSystem::copy(dirname($packageFile), $this->repository->getInstallPath($package));
		$this->repository->addPackage(clone $package);
	}

	/**
	 * {@inheritdoc}
	 */
	public function update($packageFile)
	{
		$update = $this->loader->load($packageFile);

		if (!$initial = $this->repository->findPackage($update->getName())) {
			throw new Exceptions\LogicException('Package is not installed: '. $initial);
		}

		$installPath = $this->repository->getInstallPath($initial);

		Utils\FileSystem::delete($installPath);
		$this->repository->removePackage($initial);
		Utils\FileSystem::copy(dirname($packageFile), $installPath);

		if (!$this->repository->hasPackage($update)) {
			$this->repository->addPackage(clone $update);
		}
	}

	/**
	 * {@inheritdoc}
	 */
	public function uninstall(Entities\IPackage $package)
	{
		if (!$this->repository->hasPackage($package)) {
			throw new Exceptions\LogicException('Package is not installed: '. $package);
		}

		Utils\FileSystem::delete($this->repository->getInstallPath($package));
		$this->repository->removePackage($package);
	}

	/**
	 * {@inheritdoc}
	 */
	public function isInstalled(Entities\IPackage $package)
	{
		return is_dir($this->repository->getInstallPath($package));
	}
}
