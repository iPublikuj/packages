<?php
/**
 * PathResolver.php
 *
 * @copyright      More in license.md
 * @license        https://www.ipublikuj.eu
 * @author         Adam Kadlec <adam.kadlec@ipublikuj.eu>
 * @package        iPublikuj:Packages!
 * @subpackage     Helpers
 * @since          1.0.0
 *
 * @date           19.06.16
 */

declare(strict_types = 1);

namespace IPub\Packages\Helpers;

use Nette;
use Nette\Utils;

use IPub\Packages\Exceptions;
use IPub\Packages\Repository;

/**
 * Package path resolver
 *
 * @package        iPublikuj:Packages!
 * @subpackage     Helpers
 *
 * @author         Adam Kadlec <adam.kadlec@ipublikuj.eu>
 */
final class PathResolver
{
	/**
	 * Implement nette smart magic
	 */
	use Nette\SmartObject;

	/**
	 * @var Repository\IRepository
	 */
	private $repository;

	/**
	 * @param Repository\IRepository $repository
	 */
	public function __construct(Repository\IRepository $repository)
	{
		$this->repository = $repository;
	}

	/**
	 * Expands @foo/path/....
	 *
	 * @param string $path
	 * @param string|NULL $localPrefix
	 *
	 * @return string
	 */
	public function expandPath(string $path, string $localPrefix = NULL) : string
	{
		$path = Utils\Strings::replace($path, '~\\\~', '/');

		if (substr($path, 0, 1) !== '@') {
			return $path;
		}

		$pos = strpos($path, '/');

		if ($pos) {
			$package = substr($path, 1, $pos - 1);

		} else {
			$package = substr($path, 1);
		}

		$package = str_replace('.', '/', $package);

		if (!$this->repository->findPackage($package)) {
			throw new Exceptions\InvalidArgumentException(sprintf('Package \'%s\' does not exist.', $package));
		}

		$path = $this->repository->findPackage($package)->getPath() . ($localPrefix ? '/' . $localPrefix : '') . ($pos ? substr($path, $pos) : '');

		return Utils\Strings::replace($path, '~\\\~', '/');
	}
}
