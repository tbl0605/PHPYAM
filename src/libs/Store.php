<?php
namespace PHPYAM\libs;

use PHPYAM\core\Core;
use PHPYAM\core\interfaces\IConfiguration;

/**
 * Utility classes.
 *
 * @package PHPYAM\libs
 * @author Thierry BLIND
 * @since 01/04/2022
 * @copyright 2014-2022 Thierry BLIND
 */

/**
 * Static class holding the configuration of any running PHPYAM router.
 * Note that the PHPYAM router is intended to be instanciated only once per application,
 * that's why the router's configuration can be stored and accessed as a singleton here.
 *
 * When no configuration object is set using the router's constructor,
 * PHPYAM will mimic the behavior of PHPYAM v1.1 (to be fully backward compatible)
 * by retrieving the router's configuration from global constants.
 *
 * @author Thierry BLIND
 */
class Store
{

    /**
     * Singleton.
     * Used to store the running PHPYAM router configuration.
     * Can be null, the router will then try to retrieve the values
     * from global constants (like PHPYAM v1.1) before using PHPYAM's internal default values.
     *
     * @var \PHPYAM\core\interfaces\IConfiguration|null
     */
    private static $configuration = null;

    /**
     * Retrieve the router configuration, null otherwise.
     *
     * @return \PHPYAM\core\interfaces\IConfiguration|null
     */
    public final static function getConfiguration()
    {
        return self::$configuration;
    }

    /**
     * Set the router configuration.
     *
     * @param \PHPYAM\core\interfaces\IConfiguration $configuration
     * @return \PHPYAM\core\interfaces\IConfiguration the newly stored configuration, for fluent interface
     */
    public final static function setConfiguration(IConfiguration $configuration)
    {
        self::$configuration = $configuration;
        return $configuration;
    }

    /**
     * Remove the stored router configuration.
     *
     * @return \PHPYAM\core\interfaces\IConfiguration|null the previously stored configuration (or null), for fluent interface
     */
    public final static function removeConfiguration()
    {
        $previousConfiguration = self::$configuration;
        self::$configuration = null;
        return $previousConfiguration;
    }

    /**
     * Retrieve the value associated to a configuration key by using the PHPYAM router configuration.
     * When no configuration objet is set, PHPYAM will try to look for a global constant named $key.
     * When no configuration key (or global constant) was found, $defaultValue will be returned.
     * Note that any returned value is valid and allowed (even null).
     *
     * @param string $key
     *            configuration key
     * @param mixed $defaultValue
     *            default value to return when the key was not found
     * @return mixed configuration value
     */
    public final static function get($key, $defaultValue)
    {
        if (self::$configuration === null) {
            // When no configuration object exists, PHYAM first
            // looks after global constants (for backward compatibility with PHPYAM 1.1).
            if (defined($key)) {
                return constant($key);
            }
            return $defaultValue;
        }

        return self::$configuration->get($key, $defaultValue);
    }

    /**
     * Retrieve the value associated to a configuration key by using the PHPYAM router configuration.
     * When no configuration objet is set, PHPYAM will try to look for a global constant named $key.
     * An AssertException will be thrown when no configuration key (or global constant) is found
     * or if <b>the associated value is null</b>.
     *
     * @param string $key
     *            configuration key
     * @return mixed configuration value
     * @throws \PHPYAM\libs\AssertException when the required configuration property is missing or the associated value is null
     */
    public final static function getRequired($key)
    {
        $value = null;
        if (self::$configuration === null) {
            // When no configuration object exists, PHYAM first
            // looks after global constants (for backward compatibility with PHPYAM 1.1)
            // before throwing an exception.
            if (defined($key)) {
                $value = constant($key);
            }
        } else {
            $value = self::$configuration->get($key, null);
        }

        Assert::isTrue($value !== null, Core::gettext("The required configuration property '%s' is missing or the associated value is null."), $key);

        return $value;
    }

    /**
     * Check if PHPYAM has some logging mechanism available to log internal error messages.
     * The user can always choose to ignore and not log these messages.
     * For internal use only.
     *
     * @return boolean
     */
    public final static function hasLoggingFunctionality()
    {
        return self::$configuration !== null || defined('USE_LOG4PHP');
    }

    /**
     * Log an internal error message.
     * When no configuration objet is set, PHPYAM will try to use the log4php library when
     * the constant USE_LOG4PHP is defined and set to true (like PHPYAM v1.1).
     *
     * @param mixed $data
     *            data to log
     */
    public final static function logError($data)
    {
        if (self::$configuration !== null) {
            self::$configuration->logError($data);
        } elseif (defined('USE_LOG4PHP') && constant('USE_LOG4PHP')) {
            // Backward compatibility with PHPYAM 1.1
            \Logger::getLogger(__CLASS__)->error($data);
        }
    }
}
?>