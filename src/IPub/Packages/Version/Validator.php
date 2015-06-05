<?php
/**
 * Validator.php
 *
 * @copyright	More in license.md
 * @license		http://www.ipublikuj.eu
 * @author		Adam Kadlec http://www.ipublikuj.eu
 * @package		iPublikuj:Packages!
 * @subpackage	Version
 * @since		5.0
 *
 * @date		30.09.14
 */

namespace IPub\Packages\Version;

class Validator
{
	/**
	 * Validates a version
	 *
	 * @param  string $version
	 *
	 * @return bool
	 */
	public static function validate($version)
	{
		return preg_match('/^\d{1,3}\.\d{1,3}\.\d{1,3}(-(pre|beta|b|RC|alpha|a|pl|p)([\.]?(\d{1,3}))?)?$/', $version);
	}
}
