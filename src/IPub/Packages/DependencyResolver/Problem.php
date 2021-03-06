<?php
/**
 * Problem.php
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

use IPub\Packages\Exceptions;

/**
 * Dependency solver problem definition
 *
 * @package        iPublikuj:Packages!
 * @subpackage     DependencyResolver
 *
 * @author         Josef Kříž <pepakriz@gmail.com>
 * @author         Adam Kadlec <adam.kadlec@ipublikuj.eu>
 */
class Problem
{
	/**
	 * @var Job[]
	 */
	private $solutions = [];

	/**
	 * @param Job $job
	 *
	 * @return void
	 */
	public function addSolution(Job $job) : void
	{
		if ($this->hasSolution($job)) {
			throw new Exceptions\InvalidArgumentException(sprintf(
				'Solution "%s:%s" is already added.',
				$job->getPackage()->getName(),
				$job->getAction()
			));
		}

		$this->solutions[$job->getPackage()->getName()] = $job;
	}

	/**
	 * @param Job $job
	 *
	 * @return bool
	 */
	public function hasSolution(Job $job) : bool
	{
		return isset($this->solutions[$job->getPackage()->getName()]);
	}

	/**
	 * @return Job[]
	 */
	public function getSolutions() : array
	{
		return array_merge($this->solutions);
	}
}
