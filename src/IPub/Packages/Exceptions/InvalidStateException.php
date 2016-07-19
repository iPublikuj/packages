<?php
/**
 * InvalidStateException.php
 *
 * @copyright      More in license.md
 * @license        http://www.ipublikuj.eu
 * @author         Adam Kadlec http://www.ipublikuj.eu
 * @package        iPublikuj:Packages!
 * @subpackage     Exceptions
 * @since          2.0.0
 *
 * @date           21.06.16
 */

namespace IPub\Packages\Exceptions;

use Nette;

class InvalidStateException extends Nette\InvalidStateException implements IException
{
}