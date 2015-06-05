<?php
/**
 * IDownloader.php
 *
 * @copyright	More in license.md
 * @license		http://www.ipublikuj.eu
 * @author		Adam Kadlec http://www.ipublikuj.eu
 * @package		iPublikuj:Packages!
 * @subpackage	Downloaders
 * @since		5.0
 *
 * @date		30.05.15
 */

namespace IPub\Packages\Downloaders;

use IPub;
use IPub\Packages\Entities;
use IPub\Packages\Exceptions;

interface IDownloader
{
	/**
	 * Downloads specific package into specific folder
	 *
	 * @param  Entities\IPackage $package
	 * @param  string $path
	 *
	 * @throws Exceptions\DownloadErrorException
	 */
	public function download(Entities\IPackage $package, $path);
}
