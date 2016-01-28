<?php
namespace PHPYAM\libs;

/**
 * Utility classes.
 *
 * @package PHPYAM.libs
 * @author Thierry BLIND
 * @version 1.0.0
 * @since 01/01/2014
 * @copyright 2014-2016 Thierry BLIND
 */

/**
 * Utility class for advanced log4php configuration.
 * Just call {@link \PHPYAM\libs\LoggerUtils::configure($log4phpConfiguration)} and it
 * provides a fully and elegant logging system to catch all PHP errors and exceptions.
 *
 * @author Thierry BLIND
 */
class LoggerUtils
{

    private static $errLevel = array(
        E_ERROR => 'E_ERROR',
        E_CORE_ERROR => 'E_CORE_ERROR',
        E_COMPILE_ERROR => 'E_COMPILE_ERROR',
        E_PARSE => 'E_PARSE',
        E_USER_ERROR => 'E_USER_ERROR',
        E_RECOVERABLE_ERROR => 'E_RECOVERABLE_ERROR',
        E_WARNING => 'E_WARNING',
        E_CORE_WARNING => 'E_CORE_WARNING',
        E_COMPILE_WARNING => 'E_COMPILE_WARNING',
        E_USER_WARNING => 'E_USER_WARNING',
        E_NOTICE => 'E_NOTICE',
        E_USER_NOTICE => 'E_USER_NOTICE',
        E_STRICT => 'E_STRICT',
        E_DEPRECATED => 'E_DEPRECATED',
        E_USER_DEPRECATED => 'E_USER_DEPRECATED'
    );

    /**
     * Configures log4php and the PHP error & exception handlers
     * to log all errors and exceptions through log4php.
     *
     * @param string $log4phpConfiguration
     *            the log4php configuration filename
     */
    public final static function configure($log4phpConfiguration)
    {
        \Logger::configure($log4phpConfiguration);
        set_error_handler('\PHPYAM\libs\LoggerUtils::errorHandler');
        set_exception_handler('\PHPYAM\libs\LoggerUtils::exceptionHandler');
        // TODO: add register_shutdown_function() for non-catchable errors?
        // http://phpfunk.com/php/capture-fatal-php-errors-for-logging/
    }

    /**
     * Error handler used by {@link \PHPYAM\libs\LoggerUtils::configure($log4phpConfiguration)}.
     *
     * @param int $errno
     *            contains the level of the error raised
     * @param string $errstr
     *            contains the error message
     * @param string $errfile
     *            contains the filename that the error was raised in
     * @param int $errline
     *            contains the line number the error was raised at
     */
    public final static function errorHandler($errno, $errstr, $errfile, $errline)
    {
        if (! (error_reporting() & $errno)) {
            // This error code is not included in error_reporting.
            // Execute PHP internal error handler (ie. return FALSE) only if 'track_errors' is set.
            return ! ini_get('track_errors');
        }

        $eol = (PHP_SAPI == 'cli') ? PHP_EOL : '<br />';
        $bo = (PHP_SAPI == 'cli') ? '[' : '<b>';
        $bc = (PHP_SAPI == 'cli') ? ']' : '</b>';
        $errDesc = self::$errLevel[$errno];

        // Display error only if PHP internal error handler is NOT called afterwards.
        $displayError = ! ! ini_get('display_errors') && ! ini_get('track_errors');

        switch ($errno) {
        case E_ERROR:
        case E_CORE_ERROR:
        case E_COMPILE_ERROR:
        case E_PARSE:
            if ($displayError) {
                echo "{$bo}{$errDesc}{$bc} $errstr - $errfile:$errline$eol";
            }
            \Logger::getLogger(__CLASS__)->fatal("[{$errDesc}] $errstr - $errfile:$errline");
            exit(1);
            break;

        case E_USER_ERROR:
        case E_RECOVERABLE_ERROR:
            if ($displayError) {
                echo "{$bo}{$errDesc}{$bc} $errstr - $errfile:$errline$eol";
            }
            \Logger::getLogger(__CLASS__)->fatal("[{$errDesc}] $errstr - $errfile:$errline");
            exit(2);
            break;

        case E_WARNING:
        case E_CORE_WARNING:
        case E_COMPILE_WARNING:
        case E_USER_WARNING:
            if ($displayError) {
                echo "{$bo}{$errDesc}{$bc} $errstr - $errfile:$errline$eol";
            }
            \Logger::getLogger(__CLASS__)->warn("[{$errDesc}] $errstr - $errfile:$errline");
            break;

        case E_NOTICE:
        case E_USER_NOTICE:
            if ($displayError) {
                echo "{$bo}{$errDesc}{$bc} $errstr - $errfile:$errline$eol";
            }
            \Logger::getLogger(__CLASS__)->info("[{$errDesc}] $errstr - $errfile:$errline");
            break;

        case E_STRICT:
        case E_DEPRECATED:
        case E_USER_DEPRECATED:
            if ($displayError) {
                echo "{$bo}{$errDesc}{$bc} $errstr - $errfile:$errline$eol";
            }
            \Logger::getLogger(__CLASS__)->debug("[{$errDesc}] $errstr - $errfile:$errline");
            break;

        default:
            if ($displayError) {
                echo "{$bo}Unknown error type $errno{$bc} $errstr - $errfile:$errline$eol";
            }
            \Logger::getLogger(__CLASS__)->warn("[Unknown error type $errno] $errstr - $errfile:$errline");
            break;
        }

        // Execute PHP internal error handler (ie. return FALSE) only if 'track_errors' is set.
        return ! ini_get('track_errors');
    }

    /**
     * Error handler used by {@link \PHPYAM\libs\LoggerUtils::configure($log4phpConfiguration)}.
     *
     * @param Exception $exception
     *            exception object that was thrown
     */
    public final static function exceptionHandler(\Exception $exception)
    {
        $eol = (PHP_SAPI == 'cli') ? PHP_EOL : '<br />';
        $bo = (PHP_SAPI == 'cli') ? '[' : '<b>';
        $bc = (PHP_SAPI == 'cli') ? ']' : '</b>';
        $displayError = ! ! ini_get('display_errors');
        if ($displayError) {
            echo "{$bo}Uncaught exception " . $exception->getCode() . "{$bc}$eol" . $exception->getMessage() . $eol . 'at ' . $exception->getFile() . '(' . $exception->getLine() . ")$eol" . preg_replace('/\\r?\\n/', $eol, $exception->getTraceAsString()) . $eol;
        }
        \Logger::getLogger(__CLASS__)->fatal($exception);
    }
}
