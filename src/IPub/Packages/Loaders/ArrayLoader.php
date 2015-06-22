<?php
/**
 * ArrayLoader.php
 *
 * @copyright	More in license.md
 * @license		http://www.ipublikuj.eu
 * @author		Adam Kadlec http://www.ipublikuj.eu
 * @package		iPublikuj:Packages!
 * @subpackage	Loaders
 * @since		5.0
 *
 * @date		30.09.14
 */

namespace IPub\Packages\Loaders;

use Nette;
use Nette\Utils;

use IPub;
use IPub\Packages;
use IPub\Packages\Entities;
use IPub\Packages\Exceptions;
use IPub\Packages\Version;

class ArrayLoader implements ILoader
{
	/**
	 * {@inheritdoc}
	 */
	public function load($config, $class = 'IPub\Packages\Entities\Package')
	{
		if (!is_array($config)) {
			throw new Exceptions\InvalidArgumentException('Package config needs to be an array.');
		}

		if (!isset($config['name'])) {
			throw new Exceptions\UnexpectedValueException('Unknown package has no name defined ('.json_encode($config).').');
		}

		if (!isset($config['version'])) {
			throw new Exceptions\UnexpectedValueException('Package "'.$config['name'].'" has no version defined.');
		}

		if (!Version\Validator::validate($config['version'])) {
			throw new Exceptions\UnexpectedValueException('Package "'.$config['name'].'" has invalid version defined "'.$config['version'].'".');
		}

		/** @var Entities\IPackage $package */
		$package = new $class($config['name'], $config['version']);
		$package->setType($config['package']);

		if ($this->checkStringConfig($config['title']) !== FALSE) {
			$package->setTitle($config['title']);
		}

		if ($this->checkStringConfig($config['description']) !== FALSE) {
			$package->setDescription($config['description']);
		}

		if ($this->checkArrayConfig($config['keywords']) !== FALSE) {
			$package->setKeywords($config['keywords']);
		}

		if ($this->checkStringConfig($config['homepage']) !== FALSE) {
			$package->setHomepage($config['homepage']);
		}

		if (!empty($config['license'])) {
			$package->setLicense(is_array($config['license']) ? $config['license'] : [$config['license']]);
		}

		if ($this->checkArrayConfig($config['authors']) !== FALSE) {
			$package->setAuthors($config['authors']);
		}

		if ($this->checkArrayConfig($config['extra']) !== FALSE) {
			$package->setExtra($config['extra']);
		}

		if (!empty($config['time'])) {
			try {
				$package->setReleaseDate(new Utils\DateTime($config['time'], new \DateTimeZone('UTC')));
			} catch (\Exception $ex) {
				// If date could not be converted to object than is in wrong format and is not added to package
			}
		}

		if (isset($config['source'])) {
			if (!isset($config['source']['type']) || !isset($config['source']['url'])) {
				throw new Exceptions\UnexpectedValueException(sprintf("Package source should be specified as {\"type\": ..., \"url\": ...},\n%s given", json_encode($config['source'])));
			}

			$package->setSourceType($config['source']['type']);
			$package->setSourceUrl($config['source']['url']);
		}

		if (isset($config['dist'])) {
			if (!isset($config['dist']['type']) || !isset($config['dist']['url'])) {
				throw new Exceptions\UnexpectedValueException(sprintf("Package dist should be specified as {\"type\": ..., \"url\": ...},\n%s given", json_encode($config['dist'])));
			}

			$package->setDistType($config['dist']['type']);
			$package->setDistUrl($config['dist']['url']);
			$package->setDistSha1Checksum(isset($config['dist']['shasum']) ? $config['dist']['shasum'] : NULL);
		}

		if ($this->checkArrayConfig($config['autoload']) !== FALSE) {
			$package->setAutoload($config['autoload']);
		}

		if ($this->checkArrayConfig($config['resources']) !== FALSE) {
			$package->setResources($config['resources']);
		}

		return $package;
	}

	/**
	 * @param string $value
	 *
	 * @return string|FALSE
	 */
	protected function checkStringConfig($value)
	{
		return (!empty($value) && is_string($value)) ? $value : FALSE;
	}

	/**
	 * @param string $value
	 *
	 * @return array|FALSE
	 */
	protected function checkArrayConfig($value)
	{
		return (!empty($value) && is_array($value)) ? $value : FALSE;
	}
}