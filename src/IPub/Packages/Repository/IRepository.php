<?php
/**
 * IRepository.php
 *
 * @copyright    More in license.md
 * @license      http://www.ipublikuj.eu
 * @author       Adam Kadlec http://www.ipublikuj.eu
 * @package      iPublikuj:Packages!
 * @subpackage   Repository
 * @since        1.0.0
 *
 * @date         30.05.15
 */

declare(strict_types = 1);

namespace IPub\Packages\Repository;

use IPub;
use IPub\Packages\Entities;

/**
 * Packages repository interface
 *
 * @package      iPublikuj:Packages!
 * @subpackage   Repository
 *
 * @author       Adam Kadlec <adam.kadlec@ipublikuj.eu>
 */
interface IRepository extends \Countable, \ArrayAccess, \IteratorAggregate
{
	/**
	 * Checks if specified package registered
	 *
	 * @param Entities\IPackage $package
	 *
	 * @return bool
	 */
	function hasPackage(Entities\IPackage $package) : bool;

	/**
	 * Searches for the first match of a package by name and version
	 *
	 * @param string $name
	 * @param string $version
	 *
	 * @return Entities\IPackage|bool
	 */
	function findPackage(string $name, string $version = 'latest');

	/**
	 * Searches for all packages matching a name and optionally a version
	 *
	 * @param string $name
	 * @param string $version
	 *
	 * @return Entities\IPackage[]
	 */
	function findPackages(string $name, string $version = NULL) : array;

	/**
	 * Filters all the packages through a callback
	 *
	 * @param callable $callback
	 *
	 * @return Entities\IPackage[]
	 */
	function filterPackages(callable $callback) : array;

	/**
	 * Returns list of registered packages
	 *
	 * @return Entities\IPackage[]
	 */
	function getPackages() : array;

	/**
	 * Reload packages repository
	 *
	 * @return void
	 */
	function reload();

	/**
	 * Get the repository path
	 *
	 * @return array
	 */
	function getPaths() : array;

	/**
	 * Adds a package path(s)
	 *
	 * @param string|array $paths
	 */
	public function addPath($paths);
}
