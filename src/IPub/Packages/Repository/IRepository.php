<?php
/**
 * IRepository.php
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
 * Packages repository interface
 *
 * @package        iPublikuj:Packages!
 * @subpackage     Repository
 *
 * @author         Adam Kadlec <adam.kadlec@fastybird.com>
 */
interface IRepository extends \Countable
{
	/**
	 * Checks if specified package registered
	 *
	 * @param  Entities\IPackage $package
	 *
	 * @return bool
	 */
	function hasPackage(Entities\IPackage $package);

	/**
	 * Searches for the first match of a package by name and version
	 *
	 * @param  string $name
	 * @param  string $version
	 *
	 * @return Entities\IPackage|FALSE
	 */
	function findPackage($name, $version = 'latest');

	/**
	 * Searches for all packages matching a name and optionally a version
	 *
	 * @param  string $name
	 * @param  string $version
	 *
	 * @return Entities\IPackage[]
	 */
	function findPackages($name, $version = NULL);

	/**
	 * Filters all the packages through a callback
	 *
	 * @param  callable $callback
	 * @param  string $class
	 *
	 * @return bool
	 */
	function filterPackages(callable $callback, $class = 'IPub\Packages\Entities\Package');

	/**
	 * Returns list of registered packages
	 *
	 * @return Entities\IPackage[]
	 */
	function getPackages();

	/**
	 * Adds package to the repository
	 *
	 * @param Entities\IPackage $package
	 *
	 * @return Entities\IPackage $package
	 */
	function addPackage(Entities\IPackage $package);

	/**
	 * Removes package from the repository
	 *
	 * @param Entities\IPackage $package
	 */
	function removePackage(Entities\IPackage $package);
}
