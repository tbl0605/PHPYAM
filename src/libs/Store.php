<?php
namespace PHPYAM\libs;

use PHPYAM\core\Core;
use PHPYAM\core\interfaces\IConfiguration;

/**
 * Utility classes.
 *
 * @package PHPYAM\libs
 * @author Thierry BLIND
 * @version 1.0.0
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
     */
    public final static function getRequired($key)
    {
        $value = null;
        if (self::$configuration === null) {
            // When no configuration object exists, PHYAM first
            // loofs after global constants (for backward compatibility)
            // before throwing an exception.
            if (defined($key)) {
                $value = constant($key);
            }
        } else {
            $value = self::$configuration->get($key, null);
        }
        if ($value === null) {
            throw new \RuntimeException(Core::gettext('Internal error. Please restart the application.'));
        }
        return $value;
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
        }
    }
}
?>