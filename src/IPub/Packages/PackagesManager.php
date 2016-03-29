<?php
/**
 * PackagesManager.php
 *
 * @copyright      More in license.md
 * @license        http://www.ipublikuj.eu
 * @author         Adam Kadlec http://www.ipublikuj.eu
 * @package        iPublikuj:Packages!
 * @subpackage     common
 * @since          1.0.0
 *
 * @date           30.05.15
 */

namespace IPub\Packages;

use Nette;

use IPub;
use IPub\Packages;
use IPub\Packages\Installers;
use IPub\Packages\Repository;

/**
 * Packages manager
 *
 * @package        iPublikuj:Packages!
 * @subpackage     common
 *
 * @author         Adam Kadlec <adam.kadlec@fastybird.com>
 */
abstract class PackagesManager extends Nette\Object implements IPackagesManager
{
	/**
	 * @var Repository\IInstalledRepository
	 */
	protected $repository;

	/**
	 * @var Installers\IPackageInstaller
	 */
	protected $installer;

	/**
	 * @param Repository\IInstalledRepository $repository
	 * @param Installers\IPackageInstaller $installer
	 */
	public function __construct(
		Repository\IInstalledRepository $repository,
		Installers\IPackageInstaller $installer
	) {
		$this->repository = $repository;
		$this->installer = $installer;
	}

	/**
	 * @inheritdoc
	 */
	public function getRepository()
	{
		return $this->repository;
	}

	/**
	 * @inheritdoc
	 */
	public function getInstaller()
	{
		return $this->installer;
	}
}
