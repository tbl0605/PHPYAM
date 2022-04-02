<?php
namespace PHPYAM\libs;

/**
 * Utility classes.
 *
 * @package PHPYAM\libs
 * @author Thierry BLIND
 * @since 01/01/2014
 * @copyright 2014-2020 Thierry BLIND
 */

/**
 * Exception class used specifically by the \PHPYAM\libs\Assert class.
 *
 * @author Thierry BLIND
 */
class AssertException extends \Exception
{

    /*
     * (non-PHPdoc) @see \Exception::__construct()
     */
    public function __construct($message = null, $code = 0)
    {
        parent::__construct($message, $code);
    }
}
?>