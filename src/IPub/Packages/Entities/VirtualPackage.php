<?php
/**
 * VirtualPackage.php
 *
 * @copyright      More in license.md
 * @license        https://www.ipublikuj.eu
 * @author         Adam Kadlec https://www.ipublikuj.eu
 * @package        iPublikuj:Packages!
 * @subpackage     Entities
 * @since          2.0.0
 *
 * @date           21.06.16
 */

declare(strict_types = 1);

namespace IPub\Packages\Entities;

/**
 * Virtual package entity
 *
 * @package        iPublikuj:Packages!
 * @subpackage     Entities
 *
 * @author         Adam Kadlec <adam.kadlec@ipublikuj.eu>
 */
final class VirtualPackage extends Package
{
	/**
	 * @var string
	 */
	private $path;

	/**
	 * @param mixed[] $composerData
	 * @param string $path
	 */
	public function __construct(array $composerData, string $path)
	{
		parent::__construct($composerData);

		$this->path = $path;
	}

	/**
	 * {@inheritdoc}
	 */
	public function getPath() : string
	{
		return $this->path;
	}
}
