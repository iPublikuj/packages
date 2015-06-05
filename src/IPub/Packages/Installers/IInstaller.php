<?php
/**
 * IInstaller.php
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

use IPub;
use IPub\Packages\Entities;
use IPub\Packages\Exceptions;

interface IInstaller
{
	/**
	 * Installs specific package
	 *
	 * @param string $packageFile
	 * 
	 * @throws Exceptions\LogicException
	 */
	public function install($packageFile);

	/**
	 * Updates specific package
	 *
	 * @param string $packageFile
	 * 
	 * @throws Exceptions\LogicException
	 */
	public function update($packageFile);

	/**
	 * Uninstalls specific package.
	 *
	 * @param Entities\IPackage $package
	 * 
	 * @throws Exceptions\LogicException
	 */
	public function uninstall(Entities\IPackage $package);

	/**
	 * Checks that provided package is installed
	 *
	 * @param Entities\IPackage $package
	 * 
	 * @return bool
	 */
	public function isInstalled(Entities\IPackage $package);
}
