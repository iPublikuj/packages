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
use IPub\Packages\Installers;
use IPub\Packages\Repository;

/**
 * Packages manager
 *
 * @package        iPublikuj:Packages!
 * @subpackage     common
 *
 * @author         Adam Kadlec <adam.kadlec@fastybird.com>
 */
interface IPackagesManager
{
	/**
	 * Gets a repository instance
	 *
	 * @return Packages\Repository\IInstalledRepository
	 */
	function getRepository();

	/**
	 * Gets a installer instance
	 *
	 * @return Packages\Installers\IPackageInstaller
	 */
	function getInstaller();
}
