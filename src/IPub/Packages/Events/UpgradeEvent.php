<?php
/**
 * UpgradeEvent.php
 *
 * @copyright      More in license.md
 * @license        https://www.ipublikuj.eu
 * @author         Adam Kadlec <adam.kadlec@ipublikuj.eu>
 * @package        iPublikuj:Packages!
 * @subpackage     Events
 * @since          1.0.0
 *
 * @date           15.11.19
 */

namespace IPub\Packages\Events;

use Symfony\Contracts\EventDispatcher;

use IPub\Packages;
use IPub\Packages\Entities;

/**
 * Package upgraded event
 *
 * @package        iPublikuj:Packages!
 * @subpackage     Events
 *
 * @author         Adam Kadlec <adam.kadlec@ipublikuj.eu>
 */
final class UpgradeEvent extends EventDispatcher\Event
{
	/**
	 * @var Packages\PackagesManager
	 */
	private $manager;

	/**
	 * @var Entities\IPackage
	 */
	private $package;

	/**
	 * @param Packages\PackagesManager $manager
	 * @param Entities\IPackage $package
	 */
	public function __construct(
		Packages\PackagesManager $manager,
		Entities\IPackage $package
	) {
		$this->manager = $manager;
		$this->package = $package;
	}

	/**
	 * @return Packages\PackagesManager
	 */
	public function getManager() : Packages\PackagesManager
	{
		return $this->manager;
	}

	/**
	 * @return Entities\IPackage
	 */
	public function getPackage() : Entities\IPackage
	{
		return $this->package;
	}
}
