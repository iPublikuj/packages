<?php
/**
 * IPackage.php
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

interface IPackage
{
	/**
	 * @param string $parent
	 */
	function setParent($parent);

	/**
	 * @return string
	 */
	function getParent();

	/**
	 * @param string $name
	 */
	function setName($name);

	/**
	 * @return string
	 */
	function getName();

	/**
	 * @param string $version
	 */
	function setVersion($version);

	/**
	 * @return string
	 */
	function getVersion();

	/**
	 * @param array $types
	 */
	function setTypes(array $types);

	/**
	 * @param string $type
	 */
	function addType($type);

	/**
	 * @return array
	 */
	function getTypes();

	/**
	 * @param string $title
	 */
	function setTitle($title);

	/**
	 * @return string
	 */
	function getTitle();

	/**
	 * @return bool
	 */
	function hasTitle();

	/**
	 * @param string $description
	 */
	function setDescription($description);

	/**
	 * @return string
	 */
	function getDescription();

	/**
	 * @param array|Utils\ArrayHash $keywords
	 *
	 * @throws Exceptions\InvalidArgumentException
	 */
	function setKeywords($keywords);

	/**
	 * @param string $keyword
	 */
	function addKeyword($keyword);

	/**
	 * @return Utils\ArrayHash
	 */
	function getKeywords();

	/**
	 * @param string $homepage
	 */
	function setHomepage($homepage);

	/**
	 * @return string
	 */
	function getHomepage();

	/**
	 * @param array|Utils\ArrayHash $license
	 *
	 * @throws Exceptions\InvalidArgumentException
	 */
	function setLicense($license);

	/**
	 * @return Utils\ArrayHash
	 */
	function getLicense();

	/**
	 * @param array|Utils\ArrayHash $authors
	 *
	 * @throws Exceptions\InvalidArgumentException
	 */
	function setAuthors($authors);

	/**
	 * @return string|FALSE
	 */
	function getAuthor();

	/**
	 * @return Utils\ArrayHash
	 */
	function getAuthors();

	/**
	 * @param array|Utils\ArrayHash $extra
	 *
	 * @throws Exceptions\InvalidArgumentException
	 */
	function setExtra($extra);

	/**
	 * @return Utils\ArrayHash
	 */
	function getExtra();

	/**
	 * @param Utils\DateTime $releaseDate
	 */
	function setReleaseDate(Utils\DateTime $releaseDate);

	/**
	 * @return Utils\DateTime
	 */
	function getReleaseDate();

	/**
	 * Sets source from which this package was installed (source/dist)
	 *
	 * @param string $type
	 */
	function setInstallationSource($type);

	/**
	 * Returns source from which this package was installed (source/dist)
	 *
	 * @return string
	 */
	function getInstallationSource();

	/**
	 * @param string $type
	 */
	function setSourceType($type);

	/**
	 * @return string
	 */
	function getSourceType();

	/**
	 * @param string $url
	 */
	function setSourceUrl($url);

	/**
	 * @return string
	 */
	function getSourceUrl();

	/**
	 * @param string $type
	 */
	function setDistType($type);

	/**
	 * @return string
	 */
	function getDistType();

	/**
	 * @param string $url
	 */
	function setDistUrl($url);

	/**
	 * @return string
	 */
	function getDistUrl();

	/**
	 * @param string $shaSum
	 */
	function setDistSha1Checksum($shaSum);

	/**
	 * @return string
	 */
	function getDistSha1Checksum();

	/**
	 * Set the autoload namespace => directory mapping
	 *
	 * @param array|Utils\ArrayHash $autoload
	 *
	 * @throws Exceptions\InvalidArgumentException
	 */
	function setAutoload($autoload);

	/**
	 * Returns the autoload namespace => directory mapping
	 *
	 * @return Utils\ArrayHash
	 */
	function getAutoload();

	/**
	 * Set the resources scheme => path(s)
	 *
	 * @param array|Utils\ArrayHash $resources
	 *
	 * @throws Exceptions\InvalidArgumentException
	 */
	function setResources($resources);

	/**
	 * Returns the resources scheme => path(s)
	 *
	 * @return Utils\ArrayHash
	 */
	function getResources();

	/**
	 * @return string
	 */
	function getUniqueName();

	/**
	 * @param IPackage $package
	 * @param string $operator
	 *
	 * @return bool
	 */
	function compare(IPackage $package, $operator = '==');

	/**
	 * @return string
	 */
	function __toString();
}
