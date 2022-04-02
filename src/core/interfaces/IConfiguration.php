<?php
namespace PHPYAM\core\interfaces;

/**
 * TODO comment.
 *
 * @package PHPYAM\core\interfaces
 * @author Thierry BLIND
 * @since 01/04/2022
 * @copyright 2014-2022 Thierry BLIND
 */
interface IConfiguration
{

    /**
     * TODO comment.
     *
     * @param string $key
     * @param mixed $defaultValue
     * @return mixed
     */
    public function get($key, $defaultValue);

    /**
     * TODO comment.
     *
     * @param mixed $data
     */
    public function logError($data);
}
