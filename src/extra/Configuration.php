<?php
namespace PHPYAM\extra;

use PHPYAM\core\interfaces\IConfiguration;

/**
 * TODO comment.
 *
 * @package PHPYAM\extra
 * @author Thierry BLIND
 * @version 1.0.0
 * @since 01/04/2022
 * @copyright 2014-2022 Thierry BLIND
 */
class Configuration implements IConfiguration
{

    /**
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
        if (property_exists($this, $key)) {
            return $this->${$key};
        }
        if (defined($key)) {
            return constant($key);
        }
        return $defaultValue;
    }

    /**
     *
     * {@inheritdoc}
     * @see \PHPYAM\core\interfaces\IConfiguration::logError()
     */
    public function logError($data)
    {
        \Logger::getLogger(__CLASS__)->error($data);
    }
}
