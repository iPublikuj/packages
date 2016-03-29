<?php
/**
 * JsonLoader.php
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

use IPub;
use IPub\Packages;
use IPub\Packages\Entities;
use IPub\Packages\Exceptions;

/**
 * Package JSON loader
 *
 * @package        iPublikuj:Packages!
 * @subpackage     Loaders
 *
 * @author         Adam Kadlec <adam.kadlec@fastybird.com>
 */
class JsonLoader extends ArrayLoader
{
	/**
	 * @param mixed $json a file or json string
	 * @param string $class
	 *
	 * @return Entities\IPackage
	 *
	 * @throws Exceptions\InvalidArgumentException
	 */
	public function load($json, $class = 'IPub\Packages\Entities\Package')
	{
		$json = (string) $json;

		if (strpos($json, '{') !== FALSE && !is_file($json)) {
			$config = json_decode($json, TRUE);

		} else if (is_file($json)) {
			$config = json_decode(file_get_contents($json), TRUE);
		}

		if (!isset($config) || !is_array($config)) {
			throw new Exceptions\InvalidArgumentException('Unable to load json.');
		}

		return parent::load($config, $class);
	}
}
