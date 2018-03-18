<?php
/**
 * IPackagesManager.php
 *
 * @copyright      More in license.md
 * @license        https://www.ipublikuj.eu
 * @author         Adam Kadlec https://www.ipublikuj.eu
 * @package        iPublikuj:Packages!
 * @subpackage     common
 * @since          1.0.0
 *
 * @date           13.03.16
 */

declare(strict_types = 1);

namespace IPub\Packages;

use Nette;

use IPub\Packages;
use IPub\Packages\Entities;

/**
 * Packages manager interface
 *
 * @package        iPublikuj:Packages!
 * @subpackage     common
 *
 * @author         Adam Kadlec <adam.kadlec@ipublikuj.eu>
 */
interface IPackagesManager
{
	/**
	 * @param Entities\IPackage $package
	 *
	 * @return string
	 */
	function getStatus(Entities\IPackage $package) : string;

	/**
	 * @param Entities\IPackage $package
	 *
	 * @return string
	 */
	function getVersion(Entities\IPackage $package) : string;

	/**
	 * @param Entities\IPackage $first
	 * @param Entities\IPackage $second
	 * @param string $operator
	 *
	 * @return bool
	 */
	function comparePackages(Entities\IPackage $first, Entities\IPackage $second, string $operator = '==') : bool;

	/**
	 * @param string $name
	 * @param Scripts\IScript $service
	 */
	function addScript(string $name, Scripts\IScript $service);

	/**
	 * @return array[]
	 */
	function registerAvailable() : array;

	/**
	 * @return array[]
	 */
	function enableAvailable() : array;

	/**
	 * @return array[]
	 */
	function disableAbsent() : array;

	/**
	 * @param string $name
	 *
	 * @return void
	 */
	function install(string $name) : void;

	/**
	 * @param string $name
	 *
	 * @return void
	 */
	function uninstall(string $name) : void;

	/**
	 * @param Entities\IPackage $package
	 *
	 * @return void
	 */
	function enable(Entities\IPackage $package) : void;

	/**
	 * @param Entities\IPackage $package
	 *
	 * @return void
	 */
	function disable(Entities\IPackage $package) : void;

	/**
	 * @param Entities\IPackage $package
	 *
	 * @return DependencyResolver\Problem
	 */
	function testEnable(Entities\IPackage $package) : DependencyResolver\Problem;

	/**
	 * @param Entities\IPackage $package
	 *
	 * @return DependencyResolver\Problem
	 */
	function testDisable(Entities\IPackage $package) : DependencyResolver\Problem;
}
