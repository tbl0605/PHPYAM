<?php
namespace PHPYAM\libs;

/**
 * Utility classes.
 *
 * @package PHPYAM\libs
 * @author Thierry BLIND
 * @version 1.0.0
 * @since 01/01/2014
 * @copyright 2014-2020 Thierry BLIND
 */

/**
 * Class to perform data validity tests.
 * If a test fails, an exception will be generated.
 *
 * @author Thierry BLIND
 */
class Assert
{

    /**
     * Method that accepts a variable number of arguments.
     * The first argument is the condition to be tested.
     * If the condition is TRUE, an exception will be thrown.
     * The arguments $arg1,... will be passed to the function {@link PHP_MANUAL#sprintf()}
     * and the result will be used as message related to the thrown exception.
     *
     * @param string $expression
     *            expression to evaluate
     * @param mixed $arg1,...
     *            arguments used to generate the exception's message when $expression is TRUE
     * @throws \PHPYAM\libs\AssertException
     */
    public final static function isFalse($expression)
    {
        $args = func_get_args();
        $count = count($args);
        if (! $count) {
            return;
        }
        $expr = $args[0];
        array_shift($args);
        $count --;
        if ($expr) {
            switch ($count) {
            case 0:
                throw new AssertException();
                break;
            case 1:
                throw new AssertException($args[0]);
                break;
            default:
                throw new AssertException(call_user_func_array('sprintf', $args));
                break;
            }
        }
    }

    /**
     * Method that accepts a variable number of arguments.
     * The first argument is the condition to be tested.
     * If the condition is FALSE, an exception will be thrown.
     * The arguments $arg1,... will be passed to the function {@link PHP_MANUAL#sprintf()}
     * and the result will be used as message related to the thrown exception.
     *
     * @param string $expression
     *            expression to evaluate
     * @param mixed $arg1,...
     *            arguments used to generate the exception's message when $expression is FALSE
     * @throws \PHPYAM\libs\AssertException
     */
    public final static function isTrue($expression)
    {
        $args = func_get_args();
        $count = count($args);
        if (! $count) {
            return;
        }
        $expr = $args[0];
        array_shift($args);
        $count --;
        if (! $expr) {
            switch ($count) {
            case 0:
                throw new AssertException();
                break;
            case 1:
                throw new AssertException($args[0]);
                break;
            default:
                throw new AssertException(call_user_func_array('sprintf', $args));
                break;
            }
        }
    }
}
?>