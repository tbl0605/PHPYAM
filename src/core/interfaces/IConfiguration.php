<?php
namespace PHPYAM\core\interfaces;

/**
 * Any class implementing this interface can be used to store the router's configuration (and by extension, any additional application configuration)
 * and define the router's internal error logger.
 * The router's configuration is a set of key/value pairs which defines the behavior of any router instance.
 * Note that the PHPYAM router is intended to be instanciated only once per application, that's why the router's configuration
 * will be stored as a singleton for the duration of the running web application
 * and will be accessible through the static class PHPYAM\libs\Store.<br><br>
 *
 * Following required keys must be stored in the configuration class instantiation (their values must not be null):
 * <ul>
 * <li>URL</li>
 * <li>CLIENT_CHARSET</li>
 * <li>CLIENT_LANGUAGE</li>
 * <li>SECURITY_POLICY</li>
 * <li>ERROR_CONTROLLER</li>
 * <li>ERROR_ACTION</li>
 * <li>ERROR_AJAX_ACTION</li>
 * <li>DEFAULT_CONTROLLER</li>
 * <li>DEFAULT_ACTION</li>
 * </ul>
 *
 * Following optional keys can be stored in the configuration class instantiation (the associated value can be null,
 * but the router's default value will be used when the key is not found):
 * <ul>
 * <li>URL_ASSOCIATIVE_PARAMS default router value: true</li>
 * <li>CREATE_SESSION default router value: true</li>
 * <li>PHPYAM_CATCH_INTERNAL_PHP_ERRORS default router value: false</li>
 * <li>DROP_ALL_ODBC_CONNECTIONS_ON_FATAL_ERROR default router value: false</li>
 * <li>DROP_SESSION_ON_FATAL_ERROR default router value: false</li>
 * </ul>
 *
 * @package PHPYAM\core\interfaces
 * @author Thierry BLIND
 * @since 01/04/2022
 * @copyright 2014-2022 Thierry BLIND
 */
interface IConfiguration
{

    /**
     * Return the value associated with a configuration key.
     * The parameter $defaultValue should be returned when the key was not found.
     *
     * @param string $key
     *            configuration key
     * @param mixed $defaultValue
     *            default value when the configuration key was not found
     * @return mixed
     */
    public function get($key, $defaultValue);

    /**
     * Log any internal router error.
     *
     * @param mixed $data
     *            data to log
     */
    public function logError($data);
}
