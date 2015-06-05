<?php
/**
 * Package.php
 *
 * @copyright	More in license.md
 * @license		http://www.ipublikuj.eu
 * @author		Adam Kadlec http://www.ipublikuj.eu
 * @package		iPublikuj:Packages!
 * @subpackage	Entities
 * @since		5.0
 *
 * @date		27.09.14
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
	protected $name;

	/**
	 * @var string
	 */
	protected $version;

	/**
	 * @var string
	 */
	protected $type;

	/**
	 * @var string
	 */
	protected $title;

	/**
	 * @var string
	 */
	protected $description;

	/**
	 * @var array
	 */
	protected $keywords = [];

	/**
	 * @var string
	 */
	protected $homepage;

	/**
	 * @var array
	 */
	protected $license = [];

	/**
	 * @var array
	 */
	protected $authors = [];

	/**
	 * @var array
	 */
	protected $extra = [];

	/**
	 * @var Utils\DateTime
	 */
	protected $releaseDate;

	/**
	 * @var array
	 */
	protected $source = [
		'type'	=> NULL,
		'url'	=> NULL
	];

	/**
	 * @var array
	 */
	protected $dist = [
		'type'		=> NULL,
		'url'		=> NULL,
		'shasum'	=> NULL
	];

	/**
	 * @var array
	 */
	protected $autoload = [];

	/**
	 * @var array
	 */
	protected $resources = [];

	/**
	 * @param string $name
	 * @param string $version
	 */
	public function __construct($name, $version)
	{
		// Package name
		$this->name		= strtolower($name);
		// Package default title
		$this->title	= $this->name;
		// Package version
		$this->version	= $version;
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
	public function getVersion()
	{
		return $this->version;
	}

	/**
	 * {@inheritdoc}
	 */
	public function setType($type)
	{
		$this->type = (string) $type;

		return $this;
	}

	/**
	 * {@inheritdoc}
	 */
	public function getType()
	{
		return $this->type;
	}

	/**
	 * {@inheritdoc}
	 */
	public function setTitle($title)
	{
		$this->title = (string) $title;

		return $this;
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
	public function setDescription($description)
	{
		$this->description = (string) $description;

		return $this;
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
	public function setKeywords(array $keywords)
	{
		$this->keywords = (array) $keywords;

		return $this;
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

		return $this;
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
	public function setLicense(array $license)
	{
		$this->license = (array) $license;

		return $this;
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
	public function setAuthors(array $authors)
	{
		$this->authors = (array) $authors;

		return $this;
	}

	/**
	 * {@inheritdoc}
	 */
	public function getAuthor()
	{
		if ($this->authors) {
			return current($this->authors);
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
	public function setExtra(array $extra)
	{
		$this->extra = $extra;

		return $this;
	}

	/**
	 * {@inheritdoc}
	 */
	public function getExtra()
	{
		return Utils\ArrayHash::from($this->extra);
	}

	/**
	 * {@inheritdoc}
	 */
	public function setReleaseDate(Utils\DateTime $releaseDate)
	{
		$this->releaseDate = $releaseDate;

		return $this;
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

		return $this;
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

		return $this;
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

		return $this;
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

		return $this;
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
	public function setDistSha1Checksum($shasum)
	{
		$this->dist['shasum'] = (string) $shasum;

		return $this;
	}

	/**
	 * {@inheritdoc}
	 */
	public function getDistSha1Checksum()
	{
		return $this->dist['shasum'];
	}

	/**
	 * {@inheritdoc}
	 */
	public function setAutoload(array $autoload)
	{
		$this->autoload = $autoload;
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
	public function setResources(array $resources = [])
	{
		$this->resources = $resources;
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
}