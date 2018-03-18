<?php
/**
 * IPackage.php
 *
 * @copyright      More in license.md
 * @license        https://www.ipublikuj.eu
 * @author         Adam Kadlec https://www.ipublikuj.eu
 * @package        iPublikuj:Packages!
 * @subpackage     Entities
 * @since          1.0.0
 *
 * @date           27.09.14
 */

declare(strict_types = 1);

namespace IPub\Packages\Entities;

use Nette;
use Nette\Utils;

/**
 * Package interface
 *
 * @package        iPublikuj:Packages!
 * @subpackage     Entities
 *
 * @author         Adam Kadlec <adam.kadlec@ipublikuj.eu>
 */
interface IPackage
{
	/**
	 * Define statuses
	 */
	public const STATE_ENABLED = 'enabled';
	public const STATE_DISABLED = 'disabled';
	public const STATE_UNREGISTERED = 'unregistered';

	/**
	 * List of available statuses
	 */
	const STATUSES = [
		self::STATE_ENABLED,
		self::STATE_DISABLED,
	];

	/**
	 * @return string
	 */
	function getName() : string;

	/**
	 * @return string
	 */
	function getTitle() : string;

	/**
	 * @return bool
	 */
	function hasTitle() : bool;

	/**
	 * @return string|NULL
	 */
	function getDescription() : ?string;

	/**
	 * @return string
	 */
	function getVersion() : string;

	/**
	 * @return string
	 */
	function getType() : string;

	/**
	 * @return Utils\ArrayHash
	 */
	function getKeywords() : Utils\ArrayHash;

	/**
	 * @return string|NULL
	 */
	function getHomepage() : ?string;

	/**
	 * @return \DateTimeInterface|NULL
	 */
	function getReleaseDate() : ?\DateTimeInterface;

	/**
	 * @return Utils\ArrayHash
	 */
	function getLicense() : Utils\ArrayHash;

	/**
	 * @return Utils\ArrayHash
	 */
	function getAuthors() : Utils\ArrayHash;

	/**
	 * @return array|NULL
	 */
	function getSupport() : ?array;

	/**
	 * @return string[]
	 */
	public function getRequire() : array;

	/**
	 * @return string[]
	 */
	public function getRequireDev() : array;

	/**
	 * @return Utils\ArrayHash
	 */
	function getAutoload() : Utils\ArrayHash;

	/**
	 * @return Utils\ArrayHash
	 */
	public function getExtra() : Utils\ArrayHash;

	/**
	 * @return Utils\ArrayHash
	 */
	public function getConfiguration() : Utils\ArrayHash;

	/**
	 * @return Utils\ArrayHash
	 */
	public function getScripts() : Utils\ArrayHash;

	/**
	 * @return string
	 */
	function getUniqueName() : string;

	/**
	 * @return string
	 */
	function getPath() : string;

	/**
	 * @param IPackage $package
	 * @param string $operator
	 *
	 * @return bool
	 */
	function compare(IPackage $package, string $operator = '==') : bool;

	/**
	 * @return string
	 */
	function __toString() : string;
}
