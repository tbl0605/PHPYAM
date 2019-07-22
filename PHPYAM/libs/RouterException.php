<?php
namespace PHPYAM\libs;

/**
 * Exception that will be thrown by PHPYAM when the processing of a web request fails.
 * Developers can also throw this \PHPYAM\libs\RouterException to properly stop any treatment
 * and to output an error message to the client.
 * The message displayed is the one returned by {@link Exception::getMessage()}.
 *
 * @package PHPYAM\libs
 * @author Thierry BLIND
 * @version 1.0.0
 * @since 01/01/2014
 * @copyright 2014-2019 Thierry BLIND
 */
class RouterException extends \Exception
{

    /*
     * (non-PHPdoc) @see \Exception::__construct()
     */
    public function __construct($message = null, $code = null)
    {
        parent::__construct(trim($message) === '' ? StringUtils::gettext('Treatment interrupted. Please restart the application.') : $message, $code);
    }
}
?>