<?php
/**
 * IInstaller.php
 *
 * @copyright      More in license.md
 * @license        https://www.ipublikuj.eu
 * @author         Adam Kadlec <adam.kadlec@ipublikuj.eu>
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
 * @author         Adam Kadlec <adam.kadlec@ipublikuj.eu>
 */
interface IInstaller
{
	/**
	 * @param string|array $name
	 *
	 * @return void
	 */
	function install($name) : void;

	/**
	 * @param string|array $name
	 *
	 * @return void
	 */
	function uninstall($name) : void;

	/**
	 * @param string|array $name
	 *
	 * @return bool
	 */
	function isInstalled(string $name) : bool;
}
