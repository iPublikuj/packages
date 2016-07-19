<?php
/**
 * IInstaller.php
 *
 * @copyright      More in license.md
 * @license        http://www.ipublikuj.eu
 * @author         Adam Kadlec http://www.ipublikuj.eu
 * @package        iPublikuj:Packages!
 * @subpackage     Installers
 * @since          2.0.0
 *
 * @date           25.06.16
 */

declare(strict_types = 1);

namespace IPub\Packages\Installers;

/**
 * Package installer interface
 *
 * @package        iPublikuj:Packages!
 * @subpackage     Installers
 *
 * @author         Adam Kadlec <adam.kadlec@fastybird.com>
 */
interface IInstaller
{
	/**
	 * Define interface name
	 */
	const INTERFACE_NAME = __CLASS__;

	/**
	 * @param string|array $name
	 *
	 * @return void
	 */
	function install($name);

	/**
	 * @param string|array $name
	 *
	 * @return void
	 */
	function uninstall($name);

	/**
	 * @param string|array $name
	 *
	 * @return bool
	 */
	function isInstalled(string $name) : bool;
}
