<?php
/**
 * ILoader.php
 *
 * @copyright      More in license.md
 * @license        https://www.ipublikuj.eu
 * @author         Adam Kadlec https://www.ipublikuj.eu
 * @package        iPublikuj:Packages!
 * @subpackage     Loaders
 * @since          1.0.0
 *
 * @date           30.09.14
 */

declare(strict_types = 1);

namespace IPub\Packages\Loaders;

use IPub\Packages\Entities;

/**
 * Package loader interface
 *
 * @package        iPublikuj:Packages!
 * @subpackage     Loaders
 *
 * @author         Adam Kadlec <adam.kadlec@ipublikuj.eu>
 */
interface ILoader
{
	/**
	 * Creates a package instance based on a given package config
	 *
	 * @param string $file
	 *
	 * @return Entities\IPackage
	 */
	public function load(string $file) : Entities\IPackage;
}
