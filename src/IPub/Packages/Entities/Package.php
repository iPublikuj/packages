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

namespace IPub\Packages\Entities;

use Nette;
use Nette\Utils;

use IPub;
use IPub\Packages\Exceptions;
use IPub\Packages\Repository;

class Package extends Nette\Object implements IPackage
{
	/**
	 * @var string
	 */
	private $parent;

	/**
	 * @var string
	 */
	private $name;

	/**
	 * @var string
	 */
	private $version;

	/**
	 * @var Utils\ArrayHash
	 */
	private $types;

	/**
	 * @var string
	 */
	private $title;

	/**
	 * @var string
	 */
	private $description;

	/**
	 * @var Utils\ArrayHash
	 */
	private $keywords = [];

	/**
	 * @var string
	 */
	private $homepage;

	/**
	 * @var Utils\ArrayHash
	 */
	private $license = [];

	/**
	 * @var Utils\ArrayHash
	 */
	private $authors = [];

	/**
	 * @var Utils\ArrayHash
	 */
	private $extra = [];

	/**
	 * @var Utils\DateTime
	 */
	private $releaseDate;

	/**
	 * @var string
	 */
	private $installationSource;

	/**
	 * @var array
	 */
	private $source = [
		'type' => NULL,
		'url'  => NULL
	];

	/**
	 * @var array
	 */
	private $dist = [
		'type'   => NULL,
		'url'    => NULL,
		'shaSum' => NULL
	];

	/**
	 * @var Utils\ArrayHash
	 */
	private $autoload = [];

	/**
	 * @var Utils\ArrayHash
	 */
	private $resources = [];

	/**
	 * @param string $name
	 * @param string $version
	 */
	public function __construct($name, $version)
	{
		$this->setName($name);
		$this->setVersion($version);

		$this->types = new Utils\ArrayHash;
		$this->keywords = new Utils\ArrayHash;
		$this->license = new Utils\ArrayHash;
		$this->authors = new Utils\ArrayHash;
		$this->extra = new Utils\ArrayHash;
		$this->autoload = new Utils\ArrayHash;
		$this->resources = new Utils\ArrayHash;
	}

	/**
	 * {@inheritdoc}
	 */
	public function setParent($parent)
	{
		$this->parent = (string) $parent;
	}

	/**
	 * {@inheritdoc}
	 */
	public function getParent()
	{
		return $this->parent;
	}

	/**
	 * {@inheritdoc}
	 */
	public function setName($name)
	{
		$name = (string) $name;

		// Package name
		$this->name = strtolower($name);

		if (!$this->hasTitle()) {
			// Package default title
			$this->setTitle($name);
		}
	}

	/**
	 * {@inheritdoc}
	 */
	public function getName()
	{
		return $this->name;
	}

	/**
	 * {@inheritdoc}
	 */
	public function setVersion($version)
	{
		// Package version
		$this->version = (string) $version;
	}

	/**
	 * {@inheritdoc}
	 */
	public function getVersion()
	{
		return $this->version;
	}

	/**
	 * {@inheritdoc}
	 */
	public function setTypes(array $types)
	{
		$this->types = Utils\ArrayHash::from($types);
	}

	/**
	 * {@inheritdoc}
	 */
	public function addType($type)
	{
		$types = (array) $this->types;
		$types[] = $type;
		$types = array_unique($types);

		$this->types = Utils\ArrayHash::from($types);
	}

	/**
	 * {@inheritdoc}
	 */
	public function getTypes()
	{
		return (array) $this->types;
	}

	/**
	 * {@inheritdoc}
	 */
	public function setTitle($title)
	{
		$this->title = (string) $title;
	}

	/**
	 * {@inheritdoc}
	 */
	public function getTitle()
	{
		return $this->title;
	}

	/**
	 * {@inheritdoc}
	 */
	public function hasTitle()
	{
		return $this->title !== '' && $this->title !== NULL;
	}

	/**
	 * {@inheritdoc}
	 */
	public function setDescription($description)
	{
		$this->description = (string) $description;
	}

	/**
	 * {@inheritdoc}
	 */
	public function getDescription()
	{
		return $this->description;
	}

	/**
	 * {@inheritdoc}
	 */
	public function setKeywords($keywords)
	{
		$this->keywords = $this->checkForArray($keywords);
	}

	/**
	 * {@inheritdoc}
	 */
	public function addKeyword($keyword)
	{
		$this->keywords[] = (string) $keyword;
	}

	/**
	 * {@inheritdoc}
	 */
	public function getKeywords()
	{
		return $this->keywords;
	}

	/**
	 * {@inheritdoc}
	 */
	public function setHomepage($homepage)
	{
		$this->homepage = (string) $homepage;
	}

