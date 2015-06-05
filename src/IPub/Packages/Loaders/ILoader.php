<?php
/**
 * ILoader.php
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

interface ILoader
{
	/**
	 * Creates a package instance based on a given package config
	 *
	 * @param mixed  $config
	 * @param string $class
	 *
	 * @return Entities\IPackage
	 */
	public function load($config, $class = 'IPub\Packages\Entities\Package');
}
