<?php
/**
 * Loader.php
 *
 * @copyright      More in license.md
 * @license        https://www.ipublikuj.eu
 * @author         Adam Kadlec https://www.ipublikuj.eu
 * @package        iPublikuj:Packages!
 * @subpackage     Loaders
 * @since          1.0.0
 *
 * @date           30.05.15
 */

declare(strict_types = 1);

namespace IPub\Packages\Loaders;

use Composer;
use Composer\Semver;

use Nette;
use Nette\DI;
use Nette\Utils;

use IPub\Packages\Entities;
use IPub\Packages\Exceptions;

/**
 * Package loader
 *
 * @package        iPublikuj:Packages!
 * @subpackage     Loaders
 *
 * @author         Adam Kadlec <adam.kadlec@ipublikuj.eu>
 */
final class Loader implements ILoader
{
	/**
	 * Implement nette smart magic
	 */
	use Nette\SmartObject;

	/**
	 * @var string[]
	 */
	private $packageFiles = [];

	/**
	 * @var array
	 */
	private $metadataSources = [];

	/**
	 * @var string
	 */
	private $vendorDir;

	/**
	 * @var array|NULL
	 */
	private $globalMetadata;

	/**
	 * @var Semver\VersionParser
	 */
	private $versionParser;

	/**
	 * @var DI\Container
	 */
	private $container;

	/**
	 * @param array $packageFiles
	 * @param array $metadataSources
	 * @param $vendorDir
	 * @param DI\Container $container
	 */
	public function __construct(array $packageFiles = [], array $metadataSources = [], $vendorDir, DI\Container $container)
	{
		$this->packageFiles = $packageFiles;
		$this->metadataSources = $metadataSources;
		$this->vendorDir = $vendorDir;
		$this->versionParser = new Semver\VersionParser;

		$this->container = $container;
	}

	/**
	 * @param string $file
	 *
	 * @return Entities\IPackage
	 *
	 * @throws Exceptions\InvalidPackageDefinitionException
	 * @throws Exceptions\InvalidStateException
	 */
	public function load(string $file) : Entities\IPackage
	{
		$path = dirname($file);

		try {
			$data = Utils\Json::decode(file_get_contents($file), Utils\Json::FORCE_ARRAY);

		} catch (Utils\JsonException $ex) {
			throw new Exceptions\InvalidPackageDefinitionException(sprintf('The file "%s" has invalid JSON format.', $file));
		}

		$tmpPackage = new Entities\VirtualPackage($data, $path);

		if (($metadata = $this->getGlobalMetadata($tmpPackage)) !== []) {
			$data = Utils\Arrays::mergeTree($data, [
				'extra' => [
					'ipub' => $metadata,
				]
			]);
		}

		foreach ($this->packageFiles as $packageFile) {
			if (is_file($path . DIRECTORY_SEPARATOR . $packageFile)) {
				$class = $this->getPackageClassByFile($path . DIRECTORY_SEPARATOR . $packageFile);

				include_once $path . DIRECTORY_SEPARATOR . $packageFile;

				$package = $this->container->createInstance($class, [$data]);

				return $package;
			}
		}

		return new Entities\VirtualPackage($data, $path);
	}

	/**
	 * @param string $file
	 *
	 * @return string
	 */
	private function getPackageClassByFile(string $file) : string
	{
		$classes = $this->getClassesFromFile($file);

		if (count($classes) !== 1) {
			throw new Exceptions\InvalidArgumentException(sprintf('File \'%s\' must contain only one class.', $file));
		}

		return $classes[0];
	}

	/**
	 * @param Entities\IPackage $package
	 *
	 * @return array
	 *
	 * @throws Exceptions\InvalidMetadataSourceDefinitionException
	 * @throws Exceptions\InvalidStateException
	 */
	private function getGlobalMetadata(Entities\IPackage $package) : array
	{
		if ($this->globalMetadata === NULL) {
			$this->globalMetadata = [];

			foreach ($this->metadataSources as $source) {
				if (substr($source, 0, 7) === 'http://' || substr($source, 0, 8) === 'https://') {
					$ch = curl_init();

					curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, FALSE);
					curl_setopt($ch, CURLOPT_RETURNTRANSFER, TRUE);
					curl_setopt($ch, CURLOPT_FOLLOWLOCATION, TRUE);
					curl_setopt($ch, CURLOPT_URL, $source);

					$data = curl_exec($ch);

				} else {
					$data = file_get_contents($source);
				}

				if (!$data) {
					throw new Exceptions\InvalidStateException(sprintf('Source \'$source\' is empty.', $source));
				}

				if ($data) {
					try {
						$data = Utils\Json::decode($data, Utils\Json::FORCE_ARRAY);

					} catch (Utils\JsonException $ex) {
						throw new Exceptions\InvalidMetadataSourceDefinitionException(sprintf('The global metadata source "%s" has invalid JSON format.', $source));
					}

					$this->globalMetadata = Utils\Arrays::mergeTree($this->globalMetadata, $data);
				}
			}

		}

		if (!isset($this->globalMetadata[$package->getName()])) {
			return [];
		}

		$versionProvide = new Semver\Constraint\Constraint('==', $package->getVersion());

		foreach ($this->globalMetadata[$package->getName()] as $data) {
			$versionRequire = $this->versionParser->parseConstraints($data['version']);

			if ($versionRequire->matches($versionProvide)) {
				return $data['metadata'];
			}
		}
	}

	/**
	 * Get class names from given file
	 * http://stackoverflow.com/a/11070559
	 *
	 * @param string $file
	 *
	 * @return array
	 */
	private function getClassesFromFile(string $file) : array
	{
		$classes = [];

		$namespace = 0;
		$tokens = token_get_all(file_get_contents($file));
		$count = count($tokens);
		$dlm = FALSE;

		for ($i = 2; $i < $count; $i++) {
			if ((isset($tokens[$i - 2][1]) && ($tokens[$i - 2][1] === "phpnamespace" || $tokens[$i - 2][1] === "namespace")) ||
				($dlm && $tokens[$i - 1][0] === T_NS_SEPARATOR && $tokens[$i][0] === T_STRING)
			) {
				if (!$dlm) {
					$namespace = 0;
				}

				if (isset($tokens[$i][1])) {
					$namespace = $namespace ? $namespace . "\\" . $tokens[$i][1] : $tokens[$i][1];
					$dlm = TRUE;
				}

			} elseif ($dlm && ($tokens[$i][0] != T_NS_SEPARATOR) && ($tokens[$i][0] != T_STRING)) {
				$dlm = FALSE;
			}

			if (($tokens[$i - 2][0] === T_CLASS || (isset($tokens[$i - 2][1]) && $tokens[$i - 2][1] === "phpclass"))
				&& $tokens[$i - 1][0] === T_WHITESPACE && $tokens[$i][0] === T_STRING
			) {
				$class_name = $tokens[$i][1];


				$classes[] = $namespace . '\\' . $class_name;
			}
		}

		return $classes;
	}
}
