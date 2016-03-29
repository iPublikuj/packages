<?php
/**
 * InstalledRepository.php
 *
 * @copyright      More in license.md
 * @license        http://www.ipublikuj.eu
 * @author         Adam Kadlec http://www.ipublikuj.eu
 * @package        iPublikuj:Packages!
 * @subpackage     Repository
 * @since          1.0.0
 *
 * @date           30.05.15
 */

namespace IPub\Packages\Repository;

use IPub;
use IPub\Packages\Entities;

/**
 * Installed packages repository
 *
 * @package        iPublikuj:Packages!
 * @subpackage     Repository
 *
 * @author         Adam Kadlec <adam.kadlec@fastybird.com>
 */
class InstalledRepository extends ArrayRepository implements IInstalledRepository
{
	/**
	 * @var string
	 */
	protected $path;

	/**
	 * @param string $path
	 * @param array $packages
	 */
	public function __construct($path, array $packages = [])
	{
		parent::__construct($packages);

		$this->path = rtrim($path, DIRECTORY_SEPARATOR);
	}

	/**
	 * {@inheritdoc}
	 */
	public function getPath()
	{
		return $this->path;
	}

	/**
	 * {@inheritdoc}
	 */
	public function getInstallPath(Entities\IPackage $package)
	{
		return $this->path . DIRECTORY_SEPARATOR . $package->getName();
	}
}
