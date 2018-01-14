<?php
/**
 * Package.php
 *
 * @copyright      More in license.md
 * @license        http://www.ipublikuj.eu
 * @author         Adam Kadlec http://www.ipublikuj.eu
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

use IPub;
use IPub\Packages\Exceptions;

/**
 * Package abstract entity
 *
 * @package        iPublikuj:Packages!
 * @subpackage     Entities
 *
 * @author         Adam Kadlec <adam.kadlec@ipublikuj.eu>
 */
abstract class Package extends Nette\Object implements IPackage
{
	/**
	 * @var mixed
	 */
	protected $composerData;

	/**
	 * @param mixed[] $composerData
	 */
	public function __construct(array $composerData)
	{
		$this->composerData = $composerData;

		if (!isset($composerData['name'])) {
			throw new Exceptions\UnexpectedValueException('Unknown package has no name defined (' . json_encode($composerData) . ').');
		}
	}

	/**
	 * {@inheritdoc}
	 */
	public function getName() : string
	{
		return $this->composerData['name'];
	}

	/**
	 * {@inheritdoc}
	 */
	public function getTitle()
	{
		$extra = $this->getExtra();

		return $extra->offsetExists('title') ? $extra->offsetGet('title') : $this->getName();
	}

	/**
	 * {@inheritdoc}
	 */
	public function hasTitle() : bool
	{
		return $this->getTitle() ? TRUE : FALSE;
	}

	/**
	 * {@inheritdoc}
	 */
	public function getDescription()
	{
		return $this->composerData['description'];
	}

	/**
	 * {@inheritdoc}
	 */
	public function getVersion() : string
	{
		return isset($this->composerData['version']) ? $this->composerData['version'] : '0.0.0';
	}

	/**
	 * {@inheritdoc}
	 */
	public function getType() : string
	{
		return isset($this->composerData['type']) ? $this->composerData['type'] : 'undefined';
	}

	/**
	 * {@inheritdoc}
	 */
	public function getKeywords() : Utils\ArrayHash
	{
		$keywords = [];

		if (isset($this->composerData['keywords'])) {
			$keywords = $this->composerData['keywords'];

			$keywords = is_array($keywords) ? $keywords : array_map('trim', explode(',', $keywords));
		}

		return Utils\ArrayHash::from($keywords);
	}

	/**
	 * {@inheritdoc}
	 */
	public function getHomepage()
	{
		return isset($this->composerData['homepage']) ? $this->composerData['homepage'] : NULL;
	}

	/**
	 * {@inheritdoc}
	 */
	public function getReleaseDate()
	{
		if (isset($this->composerData['time'])) {
			try {
				return new Utils\DateTime($this->composerData['time'], new \DateTimeZone('UTC'));

			} catch (\Exception $ex) {
				// If date could not be converted to object, than is in wrong format and is not added to package
			}
		}

		return NULL;
	}

	/**
	 * {@inheritdoc}
	 */
	public function getLicense() : Utils\ArrayHash
	{
		$licenses = [];

		if (isset($this->composerData['license'])) {
			$licenses = $this->composerData['license'];

			$licenses = is_array($licenses) ? $licenses : array_map('trim', explode(',', $licenses));
		}

		return Utils\ArrayHash::from($licenses);
	}

	/**
	 * {@inheritdoc}
	 */
	public function getAuthors() : Utils\ArrayHash
	{
		$authors = [];

		if (isset($this->composerData['authors'])) {
			$authors = $this->composerData['authors'];
		}

		return Utils\ArrayHash::from($authors);
	}


	/**
	 * {@inheritdoc}
	 */
	public function getSupport()
	{
		return isset($this->composerData['support']) ? $this->composerData['support'] : NULL;
	}

	/**
	 * {@inheritdoc}
	 */
	public function getRequire() : array
	{
		$ret = [];

		if (isset($this->composerData['require'])) {
			foreach ($this->composerData['require'] as $name => $require) {
				if (strpos($name, '/') !== FALSE) {
					$ret[] = $name;
				}
			}
		}

		return $ret;
	}

	/**
	 * {@inheritdoc}
	 */
	public function getRequireDev() : array
	{
		$ret = [];

		if (isset($this->composerData['require-dev'])) {
			foreach ($this->composerData['require-dev'] as $name => $require) {
				if (strpos($name, '/') !== FALSE) {
					$ret[] = $name;
				}
			}
		}

		return $ret;
	}

	/**
	 * {@inheritdoc}
	 */
	public function getAutoload() : Utils\ArrayHash
	{
		$autoload = [];

		if (isset($this->composerData['autoload'])) {
			$autoload = $this->composerData['autoload'];
		}

		return Utils\ArrayHash::from($autoload);
	}

	/**
	 * {@inheritdoc}
	 */
	public function getExtra() : Utils\ArrayHash
	{
		$data = [];

		if (isset($this->composerData['extra'])) {
			$data = $this->composerData['extra'];
		}

		return Utils\ArrayHash::from($data);
	}

	/**
	 * {@inheritdoc}
	 */
	public function getConfiguration() : Utils\ArrayHash
	{
		$data = [];

		if (isset($this->composerData['extra']['ipub']['configuration'])) {
			$data = $this->composerData['extra']['ipub']['configuration'];
		}

		return Utils\ArrayHash::from($data);
	}

	/**
	 * {@inheritdoc}
	 */
	public function getScripts() : Utils\ArrayHash
	{
		$scripts = ['IPub\Packages\Scripts\ConfigurationScript'];

		if (isset($this->composerData['extra']['ipub']['scripts'])) {
			$scripts = array_merge($scripts, $this->composerData['extra']['ipub']['scripts']);
		}

		return Utils\ArrayHash::from($scripts);
	}

	/**
	 * {@inheritdoc}
	 */
	public function getUniqueName() : string
	{
		return sprintf('%s-%s', $this->getName(), $this->composerData['version']);
	}

	/**
	 * {@inheritdoc}
	 */
	public function getPath() : string
	{
		$reflectionClass = new \ReflectionClass($this);

		return dirname($reflectionClass->getFileName());
	}

	/**
	 * {@inheritdoc}
	 */
	public function compare(IPackage $package, string $operator = '==') : bool
	{
		return strtolower($this->getName()) === strtolower($package->getName()) &&
		version_compare(strtolower($this->getVersion()), strtolower($package->getVersion()), $operator);
	}

	/**
	 * {@inheritdoc}
	 */
	public function __toString() : string
	{
		return $this->getUniqueName();
	}
}
