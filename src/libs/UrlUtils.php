<?php
namespace PHPYAM\libs;

/**
 * Utility classes.
 *
 * @package PHPYAM\libs
 * @author Thierry BLIND
 * @since 01/01/2014
 * @copyright 2014-2020 Thierry BLIND
 */

/**
 * Utility class for URL handling.
 *
 * @author Thierry BLIND
 */
class UrlUtils
{

    /**
     * Returns an array containing all the elements of arr1 after applying
     * the callback function to each one.
     *
     * @param string $callback
     *            Callback function to run for each
     *            element in each array
     * @param array $array
     *            An array to run through the callback
     *            function
     * @param boolean $on_nonscalar
     *            Whether or not to call the callback
     *            function on nonscalar values
     *            (Objects, resources, etc)
     * @return array
     */
    public static function array_map_deep(array $array, $callback, $on_nonscalar = false)
    {
        foreach ($array as $key => $value) {
            if (is_array($value)) {
                $args = array(
                    $value,
                    $callback,
                    $on_nonscalar
                );
                $array[$key] = call_user_func_array(array(
                    __CLASS__,
                    __FUNCTION__
                ), $args);
            } elseif (is_scalar($value) || $on_nonscalar) {
                $array[$key] = call_user_func($callback, $value);
            }
        }

        return $array;
    }

    /**
     * Retrieve a modified URL query string.
     *
     * You can rebuild the URL and append a new query variable to the URL
     * query by using this function. You can also retrieve the full URL
     * with query data.
     *
     * Adding a single key & value or an associative array. Setting a key
     * value to an empty string removes the key. Omitting oldquery_or_uri
     * uses the $_SERVER value. Additional values provided are expected
     * to be encoded appropriately with urlencode() or rawurlencode().
     *
     * @link http://github.com/brandonwamboldt/utilphp/ Official Documentation
     *
     * @param mixed $newkey
     *            Either newkey or an associative
     *            array
     * @param mixed $newvalue
     *            Either newvalue or oldquery or uri
     * @param mixed $oldquery_or_uri
     *            Optionally the old query or uri
     * @return string
     */
    public static function addQueryArg()
    {
        $ret = '';

        // Was an associative array of key => value pairs passed?
        if (is_array(func_get_arg(0))) {

            // Was the URL passed as an argument?
            if (func_num_args() == 2 && func_get_arg(1)) {
                $uri = (string) func_get_arg(1);
            } elseif (func_num_args() == 3 && func_get_arg(2)) {
                $uri = (string) func_get_arg(2);
            } else {
                $uri = $_SERVER['REQUEST_URI'];
            }
        } else {

            // Was the URL passed as an argument?
            if (func_num_args() == 3 && func_get_arg(2)) {
                $uri = (string) func_get_arg(2);
            } else {
                $uri = $_SERVER['REQUEST_URI'];
            }
        }

        // Does the URI contain a fragment section (The part after the #).
        if ($frag = strstr($uri, '#')) {
            $uri = substr($uri, 0, - strlen($frag));
        } else {
            $frag = '';
        }

        // Get the URI protocol if possible.
        if (preg_match('|^https?://|i', $uri, $matches)) {
            $protocol = $matches[0];
            $uri = substr($uri, strlen($protocol));
        } else {
            $protocol = '';
        }

        // Does the URI contain a query string?
        if (strpos($uri, '?') !== FALSE) {
            $parts = explode('?', $uri, 2);
            $base = $parts[0] . '?';
            $query = $parts[1];
        } elseif (! empty($protocol) || strpos($uri, '=') === FALSE) {
            $base = $uri . '?';
            $query = '';
        } else {
            $base = '';
            $query = $uri;
        }

        // Parse the query string into an array.
        parse_str($query, $qs);

        // This re-URL-encodes things that were already in the query string.
        $qs = self::array_map_deep($qs, 'urlencode');

        if (is_array(func_get_arg(0))) {
            $kayvees = func_get_arg(0);
            $qs = array_merge($qs, $kayvees);
        } else {
            $qs[func_get_arg(0)] = func_get_arg(1);
        }

        foreach ((array) $qs as $k => $v) {
            if ($v === false)
                unset($qs[$k]);
        }

        $ret = http_build_query($qs);
        $ret = trim($ret, '?');
        $ret = preg_replace('#=(&|$)#', '$1', $ret);
        $ret = $protocol . $base . $ret . $frag;
        $ret = rtrim($ret, '?');
        return $ret;
    }
}
?>