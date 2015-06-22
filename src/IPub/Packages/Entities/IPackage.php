<?php
/**
 * IPackage.php
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

interface IPackage
{
	/**
	 * @return string
	 */
	public function getName();

	/**
	 * @return string
	 */
	public function getVersion();

	/**
	 * @param string $type
	 *
	 * @return $this
	 */
	public function setType($type);

	/**
	 * @return string
	 */
	public function getType();

	/**
	 * @param string $title
	 *
	 * @return $this
	 */
	public function setTitle($title);

	/**
	 * @return string
	 */
	public function getTitle();

	/**
	 * @param string $description
	 *
	 * @return $this
	 */
	public function setDescription($description);

	/**
	 * @return string
	 */
	public function getDescription();

	/**
	 * @param array|Utils\ArrayHash $keywords
	 *
	 * @return $this
	 *
	 * @throws Exceptions\InvalidArgumentException
	 */
	public function setKeywords($keywords);

	/**
	 * @return Utils\ArrayHash
	 */
	public function getKeywords();

	/**
	 * @param string $homepage
	 *
	 * @return $this
	 */
	public function setHomepage($homepage);

	/**
	 * @return string
	 */
	public function getHomepage();

	/**
	 * @param array|Utils\ArrayHash $license
	 *
	 * @return $this
	 *
	 * @throws Exceptions\InvalidArgumentException
	 */
	public function setLicense($license);

	/**
	 * @return Utils\ArrayHash
	 */
	public function getLicense();

	/**
	 * @param array|Utils\ArrayHash $authors
	 *
	 * @return $this
	 *
	 * @throws Exceptions\InvalidArgumentException
	 */
	public function setAuthors($authors);

	/**
	 * @return string|FALSE
	 */
	public function getAuthor();

	/**
	 * @return Utils\ArrayHash
	 */
	public function getAuthors();

	/**
	 * @param array|Utils\ArrayHash $extra
	 *
	 * @return $this
	 *
	 * @throws Exceptions\InvalidArgumentException
	 */
	public function setExtra($extra);

	/**
	 * @return Utils\ArrayHash
	 */
	public function getExtra();

	/**
	 * @param Utils\DateTime $releaseDate
	 *
	 * @return $this
	 */
	public function setReleaseDate(Utils\DateTime $releaseDate);

	/**
	 * @return Utils\DateTime
	 */
	public function getReleaseDate();

	/**
	 * Sets source from which this package was installed (source/dist).
	 *
	 * @param string $type
	 *
	 * @return $this
	 */
	public function setInstallationSource($type);

	/**
	 * Returns source from which this package was installed (source/dist).
	 *
	 * @return string
	 */
	public function getInstallationSource();

	/**
	 * @param string $type
	 *
	 * @return $this
	 */
	public function setSourceType($type);

	/**
	 * @return string
	 */
	public function getSourceType();

	/**
	 * @param string $url
	 *
	 * @return $this
	 */
	public function setSourceUrl($url);

	/**
	 * @return string
	 */
	public function getSourceUrl();

	/**
	 * @param string $type
	 *
	 * @return $this
	 */
	public function setDistType($type);

	/**
	 * @return string
	 */
	public function getDistType();

	/**
	 * @param string $url
	 *
	 * @return $this
	 */
	public function setDistUrl($url);

	/**
	 * @return string
	 */
	public function getDistUrl();

	/**
	 * @param string $shasum
	 *
	 * @return $this
	 */
	public function setDistSha1Checksum($shasum);

	/**
	 * @return string
	 */
	public function getDistSha1Checksum();

	/**
	 * Set the autoload namespace => directory mapping
	 *
	 * @param array|Utils\ArrayHash $autoload
	 *
	 * @return $this
	 *
	 * @throws Exceptions\InvalidArgumentException
	 */
	public function setAutoload($autoload);

	/**
	 * Returns the autoload namespace => directory mapping
	 *
	 * @return Utils\ArrayHash
	 */
	public function getAutoload();

	/**
	 * Set the resources scheme => path(s)
	 *
	 * @param array|Utils\ArrayHash $resources
	 *
	 * @return $this
	 *
	 * @throws Exceptions\InvalidArgumentException
	 */
	public function setResources($resources);

	/**
	 * Returns the resources scheme => path(s)
	 *
	 * @return Utils\ArrayHash
	 */
	public function getResources();

	/**
	 * @return string
	 */
	public function getUniqueName();

	/**
	 * @param IPackage $package
	 * @param string $operator
	 *
	 * @return bool
	 */
	public function compare(IPackage $package, $operator = '==');
}