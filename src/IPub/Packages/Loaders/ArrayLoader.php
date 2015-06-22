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

		if ($value = $this->checkStringConfig($config, 'title') AND $value !== FALSE) {
			$package->setTitle($value);
		}

		if ($value = $this->checkStringConfig($config, 'description') AND $value !== FALSE) {
			$package->setDescription($value);
		}

		if ($value = $this->checkArrayConfig($config, 'keywords') AND $value !== FALSE) {
			$package->setKeywords($config['keywords']);
		}

		if ($value = $this->checkStringConfig($config, 'homepage') AND $value !== FALSE) {
			$package->setHomepage($value);
		}

		if (!empty($config['license'])) {
			$package->setLicense(is_array($config['license']) ? $config['license'] : [$config['license']]);
		}

		if ($value = $this->checkArrayConfig($config, 'authors') AND $value !== FALSE) {
			$package->setAuthors($value);
		}

		if ($value = $this->checkArrayConfig($config, 'extra') AND $value !== FALSE) {
			$package->setExtra($value);
		}

		if (!empty($config['time'])) {
			try {
				$package->setReleaseDate(new Utils\DateTime($config['time'], new \DateTimeZone('UTC')));
			} catch (\Exception $ex) {
				// If date could not be converted to object than is in wrong format and is not added to package
			}
		}

		if (isset($config['source']) && $source = $config['source']) {
			if (!isset($source['type']) || !isset($source['url'])) {
				throw new Exceptions\UnexpectedValueException(sprintf("Package source should be specified as {\"type\": ..., \"url\": ...},\n%s given", json_encode($source)));
			}

			$package->setSourceType($source['type']);
			$package->setSourceUrl($source['url']);
		}

		if (isset($config['dist']) && $dist = $config['dist']) {
			if (!isset($dist['type']) || !isset($dist['url'])) {
				throw new Exceptions\UnexpectedValueException(sprintf("Package dist should be specified as {\"type\": ..., \"url\": ...},\n%s given", json_encode($dist)));
			}

			$package->setDistType($dist['type']);
			$package->setDistUrl($dist['url']);
			$package->setDistSha1Checksum(isset($dist['shasum']) ? $dist['shasum'] : NULL);
		}

		if ($value = $this->checkArrayConfig($config, 'autoload') AND $value !== FALSE) {
			$package->setAutoload($value);
		}

		if ($value = $this->checkArrayConfig($config, 'resources') AND $value !== FALSE) {
			$package->setResources($value);
		}

		return $package;
	}

	/**
	 * @param array $config
	 * @param string $key
	 *
	 * @return string|FALSE
	 */
	protected function checkStringConfig(array $config, $key)
	{
		return (isset($config[$key]) && !empty($config[$key]) && is_string($config[$key])) ? $config[$key] : FALSE;
	}

	/**
	 * @param array $config
	 * @param string $key
	 *
	 * @return array|FALSE
	 */
	protected function checkArrayConfig(array $config, $key)
	{
		return (isset($config[$key]) && !empty($config[$key]) && is_array($config[$key])) ? $config[$key] : FALSE;
	}
}