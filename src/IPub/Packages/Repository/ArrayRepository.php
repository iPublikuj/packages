<?php
/**
 * ArrayRepository.php
 *
 * @copyright      More in license.md
 * @license        http://www.ipublikuj.eu
 * @author         Adam Kadlec http://www.ipublikuj.eu
 * @package        iPublikuj:Packages!
 * @subpackage     Repository
 * @since          1.0.0
 *
 * @date           30.05.15
 */

namespace IPub\Packages\Repository;

use IPub;
use IPub\Packages\Entities;

/**
 * Packages default array repository
 *
 * @package        iPublikuj:Packages!
 * @subpackage     Repository
 *
 * @author         Adam Kadlec <adam.kadlec@fastybird.com>
 */
class ArrayRepository implements IRepository
{
	/**
	 * @var Entities\IPackage[]
	 */
	protected $packages;

	/**
	 * @param array $packages
	 */
	public function __construct(array $packages = [])
	{
		foreach ($packages as $package) {
			$this->addPackage($package);
		}
	}

	/**
	 * {@inheritdoc}
	 */
	public function findPackage($name, $version = 'latest')
	{
		// normalize name
		$name = strtolower($name);

		if ($version === 'latest') {
			$packages = $this->findPackages($name);
			usort($packages, function (Entities\Package $a, Entities\Package $b) {
				return version_compare($a->getVersion(), $b->getVersion());
			});

			return end($packages);

		} else {
			return current($this->findPackages($name, $version));
		}
	}

	/**
	 * {@inheritdoc}
	 */
	public function findPackages($name, $version = NULL)
	{
		// normalize name
		$name = strtolower($name);

		$packages = [];

		foreach ($this->getPackages() as $package) {
			if ($package->getName() === $name && ($version === NULL || $version === $package->getVersion())) {
				$packages[] = $package;
			}
		}

		return $packages;
	}

	/**
	 * {@inheritdoc}
	 */
	public function hasPackage(Entities\IPackage $package)
	{
		$packageId = $package->getUniqueName();

		foreach ($this->getPackages() as $repoPackage) {
			if ($packageId === $repoPackage->getUniqueName()) {
				return TRUE;
			}
		}

		return FALSE;
	}

	/**
	 * {@inheritdoc}
	 */
	public function addPackage(Entities\IPackage $package)
	{
		if ($this->packages === NULL) {
			$this->initialize();
		}

		$this->packages[] = $package;

		return $package;
	}

	/**
	 * {@inheritdoc}
	 */
	public function filterPackages(callable $callback, $class = 'IPub\Packages\Entities\Package')
	{
		foreach ($this->getPackages() as $package) {
			if (call_user_func($callback, $package) === FALSE) {
				return FALSE;
			}
		}

		return TRUE;
	}

	/**
	 * {@inheritdoc}
	 */
	public function removePackage(Entities\IPackage $package)
	{
		$packageId = $package->getUniqueName();

		foreach ($this->getPackages() as $key => $repoPackage) {
			if ($packageId === $repoPackage->getUniqueName()) {
				array_splice($this->packages, $key, 1);

				return;
			}
		}
	}

	/**
	 * {@inheritdoc}
	 */
	public function getPackages()
	{
		if ($this->packages === NULL) {
			$this->initialize();
		}

		return $this->packages;
	}

	/**
	 * {@inheritdoc}
	 */
	public function count()
	{
		return count($this->getPackages());
	}

	/**
	 * Initializes the packages array. Mostly meant as an extension point
	 */
	protected function initialize()
	{
		$this->packages = [];
	}
}