	/**
	 * {@inheritdoc}
	 */
	public function getHomepage()
	{
		return $this->homepage;
	}

	/**
	 * {@inheritdoc}
	 */
	public function setLicense($license)
	{
		$this->license = $this->checkForArray($license);
	}

	/**
	 * {@inheritdoc}
	 */
	public function getLicense()
	{
		return $this->license;
	}

	/**
	 * {@inheritdoc}
	 */
	public function setAuthors($authors)
	{
		$this->authors = $this->checkForArray($authors);
	}

	/**
	 * {@inheritdoc}
	 */
	public function getAuthor()
	{
		if ($this->authors->count()) {
			// Convert to classic array
			$authors = (array) $this->authors;

			return current($authors);

		} else {
			return FALSE;
		}
	}

	/**
	 * {@inheritdoc}
	 */
	public function getAuthors()
	{
		return $this->authors;
	}

	/**
	 * {@inheritdoc}
	 */
	public function setExtra($extra)
	{
		$this->extra = $this->checkForArray($extra);
	}

	/**
	 * {@inheritdoc}
	 */
	public function getExtra()
	{
		return $this->extra;
	}

	/**
	 * {@inheritdoc}
	 */
	public function setReleaseDate(Utils\DateTime $releaseDate)
	{
		$this->releaseDate = $releaseDate;
	}

	/**
	 * {@inheritdoc}
	 */
	public function getReleaseDate()
	{
		return $this->releaseDate;
	}

	/**
	 * {@inheritdoc}
	 */
	public function setInstallationSource($type)
	{
		$this->installationSource = $type;
	}

	/**
	 * {@inheritdoc}
	 */
	public function getInstallationSource()
	{
		return $this->installationSource;
	}

	/**
	 * {@inheritdoc}
	 */
	public function setSourceType($type)
	{
		$this->source['type'] = (string) $type;
	}

	/**
	 * {@inheritdoc}
	 */
	public function getSourceType()
	{
		return $this->source['type'];
	}

	/**
	 * {@inheritdoc}
	 */
	public function setSourceUrl($url)
	{
		$this->source['url'] = (string) $url;
	}

	/**
	 * {@inheritdoc}
	 */
	public function getSourceUrl()
	{
		return $this->source['url'];
	}

	/**
	 * {@inheritdoc}
	 */
	public function setDistType($type)
	{
		$this->dist['type'] = (string) $type;
	}

	/**
	 * {@inheritdoc}
	 */
	public function getDistType()
	{
		return $this->dist['type'];
	}

	/**
	 * {@inheritdoc}
	 */
	public function setDistUrl($url)
	{
		$this->dist['url'] = (string) $url;
	}

	/**
	 * {@inheritdoc}
	 */
	public function getDistUrl()
	{
		return $this->dist['url'];
	}

	/**
	 * {@inheritdoc}
	 */
	public function setDistSha1Checksum($shaSum)
	{
		$this->dist['shaSum'] = (string) $shaSum;
	}

	/**
	 * {@inheritdoc}
	 */
	public function getDistSha1Checksum()
	{
		return $this->dist['shaSum'];
	}

	/**
	 * {@inheritdoc}
	 */
	public function setAutoload($autoload)
	{
		$this->autoload = $this->checkForArray($autoload);
	}

	/**
	 * {@inheritdoc}
	 */
	public function getAutoload()
	{
		return $this->autoload;
	}

	/**
	 * {@inheritdoc}
	 */
	public function setResources($resources)
	{
		$this->resources = $this->checkForArray($resources);
	}

	/**
	 * {@inheritdoc}
	 */
	public function getResources()
	{
		return $this->resources;
	}

	/**
	 * {@inheritdoc}
	 */
	public function getUniqueName()
	{
		return sprintf('%s-%s', $this->getName(), $this->getVersion());
	}

	/**
	 * {@inheritdoc}
	 */
	public function compare(IPackage $package, $operator = '==')
	{
		return strtolower($this->getName()) === strtolower($package->getName()) &&
		version_compare(strtolower($this->getVersion()), strtolower($package->getVersion()), $operator);
	}

	/**
	 * {@inheritdoc}
	 */
	public function __toString()
	{
		return $this->getUniqueName();
	}

	/**
	 * @param array|Utils\ArrayHash $value
	 *
	 * @return Utils\ArrayHash
	 *
	 * @throws Exceptions\InvalidArgumentException
	 */
	protected function checkForArray($value)
	{
		if (is_array($value)) {
			return Utils\ArrayHash::from($value);

		} elseif ($value instanceof Utils\ArrayHash) {
			return $value;

		} else {
			throw new Exceptions\InvalidArgumentException('Invalid value given. Only array or instance of \Nette\Utils\ArrayHash are allowed.');
		}
	}
}
