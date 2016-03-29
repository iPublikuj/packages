<?php
/**
 * IInstalledRepository.php
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
 * Installed packages repository interface
 *
 * @package        iPublikuj:Packages!
 * @subpackage     Repository
 *
 * @author         Adam Kadlec <adam.kadlec@fastybird.com>
 */
interface IInstalledRepository extends IRepository
{
	/**
	 * Get the repository path
	 *
	 * @return string
	 */
	function getPath();

	/**
	 * Checks if specified package registered
	 *
	 * @param Entities\IPackage $package
	 *
	 * @return string
	 */
	function getInstallPath(Entities\IPackage $package);
}
