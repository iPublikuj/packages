<?php
/**
 * Command.php
 *
 * @copyright      More in license.md
 * @license        http://www.ipublikuj.eu
 * @author         Adam Kadlec http://www.ipublikuj.eu
 * @package        iPublikuj:Packages!
 * @subpackage     Commands
 * @since          2.0.0
 *
 * @date           19.07.16
 */

declare(strict_types = 1);

namespace IPub\Packages\Commands;

use Symfony\Component\Console;

use IPub;
use IPub\Packages;
use IPub\Packages\Repository;

/**
 * Command envelope
 *
 * @package        iPublikuj:Packages!
 * @subpackage     Commands
 *
 * @author         Adam Kadlec <adam.kadlec@fastybird.com>
 */
abstract class Command extends Console\Command\Command
{
	/**
	 * @var Packages\IPackagesManager
	 */
	protected $packageManager;

	/**
	 * @var Repository\IRepository
	 */
	protected $repository;

	/**
	 * @param Packages\IPackagesManager $packageManager
	 * @param Repository\IRepository $repository
	 */
	public function __construct(
		Packages\IPackagesManager $packageManager,
		Repository\IRepository $repository
	) {
		parent::__construct();

		$this->packageManager = $packageManager;
		$this->repository = $repository;
	}
}
