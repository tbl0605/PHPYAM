<?php
namespace PHPYAM\extra;

use PHPYAM\core\interfaces\IConfiguration;

/**
 * Basic implementation for storing and retrieving the PHPYAM router configuration.
 * Any class extending this class will be able to store the configuration keys <b>internally</b> as class constants,
 * static properties or object properties (or ultimately as <b>external</b> global constants).
 *
 * When this class is used as is, the configuration keys can only be stored and retrieved <b>externally</b> as global contants,
 * as for PHPYAM v1.1
 *
 * @package PHPYAM\extra
 * @author Thierry BLIND
 * @since 01/04/2022
 * @copyright 2014-2022 Thierry BLIND
 */
class Configuration implements IConfiguration
{

    /**
     * Return the value associated with a configuration key.
     *
     * For example, this method will try to resolve the configuration key "CONF_KEY"
     * in following order before defaulting to $defaultValue:<br>
     * <ul>
     * <li>look for a class constant in current object: <code>const CONF_KEY=...;</code></li>
     * <li>look for an object property in current object: <code>$CONF_KEY=...;</code></li>
     * <li>look for a static class property in current object: <code>static $CONF_KEY=...;</code></li>
     * <li>look for a global constant: <code>define("CONF_KEY", ...);</code></li>
     * </ul>
     *
     * {@inheritdoc}
     * @see \PHPYAM\core\interfaces\IConfiguration::get()
     */
    public function get($key, $defaultValue)
    {
        $classReflexion = new \ReflectionClass($this);
        $classConstants = $classReflexion->getConstants();
        if (array_key_exists($key, $classConstants)) {
            return $classConstants[$key];
        }
        $objectProperties = get_object_vars($this);
        if (array_key_exists($key, $objectProperties)) {
            return $objectProperties[$key];
        }
        $classStaticProperties = $classReflexion->getStaticProperties();
        if (array_key_exists($key, $classStaticProperties)) {
            return $classStaticProperties[$key];
        }
        if (defined($key)) {
            return constant($key);
        }
        return $defaultValue;
    }

    /**
     * NB: can be overridden if the log4php library should not be used.
     *
     * {@inheritdoc}
     * @see \PHPYAM\core\interfaces\IConfiguration::logError()
     */
    public function logError($data)
    {
        \Logger::getLogger(__CLASS__)->error($data);
    }
}
