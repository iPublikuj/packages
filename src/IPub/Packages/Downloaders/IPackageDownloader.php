<?php
/**
 * IDownloader.php
 *
 * @copyright      More in license.md
 * @license        http://www.ipublikuj.eu
 * @author         Adam Kadlec http://www.ipublikuj.eu
 * @package        iPublikuj:Packages!
 * @subpackage     Downloaders
 * @since          1.0.0
 *
 * @date           30.05.15
 */

namespace IPub\Packages\Downloaders;

use IPub;
use IPub\Packages\Entities;
use IPub\Packages\Exceptions;

/**
 * Package downloader interface
 *
 * @package        iPublikuj:Packages!
 * @subpackage     Downloaders
 *
 * @author         Adam Kadlec <adam.kadlec@fastybird.com>
 */
interface IPackageDownloader
{
	/**
	 * Downloads specific package into specific folder
	 *
	 * @param  Entities\IPackage $package
	 * @param  string $path
	 *
	 * @throws Exceptions\DownloadErrorException
	 */
	function download(Entities\IPackage $package, $path);
}
