<?php
/**
 * PackageDownloader.php
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

use Nette;
use Nette\Utils;

use IPub;
use IPub\Packages\Entities;
use IPub\Packages\Exceptions;

use GuzzleHttp;

class PackageDownloader implements IDownloader
{
	/**
	 * @var GuzzleHttp\ClientInterface
	 */
	protected $client;

	/**
	 * @param GuzzleHttp\ClientInterface $client
	 */
	public function __construct(GuzzleHttp\ClientInterface $client = NULL)
	{
		$this->client = $client ?: new GuzzleHttp\Client;
	}

	/**
	 * {@inheritdoc}
	 */
	public function download(Entities\IPackage $package, $path)
	{
		if (!$url = $package->getDistUrl()) {
			throw new Exceptions\UnexpectedValueException("The given package is missing url information");
		}

		$this->downloadFile($path, $url, $package->getDistSha1Checksum());
	}

	/**
	 * Download a package file
	 *
	 * @param string $path
	 * @param string $url
	 * @param string $shasum
	 *
	 * @throws Exceptions\ArchiveExtractionException
	 * @throws Exceptions\ChecksumVerificationException
	 * @throws Exceptions\NotWritableException
	 * @throws Exceptions\UnauthorizedDownloadException
	 * @throws \Exception
	 */
	public function downloadFile($path, $url, $shasum = '')
	{
		$file = $path.'/'.uniqid();

		try {

			$data = $this->client->get($url)->getBody();

			if ($shasum && sha1($data) !== $shasum) {
				throw new Exceptions\ChecksumVerificationException("The file checksum verification failed");
			}

			if (!Utils\FileSystem::createDir($path) || !Utils\FileSystem::write($file, $data)) {
				throw new Exceptions\NotWritableException("The path is not writable ($path)");
			}

			$zip = new \ZipArchive;
			$zip->open($file);

			if ($zip->extractTo($path) !== TRUE) {
				throw new Exceptions\ArchiveExtractionException("The file extraction failed");
			}

			Utils\FileSystem::delete($file);

		} catch (\Exception $ex) {

			Utils\FileSystem::delete($path);

			if ($ex instanceof GuzzleHttp\Exception\TransferException) {

				if ($ex instanceof GuzzleHttp\Exception\BadResponseException) {
					throw new Exceptions\UnauthorizedDownloadException("Unauthorized download ($url)");
				}

				throw new Exceptions\DownloadErrorException("The file download failed ($url)");
			}

			throw $ex;
		}
	}
}
