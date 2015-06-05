<?php
/**
 * IRepository.php
 *
 * @copyright	More in license.md
 * @license		http://www.ipublikuj.eu
 * @author		Adam Kadlec http://www.ipublikuj.eu
 * @package		iPublikuj:Packages!
 * @subpackage	Repository
 * @since		5.0
 *
 * @date		30.05.15
 */

namespace IPub\Packages\Repository;

use IPub;
use IPub\Packages\Entities;

interface IRepository extends \Countable
{
	/**
	 * Checks if specified package registered.
	 *
	 * @param  Entities\IPackage $package
	 *
	 * @return bool
	 */
	public function hasPackage(Entities\IPackage $package);

	/**
	 * Searches for the first match of a package by name and version.
	 *
	 * @param  string $name
	 * @param  string $version
	 *
	 * @return Entities\IPackage|NULL
	 */
	public function findPackage($name, $version = 'latest');

	/**
	 * Searches for all packages matching a name and optionally a version.
	 *
	 * @param  string $name
	 * @param  string $version
	 *
	 * @return Entities\IPackage[]
	 */
	public function findPackages($name, $version = NULL);

	/**
	 * Filters all the packages through a callback.
	 *
	 * @param  callable $callback
	 * @param  string $class
	 *
	 * @return bool
	 */
	public function filterPackages(callable $callback, $class = 'IPub\Packages\Entities\Package');

	/**
	 * Returns list of registered packages.
	 *
	 * @return Entities\IPackage[]
	 */
	public function getPackages();

	/**
	 * Adds package to the repository.
	 *
	 * @param Entities\IPackage $package
	 */
	public function addPackage(Entities\IPackage $package);

	/**
	 * Removes package from the repository.
	 *
	 * @param Entities\IPackage $package
	 */
	public function removePackage(Entities\IPackage $package);
}