<?php
/**
 * ConfigurationScript.php
 *
 * @copyright      More in license.md
 * @license        http://www.ipublikuj.eu
 * @author         Adam Kadlec http://www.ipublikuj.eu
 * @package        iPublikuj:Packages!
 * @subpackage     Scripts
 * @since          2.0.0
 *
 * @date           25.06.16
 */

declare(strict_types = 1);

namespace IPub\Packages\Scripts;

use Nette;
use Nette\Neon;
use Nette\Utils;

use IPub;
use IPub\Packages;
use IPub\Packages\Entities;

/**
 * Nette neon installer
 *
 * @package      iPublikuj:Packages!
 * @subpackage   Scripts
 *
 * @author       Adam Kadlec <adam.kadlec@ipublikuj.eu>
 */
final class ConfigurationScript implements IScript
{
	/**
	 * Define class name
	 */
	const CLASS_NAME = __CLASS__;

	/**
	 * @var string[]
	 */
	private $actions = [];

	/**
	 * @var string
	 */
	private $configDir;

	/**
	 * @var string
	 */
	private $configFile;

	/**
	 * @param string $configDir
	 * @param string $configFile
	 */
	public function __construct(string $configDir, string $configFile)
	{
		$this->configDir = $configDir;
		$this->configFile = $configFile;
	}

	/**
	 * {@inheritdoc}
	 */
	public function install(Entities\IPackage $package)
	{
		// Do nothing here
	}

	/**
	 * {@inheritdoc}
	 */
	public function uninstall(Entities\IPackage $package)
	{
		// Do nothing here
	}

	/**
	 * {@inheritdoc}
	 */
	public function enable(Entities\IPackage $package)
	{
		try {
			$configuration = $package->getConfiguration();

			// Update main config.neon
			if (count($configuration) > 0) {
				$orig = $data = $this->loadConfig();

				$data = array_merge_recursive($data, (array) $configuration);

				$this->saveConfig($data);

				$this->actions[] = function () use ($orig) {
					$this->saveConfig($orig);
				};
			}

		} catch (\Exception $ex) {
			$actions = array_reverse($this->actions);

			try {
				foreach ($actions as $action) {
					$action($this);
				}

			} catch (\Exception $ex) {
				echo $ex->getMessage();
			}

			throw $ex;
		}
	}

	/**
	 * {@inheritdoc}
	 */
	public function disable(Entities\IPackage $package)
	{
		$configuration = $package->getConfiguration();

		// Update main config.neon
		if (count($configuration) > 0) {
			$orig = $data = $this->loadConfig();

			$data = $this->getRecursiveDiff($data, (array) $configuration);

			// Remove extension parameters
			$configuration = $package->getConfiguration();

			if (isset($configuration['extensions'])) {
				foreach ($configuration['extensions'] as $key => $values) {
					if (isset($data[$key])) {
						unset($data[$key]);
					}
				}
			}

			$this->saveConfig($data);

			$this->actions[] = function () use ($orig) {
				$this->saveConfig($orig);
			};
		}
	}

	/**
	 * @param mixed[] $arr1
	 * @param mixed[] $arr2
	 *
	 * @return mixed[]
	 */
	private function getRecursiveDiff(array $arr1, array $arr2) : array
	{
		$isList = Utils\Validators::isList($arr1);
		$arr2IsList = Utils\Validators::isList($arr2);

		foreach ($arr1 as $key => $item) {
			if (!is_array($arr1[$key])) {

				// If key is numeric, remove the same value
				if (is_numeric($key) && ($pos = array_search($arr1[$key], $arr2)) !== FALSE) {
					unset($arr1[$key]);

				} elseif ((!$isList && isset($arr2[$key])) || ($isList && $arr2IsList && array_search($item, $arr2) !== FALSE)) {
					unset($arr1[$key]);
				}

			} elseif (isset($arr2[$key])) {
				$arr1[$key] = $item = $this->getRecursiveDiff($arr1[$key], $arr2[$key]);

				if (is_array($item) && count($item) === 0) {
					unset($arr1[$key]);
				}
			}
		}

		if ($isList) {
			$arr1 = array_merge($arr1);
		}

		return $arr1;
	}

	/**
	 * @return string
	 */
	private function getConfigPath() : string
	{
		return $this->configDir . DIRECTORY_SEPARATOR . $this->configFile;
	}

	/**
	 * @return mixed[]
	 */
	private function loadConfig() : array
	{
		return (array) Neon\Neon::decode(file_get_contents($this->getConfigPath()));
	}

	/**
	 * @param mixed[] $data
	 */
	private function saveConfig(array $data)
	{
		file_put_contents($this->getConfigPath(), Neon\Neon::encode($data, Neon\Neon::BLOCK));
	}
}
