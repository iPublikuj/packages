<?php
/**
 * InstalledRepository.php
 *
 * @copyright	More in license.md
 * @license		http://www.ipublikuj.eu
 * @author		Adam Kadlec http://www.ipublikuj.eu
 * @package		iPublikuj:Packages!
 * @subpackage	Repository
 * @since		5.0
 *
 * @date		30.05.15
 */

namespace IPub\Packages\Repository;

use IPub;
use IPub\Packages\Entities;

class InstalledRepository extends ArrayRepository implements IInstalledRepository
{
	/**
	 * @var string
	 */
	protected $path;

	/**
	 * @param string $path
	 */
	public function __construct($path)
	{
		parent::__construct();

		$this->path = rtrim($path, '\/');
	}

	/**
	 * Get the repository path
	 *
	 * return string
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
		return $this->path.'/'.$package->getName();
	}
}
