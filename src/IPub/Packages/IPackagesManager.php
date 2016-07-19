<?php
/**
 * IPackagesManager.php
 *
 * @copyright      More in license.md
 * @license        http://www.ipublikuj.eu
 * @author         Adam Kadlec http://www.ipublikuj.eu
 * @package        iPublikuj:Packages!
 * @subpackage     common
 * @since          1.0.0
 *
 * @date           13.03.16
 */

namespace IPub\Packages;

use Nette;

use IPub;
use IPub\Packages;
use IPub\Packages\Entities;

/**
 * Packages manager interface
 *
 * @package        iPublikuj:Packages!
 * @subpackage     common
 *
 * @author         Adam Kadlec <adam.kadlec@fastybird.com>
 */
interface IPackagesManager
{
	/**
	 * Define interface name
	 */
	const INTERFACE_NAME = __CLASS__;

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
	 * @return string[]
	 */
	function registerAvailable() : array;

	/**
	 * @return string[]
	 */
	function enableAvailable() : array;

	/**
	 * @return string[]
	 */
	function disableAbsent() : array;

	/**
	 * @param string $name
	 * @param bool $packagist
	 * @param bool $preferSource
	 */
	function install(string $name, bool $packagist = FALSE, bool $preferSource = TRUE);

	/**
	 * @param string $name
	 */
	function uninstall(string $name);

	/**
	 * @param Entities\IPackage $package
	 */
	function enable(Entities\IPackage $package);

	/**
	 * @param Entities\IPackage $package
	 */
	function disable(Entities\IPackage $package);

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