<?php
/**
 * UnexpectedValueException.php
 *
 * @copyright      More in license.md
 * @license        https://www.ipublikuj.eu
 * @author         Adam Kadlec <adam.kadlec@ipublikuj.eu>
 * @package        iPublikuj:Packages!
 * @subpackage     Exceptions
 * @since          1.0.0
 *
 * @date           27.03.15
 */

declare(strict_types = 1);

namespace IPub\Packages\Exceptions;

class UnexpectedValueException extends \UnexpectedValueException implements IException
{
}
