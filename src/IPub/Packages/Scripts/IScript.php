<?php
/**
 * IScript.php
 *
 * @copyright      More in license.md
 * @license        http://www.ipublikuj.eu
 * @author         Adam Kadlec http://www.ipublikuj.eu
 * @package        iPublikuj:Packages!
 * @subpackage     Scripts
 * @since          2.0.0
 *
 * @date           25.06.16
 */

declare(strict_types = 1);

namespace IPub\Packages\Scripts;

use IPub;
use IPub\Packages;
use IPub\Packages\Entities;

/**
 * Packages installers scripts interface
 *
 * @package      iPublikuj:Packages!
 * @subpackage   Scripts
 *
 * @author       Adam Kadlec <adam.kadlec@ipublikuj.eu>
 */
interface IScript
{
	/**
	 * @param Entities\IPackage $package
	 *
	 * @return void
	 */
	function install(Entities\IPackage $package);

	/**
	 * @param Entities\IPackage $package
	 *
	 * @return void
	 */
	function uninstall(Entities\IPackage $package);

	/**
	 * @param Entities\IPackage $package
	 *
	 * @return void
	 */
	function enable(Entities\IPackage $package);

	/**
	 * @param Entities\IPackage $package
	 *
	 * @return void
	 */
	function disable(Entities\IPackage $package);
}
