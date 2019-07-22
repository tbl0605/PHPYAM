<?php
namespace PHPYAM\libs;

/**
 * Utility classes.
 *
 * @package PHPYAM\libs
 * @author Thierry BLIND
 * @version 1.0.0
 * @since 01/01/2014
 * @copyright 2014-2019 Thierry BLIND
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
    public function __construct($message = null, $code = null)
    {
        parent::__construct($message, $code);
    }
}
?>