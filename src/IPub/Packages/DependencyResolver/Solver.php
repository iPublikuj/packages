<?php
/**
 * Solver.php
 *
 * @copyright      More in license.md
 * @license        https://www.ipublikuj.eu
 * @author         Adam Kadlec <adam.kadlec@ipublikuj.eu>
 * @package        iPublikuj:Packages!
 * @subpackage     DependencyResolver
 * @since          2.0.0
 *
 * @date           27.06.16
 */

declare(strict_types = 1);

namespace IPub\Packages\DependencyResolver;

use IPub\Packages;
use IPub\Packages\Entities;
use IPub\Packages\Exceptions;
use IPub\Packages\Repository;

/**
 * Dependency solver
 *
 * @package        iPublikuj:Packages!
 * @subpackage     DependencyResolver
 *
 * @author         Josef Kříž <pepakriz@gmail.com>
 * @author         Adam Kadlec <adam.kadlec@ipublikuj.eu>
 */
class Solver
{
	/**
	 * @var Entities\IPackage[]
	 */
	private $packages;

	/**
	 * @var Entities\IPackage[]
	 */
	private $enabledPackages;

	/**
	 * @param Repository\IRepository $repository
	 * @param Packages\PackagesManager $manager
	 */
	public function __construct(
		Repository\IRepository $repository,
		Packages\PackagesManager $manager
	) {
		// Get all installed packages
		$this->packages = $repository->getPackages();

		// Get all enabled packages
		$this->enabledPackages = $repository->filterPackages(function (Entities\IPackage $package) use ($manager) : bool {
			return $manager->getStatus($package) === Entities\IPackage::STATE_ENABLED;
		});
	}

	/**
	 * @param Entities\IPackage $package
	 * @param Problem|NULL $problem
	 *
	 * @return void
	 */
	public function testEnable(Entities\IPackage $package, ?Problem $problem = NULL) : void
	{
		foreach ($package->getRequire() as $name) {
			// Check if required package is enabled...
			if (!isset($this->enabledPackages[$name])) {
				if ($problem && isset($this->packages[$name])) {
					//... if not try to check if is possible to enable it
					$this->testEnable($this->packages[$name], $problem);

					$job = new Job(Job::ACTION_ENABLE, $this->packages[$name]);

					if (!$problem->hasSolution($job)) {
						$problem->addSolution($job);
					}

					$this->enabledPackages[$name] = $this->packages[$name];

				} else {
					throw new Exceptions\InvalidArgumentException(sprintf(
						'Package "%s" depend on "%s", which was not found.',
						$package->getName(),
						$name
					));
				}
			}
		}
	}

	/**
	 * @param Entities\IPackage $package
	 * @param Problem|NULL $problem
	 *
	 * @return void
	 */
	public function testDisable(Entities\IPackage $package, ?Problem $problem = NULL) : void
	{
		foreach ($this->enabledPackages as $sourcePackage) {
			if ($sourcePackage->getName() === $package->getName()) {
				continue;
			}

			foreach ($sourcePackage->getRequire() as $name) {
				if ($name === $package->getName()) {
					if ($problem) {
						$this->testDisable($sourcePackage, $problem);

						$job = new Job(Job::ACTION_DISABLE, $sourcePackage);

						if (!$problem->hasSolution($job)) {
							$problem->addSolution($job);
						}

						unset($this->enabledPackages[$name]);

					} else {
						throw new Exceptions\InvalidArgumentException(sprintf(
							'Package "%s" depend on "%s".',
							$sourcePackage->getName(),
							$package->getName()
						));
					}
				}
			}
		}
	}
}
