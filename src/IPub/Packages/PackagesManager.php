<?php
/**
 * PackagesManager.php
 *
 * @copyright	More in license.md
 * @license		http://www.ipublikuj.eu
 * @author		Adam Kadlec http://www.ipublikuj.eu
 * @package		iPublikuj:Packages!
 * @subpackage	common
 * @since		5.0
 *
 * @date		30.05.15
 */

namespace IPub\Packages;

use Nette;

use IPub;
use IPub\Packages;
use IPub\Packages\Entities;
use IPub\Packages\Installers;
use IPub\Packages\Repository;

abstract class PackagesManager extends Nette\Object implements \IteratorAggregate, \ArrayAccess
{
	/**
	 * @var Repository\IInstalledRepository
	 */
	protected $repository;

	/**
	 * @var Installers\IInstaller
	 */
	protected $installer;

	/**
	 * @var Entities\IPackage[]
	 */
	protected $loaded = [];

	/**
	 * @param Repository\IInstalledRepository $repository
	 * @param Installers\IInstaller $installer
	 */
	public function __construct(
		Repository\IInstalledRepository $repository,
		Installers\IInstaller $installer
	) {
		$this->repository = $repository;
		$this->installer = $installer;
	}

	/**
	 * Gets an instance by name
	 *
	 * @param  string $name
	 * 
	 * @return Entities\IPackage|NULL
	 */
	public function get($name)
	{
		return isset($this->loaded[$name]) ? $this->loaded[$name] : NULL;
	}

	/**
	 * @param Entities\IPackage $package
	 *
	 * @return $this
	 */
	public function addPackage(Entities\IPackage $package)
	{
		$this->loaded[$package->getName()] = $package;

		return $this;
	}

	/**
	 * Gets a repository instance
	 *
	 * @return Packages\Repository\IInstalledRepository
	 */
	public function getRepository()
	{
		return $this->repository;
	}

	/**
	 * Gets a installer instance
	 *
	 * @return Packages\Installers\IInstaller
	 */
	public function getInstaller()
	{
		return $this->installer;
	}

	/**
	 * Implements the \IteratorAggregate
	 *
	 * @return \Iterator
	 */
	public function getIterator()
	{
		return new \ArrayIterator($this->loaded);
	}

	/**
	 * Implements the \ArrayAccess
	 *
	 * @param string $offset
	 *
	 * @return bool
	 */
	public function offsetExists($offset)
	{
		return isset($this->loaded[$offset]);
	}

	/**
	 * Implements the \ArrayAccess
	 *
	 * @param string $offset
	 *
	 * @return Entities\IPackage|NULL
	 */
	public function offsetGet($offset)
	{
		return $this->loaded[$offset];
	}

	/**
	 * Implements the \ArrayAccess
	 *
	 * @param string $offset
	 * @param Entities\IPackage $value
	 *
	 * @return $this
	 */
	public function offsetSet($offset, $value)
	{
		$this->loaded[$offset] = $value;

		return $this;
	}

	/**
	 * Implements the \ArrayAccess
	 *
	 * @param string $offset
	 *
	 * @return $this
	 */
	public function offsetUnset($offset)
	{
		unset($this->loaded[$offset]);

		return $this;
	}
}