<?php
namespace PHPYAM\core\interfaces;

/**
 * Any class inheriting from class RouterException and implementing this interface will be passed
 * as is to the error controller.
 * In this way, developers can throw their own custom router exceptions and manage them entirely in the error controller.
 * In comparison, the RouterException base class will only pass the RouterException::getMessage()
 * content to the error controller, since the RouterException class was mainly intended for PHPYAM's internal use.
 *
 * @package PHPYAM\core\interfaces
 * @author Thierry BLIND
 * @version 1.0.0
 * @since 01/03/2020
 * @copyright 2020 Thierry BLIND
 */
interface IKeepRouterException
{
}
