<?php
/**
 * Job.php
 *
 * @copyright      More in license.md
 * @license        http://www.ipublikuj.eu
 * @author         Adam Kadlec http://www.ipublikuj.eu
 * @package        iPublikuj:Packages!
 * @subpackage     DependencyResolver
 * @since          2.0.0
 *
 * @date           27.06.16
 */

declare(strict_types = 1);

namespace IPub\Packages\DependencyResolver;

use IPub;
use IPub\Packages;
use IPub\Packages\Entities;
use IPub\Packages\Exceptions;

/**
 * Dependency solver job definition
 *
 * @package        iPublikuj:Packages!
 * @subpackage     DependencyResolver
 *
 * @author         Josef Kříž <pepakriz@gmail.com>
 * @author         Adam Kadlec <adam.kadlec@ipublikuj.eu>
 */
class Job
{
	/**
	 * Define actions
	 */
	const ACTION_ENABLE = 'enable';
	const ACTION_DISABLE = 'disable';

	/**
	 * List of available statuses
	 */
	const ACTIONS = [
		self::ACTION_ENABLE,
		self::ACTION_DISABLE,
	];

	/**
	 * @var string
	 */
	private $action;

	/**
	 * @var Entities\IPackage
	 */
	private $package;

	/**
	 * @param string $action
	 * @param Entities\IPackage $package
	 *
	 * @throws Exceptions\InvalidJobActionException
	 */
	public function __construct(string $action, Entities\IPackage $package)
	{
		if (!in_array($action, self::ACTIONS, TRUE)) {
			throw new Exceptions\InvalidJobActionException(sprintf(
				'Action must be one of "%s". "%s" is given.',
				join(', ', self::ACTIONS),
				$action
			));
		}

		$this->action = $action;
		$this->package = $package;
	}

	/**
	 * @return string
	 */
	public function getAction() : string
	{
		return $this->action;
	}

	/**
	 * @return Entities\IPackage
	 */
	public function getPackage() : Entities\IPackage
	{
		return $this->package;
	}
}
