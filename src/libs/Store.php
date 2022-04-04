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
 * TODO comment.
 *
 * @author Thierry BLIND
 */
class Store
{

    /**
     * TODO comment.
     * Singleton.
     *
     * @var \PHPYAM\core\interfaces\IConfiguration|null
     */
    private static $configuration = null;

    /**
     * TODO comment.
     *
     * @return \PHPYAM\core\interfaces\IConfiguration|null
     */
    public final static function getConfiguration()
    {
        return self::$configuration;
    }

    /**
     * TODO comment.
     *
     * @param \PHPYAM\core\interfaces\IConfiguration $configuration
     * @return \PHPYAM\core\interfaces\IConfiguration
     */
    public final static function setConfiguration(IConfiguration $configuration)
    {
        self::$configuration = $configuration;
        return $configuration;
    }

    /**
     * TODO comment.
     */
    public final static function removeConfiguration()
    {
        self::$configuration = null;
    }

    /**
     * TODO comment.
     *
     * @param string $key
     * @param mixed $defaultValue
     * @return mixed
     */
    public final static function get($key, $defaultValue)
    {
        return self::$configuration !== null ? self::$configuration->get($key, $defaultValue) : $defaultValue;
    }

    /**
     * TODO comment.
     *
     * @param string $key
     * @return mixed
     * @throws \PHPYAM\libs\AssertException
     */
    public final static function getRequired($key)
    {
        $value = null;
        if (self::$configuration === null) {
            // When no configuration object exists, PHYAM first
            // loofs after global constants (for backward compatibility with PHPYAM 1.1)
            // before throwing an exception.
            if (defined($key)) {
                $value = constant($key);
            }
        } else {
            $value = self::$configuration->get($key, null);
        }

        Assert::isTrue($value !== null, Core::gettext("The required configuration property '%s' is missing."), $key);

        return $value;
    }

    /**
     * TODO comment.
     * For internal use.
     *
     * @return boolean
     */
    public final static function hasLoggingFunctionality()
    {
        return self::$configuration !== null || defined('USE_LOG4PHP');
    }

    /**
     * TODO comment.
     *
     * @param mixed $data
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