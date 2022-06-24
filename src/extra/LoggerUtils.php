<?php
namespace PHPYAM\extra;

/**
 * Utility classes.
 *
 * @package PHPYAM\extra
 * @author Thierry BLIND
 * @version 1.0.0
 * @since 01/01/2014
 * @copyright 2014-2022 Thierry BLIND
 */

/**
 * Utility class for advanced log4php configuration.
 * Just call {@link \PHPYAM\extra\LoggerUtils::configure($log4phpConfiguration)} and it
 * provides a fully and elegant logging system to catch all PHP errors and exceptions.
 *
 * This class is provided as a minimalist implementation, it is not internally used
 * by PHPAM so it can be replaced by your own implementation if necessary.
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
        set_error_handler('\\PHPYAM\\extra\\LoggerUtils::errorHandler');
        set_exception_handler('\\PHPYAM\\extra\\LoggerUtils::exceptionHandler');
        register_shutdown_function('\\PHPYAM\\extra\\LoggerUtils::shutdownHandler');
    }

    /**
     * Error handler used by {@link \PHPYAM\extra\LoggerUtils::configure($log4phpConfiguration)}.
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
            // PHP >= 8.0.0: the track_errors ini directive has been removed.
            return version_compare(PHP_VERSION, '8.0.0') >= 0 || ! ini_get('track_errors');
        }

        $eol = (PHP_SAPI == 'cli') ? PHP_EOL : '<br />';
        $bo = (PHP_SAPI == 'cli') ? '[' : '<b>';
        $bc = (PHP_SAPI == 'cli') ? ']' : '</b>';
        $errDesc = self::$errLevel[$errno];

        // Display error only if PHP internal error handler is NOT called afterwards.
        // PHP >= 8.0.0: the track_errors ini directive has been removed.
        $displayError = ! ! ini_get('display_errors') && (version_compare(PHP_VERSION, '8.0.0') >= 0 || ! ini_get('track_errors'));

        switch ($errno) {
        case E_ERROR:
        case E_CORE_ERROR:
        case E_COMPILE_ERROR:
        case E_PARSE:
            http_response_code(500);

            \Logger::getLogger(__CLASS__)->fatal("[{$errDesc}] $errstr - $errfile:$errline");
            if ($displayError) {
                echo "{$bo}{$errDesc}{$bc} $errstr - $errfile:$errline$eol";
            }
            exit(1);
            break;

        case E_USER_ERROR:
        case E_RECOVERABLE_ERROR:
            http_response_code(500);

            \Logger::getLogger(__CLASS__)->fatal("[{$errDesc}] $errstr - $errfile:$errline");
            if ($displayError) {
                echo "{$bo}{$errDesc}{$bc} $errstr - $errfile:$errline$eol";
            }
            exit(2);
            break;

        case E_WARNING:
        case E_CORE_WARNING:
        case E_COMPILE_WARNING:
        case E_USER_WARNING:
            \Logger::getLogger(__CLASS__)->warn("[{$errDesc}] $errstr - $errfile:$errline");
            if ($displayError) {
                echo "{$bo}{$errDesc}{$bc} $errstr - $errfile:$errline$eol";
            }
            break;

        case E_NOTICE:
        case E_USER_NOTICE:
            \Logger::getLogger(__CLASS__)->info("[{$errDesc}] $errstr - $errfile:$errline");
            if ($displayError) {
                echo "{$bo}{$errDesc}{$bc} $errstr - $errfile:$errline$eol";
            }
            break;

        case E_STRICT:
        case E_DEPRECATED:
        case E_USER_DEPRECATED:
            \Logger::getLogger(__CLASS__)->debug("[{$errDesc}] $errstr - $errfile:$errline");
            if ($displayError) {
                echo "{$bo}{$errDesc}{$bc} $errstr - $errfile:$errline$eol";
            }
            break;

        default:
            \Logger::getLogger(__CLASS__)->warn("[Unknown error type $errno] $errstr - $errfile:$errline");
            if ($displayError) {
                echo "{$bo}Unknown error type $errno{$bc} $errstr - $errfile:$errline$eol";
            }
            break;
        }

        // Execute PHP internal error handler (ie. return FALSE) only if 'track_errors' is set.
        // PHP >= 8.0.0: the track_errors ini directive has been removed.
        return version_compare(PHP_VERSION, '8.0.0') >= 0 || ! ini_get('track_errors');
    }

    /**
     * Error handler used by {@link \PHPYAM\extra\LoggerUtils::configure($log4phpConfiguration)}.
     *
     * @param \Throwable $t
     *            exception or error object that was thrown
     */
    public final static function exceptionHandler(/*\Throwable*/ $t)
    {
        http_response_code(500);

        \Logger::getLogger(__CLASS__)->fatal($t);

        $eol = (PHP_SAPI == 'cli') ? PHP_EOL : '<br />';
        $bo = (PHP_SAPI == 'cli') ? '[' : '<b>';
        $bc = (PHP_SAPI == 'cli') ? ']' : '</b>';
        $displayError = ! ! ini_get('display_errors');
        if ($displayError) {
            echo "{$bo}Uncaught exception " . $t->getCode() . "{$bc}$eol" . $t->getMessage() . $eol . 'at ' . $t->getFile() . '(' . $t->getLine() . ")$eol" . preg_replace('/\\r?\\n/', $eol, $t->getTraceAsString()) . $eol;
        }
    }

    /**
     * Method executed after script execution finishes or exit() is called ("shutdown function").
     * Intended to handle fatal errors that cannot be "catched" by self::errorHandler(), such as memory allocation errors or timeouts.
     * This is the very last chance to log errors that have occurred.
     * Note that the shutdown functions will not be executed if the process is killed by a SIGTERM or SIGKILL signal.
     */
    public final static function shutdownHandler()
    {
        $error = error_get_last();
        if ($error !== null && $error['type'] === E_ERROR) {
            http_response_code(500);

            $errno = $error['type'];
            $errstr = $error['message'];
            $errfile = $error['file'];
            $errline = $error['line'];

            $eol = (PHP_SAPI == 'cli') ? PHP_EOL : '<br />';
            $bo = (PHP_SAPI == 'cli') ? '[' : '<b>';
            $bc = (PHP_SAPI == 'cli') ? ']' : '</b>';
            $errDesc = self::$errLevel[$errno];

            \Logger::getLogger(__CLASS__)->fatal("[{$errDesc}] $errstr - $errfile:$errline");

            $displayError = ! ! ini_get('display_errors');
            if ($displayError) {
                echo "{$bo}{$errDesc}{$bc} $errstr - $errfile:$errline$eol";
            }
        }
    }
}
