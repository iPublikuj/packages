<?php
/**
 * Repository.php
 *
 * @copyright      More in license.md
 * @license        https://www.ipublikuj.eu
 * @author         Adam Kadlec https://www.ipublikuj.eu
 * @package        iPublikuj:Packages!
 * @subpackage     Repository
 * @since          1.0.0
 *
 * @date           30.05.15
 */

declare(strict_types = 1);

namespace IPub\Packages\Repository;

use Nette;
use Nette\Utils;

use IPub\Packages;
use IPub\Packages\Caching;
use IPub\Packages\Entities;
use IPub\Packages\Loaders;

/**
 * Packages repository
 *
 * @package        iPublikuj:Packages!
 * @subpackage     Repository
 *
 * @author         Adam Kadlec <adam.kadlec@ipublikuj.eu>
 */
final class Repository implements IRepository
{
	/**
	 * Implement nette smart magic
	 */
	use Nette\SmartObject;

	/**
	 * @var array
	 */
	private $paths = [];

	/**
	 * @var Loaders\ILoader
	 */
	private $loader;

	/**
	 * @var Entities\IPackage[]
	 */
	private $packages;

	/**
	 * @param Loaders\ILoader $loader
	 */
	public function __construct(Loaders\ILoader $loader)
	{
		$this->loader = $loader;
	}

	/**
	 * {@inheritdoc}
	 */
	public function findPackage(string $name, string $version = 'latest')
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
	public function findPackages(string $name, string $version = NULL) : array
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
	public function hasPackage(Entities\IPackage $package) : bool
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
	public function filterPackages(callable $callback) : array
	{
		$packages = [];

		foreach ($this->getPackages() as $package) {
			if (call_user_func($callback, $package) === TRUE) {
				$packages[$package->getName()] = $package;
			}
		}

		return $packages;
	}

	/**
	 * {@inheritdoc}
	 */
	public function getPackages() : array
	{
		if ($this->packages === NULL) {
			$this->initialize();
		}

		return $this->packages;
	}

	/**
	 * {@inheritdoc}
	 */
	public function reload() : void
	{
		$this->initialize();
	}

	/**
	 * {@inheritdoc}
	 */
	public function getPaths() : array
	{
		return $this->paths;
	}

	/**
	 * {@inheritdoc}
	 */
	public function addPath($path) : void
	{
		if (!is_array($path)) {
			$path = [$path];
		}

		$this->paths = array_merge($this->paths, $path);
	}

	/**
	 * {@inheritdoc}
	 */
	public function count()
	{
		return count($this->getPackages());
	}

	/**
	 * {@inheritdoc}
	 */
	public function offsetExists($name)
	{
		return isset($this->packages[$name]);
	}

	/**
	 * {@inheritdoc}
	 */
	public function offsetGet($name)
	{
		return $this->findPackage($name);
	}

	/**
	 * {@inheritdoc}
	 */
	public function offsetSet($name, $package)
	{
		$this->packages[$name] = $package;
	}

	/**
	 * {@inheritdoc}
	 */
	public function offsetUnset($name)
	{
		unset($this->packages[$name]);
	}

	/**
	 * {@inheritdoc}
	 */
	public function getIterator()
	{
		return new \ArrayIterator($this->packages);
	}

	/**
	 * Initializes the packages array. Mostly meant as an extension point
	 */
	private function initialize()
	{
		$this->packages = [];

		foreach ($this->paths as $path) {
			$files = glob($path . DIRECTORY_SEPARATOR . '*' . DIRECTORY_SEPARATOR . '*' . DIRECTORY_SEPARATOR . 'composer.json', GLOB_NOSORT) ?: [];

			foreach ($files as $file) {
				/** @var Entities\IPackage $package */
				if ($package = $this->loader->load($file)) {
					$this->packages[$package->getName()] = $package;
				}
			}
		}
	}
}
