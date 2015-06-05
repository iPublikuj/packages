<?php
/**
 * IInstalledRepository.php
 *
 * @copyright	More in license.md
 * @license		http://www.ipublikuj.eu
 * @author		Adam Kadlec http://www.ipublikuj.eu
 * @package		iPublikuj:Packages!
 * @subpackage	Repository
 * @since		5.0
 *
 * @date		30.05.15
 */

namespace IPub\Packages\Repository;

use IPub;
use IPub\Packages\Entities;

interface IInstalledRepository extends IRepository
{
	/**
	 * Checks if specified package registered.
	 *
	 * @param  Entities\IPackage $package
	 *
	 * @return string
	 */
	public function getInstallPath(Entities\IPackage $package);
}