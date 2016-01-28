<?php
namespace PHPYAM\core;

/**
 * Utility class containing methods that can be invoked in any context (model, view or controller).
 * Some methods depend on the current project configuration (e.g. certain methods use mb_internal_encoding(),
 * or the constants CLIENT_CHARSET and URL_ASSOCIATIVE_PARAMS).
 *
 * @package PHPYAM.core
 * @author Thierry BLIND
 * @version 1.0.0
 * @since 01/01/2014
 * @copyright 2014 Thierry BLIND
 */
final class Core
{

    /**
     * Prevent the instantiation of this class.
     */
    private function __construct()
    {
    }

    /**
     * Encodes a parameter from an URL used to do MVC routing.
     * This URL is in the form "alias/controller/action/parameter1/parameter2/.../parameterN".
     * It is recommended to encode URL parts "parameterN" but also the URL parts "controller" and "action"
     * using this method, because the router re-decode ALL URL parts later through the method
     * {@link \PHPYAM\core\Core::decodeUrlParameter($param)}.
     * This method uses the standard {@link PHP_MANUAL#urlencode} encoding function,
     * However it's not enough because (depending on the configuration) Apache and its URL-rewriting "clean-up"
     * URLs for security reasons (everything before the GET part of the URL, i.e. before the symbol '?').
     * Among others, the space characters are removed, and several successive '/' are replaced with a single '/'.
     * To protect against these adverse effects, the following actions are performed on each parameter:
     * <ul>
     * <li>before encoding a parameter, trailing blanks are automatically deleted
     * <li>before encoding, an empty parameter is replaced by a (single) space character
     * <li>after encoding a parameter, the character strings "_", "+" and "%" are respectively transformed into "_5F", "_20" and "_"
     * </ul>
     *
     * @param string|null $param
     *            value to encode
     * @return string encoded value
     * @see http://httpd.apache.org/docs/current/mod/core.html#allowencodedslashes
     * @see http://stackoverflow.com/questions/4069002/http-400-if-2f-part-of-get-url-in-jboss
     * @see http://forums.devshed.com/php-development-5/link-url-encoded-hash-object-found-error-browser-797165.html
     * @see http://php.net/manual/fr/function.urlencode.php
     */
    public final static function encodeUrlParameter($param)
    {
        $str = rtrim((string) $param);
        if ($str === '') {
            $str = ' ';
        }
        // Because str_replace() replaces left to right, it might replace a previously
        // inserted value when doing multiple replacements.
        return str_replace(array(
            '_',
            '+',
            '%'
        ), array(
            '%5F',
            '%20',
            '_'
        ), urlencode($str));
    }

    /**
     * Decodes a parameter from an URL used to do MVC routing, previously encoded by {@link \PHPYAM\core\Core::encodeUrlParameter($param)}.
     * This method uses the PHP function {@link PHP_MANUAL#urldecode}, but also performs the following actions to work around
     * the limitations imposed by Apache and URL-rewriting:
     * <ul>
     * <li>before decoding the parameter, strings "_5F" and "_" are respectively converted into "%5F" and "%"
     * <li>after decoding the parameter, trailing blanks are removed
     * </ul>
     *
     * @param string|null $param
     *            value to be decoded
     * @return string decoded value
     * @see http://httpd.apache.org/docs/current/mod/core.html#allowencodedslashes
     * @see http://stackoverflow.com/questions/4069002/http-400-if-2f-part-of-get-url-in-jboss
     * @see http://forums.devshed.com/php-development-5/link-url-encoded-hash-object-found-error-browser-797165.html
     * @see http://php.net/manual/fr/function.urldecode.php
     */
    public final static function decodeUrlParameter($param)
    {
        // Because str_replace() replaces left to right, it might replace a previously
        // inserted value when doing multiple replacements.
        return rtrim(urldecode(str_replace(array(
            '_5F',
            '_'
        ), array(
            '%5F',
            '%'
        ), (string) $param)));
    }

    /**
     * Creates an URL from given controller + action + parameters.
     *
     * @param string $urlController
     *            controller (case-insensitive)
     * @param string $urlAction
     *            action (case-insensitive)
     * @param array $urlParameters
     *            parameters
     * @return string URL
     */
    public final static function url($urlController, $urlAction, array $urlParameters = array())
    {
        $params = '';
        foreach ($urlParameters as $key => $value) {
            if (URL_ASSOCIATIVE_PARAMS) {
                $params .= '/' . self::encodeUrlParameter($key) . '/' . self::encodeUrlParameter($value);
            } else {
                $params .= '/' . self::encodeUrlParameter($value);
            }
        }
        // Note: the controller name and action are also encoded.
        return URL . self::encodeUrlParameter($urlController) . '/' . self::encodeUrlParameter($urlAction) . $params;
    }

    /**
     * Adds a random value (known as "anti-cache") to prevent URL caching in the web browser.
     * Example :<br />
     * <code>
     * echo \PHPYAM\core\Core::antiCache('my/url#my_anchor') . '\n';
     * echo \PHPYAM\core\Core::antiCache('my/url#my_anchor') . '\n';
     *
     * my/url?antiCache=569377881#my_anchor
     * my/url?antiCache=1604303817#my_anchor
     * </code>
     *
     * @param string $url
     *            URL to protect. If empty, $_SERVER['REQUEST_URI'] will be used as URL.
     * @return string|boolean URL with its "anti-cache" value
     */
    public final static function antiCache($url)
    {
        return \PHPYAM\libs\UrlUtils::addQueryArg('antiCache', mt_rand(), $url);
    }

    /**
     * Protects a value (or a list of values), that should be integrated into an HTML page,
     * with {@link \PHPYAM\libs\StringUtils::htmlize()}.
     * Uses the charset <code>CLIENT_CHARSET</code> to try to encode $value.
     * Note: if $value is a list of values, the keys stay unchanged (and therefore unprotected).
     * IMPORTANT: the value (or the list of values) $value will be converted as string(s).
     *
     * @param mixed|array|null $value
     *            value passed by reference (or list of values passed by reference), protected and then converted into string(s)
     */
    public final static function htmlize(&$value)
    {
        if (is_array($value)) {
            array_walk_recursive($value, '\PHPYAM\libs\StringUtils::htmlize', CLIENT_CHARSET);
        } else {
            \PHPYAM\libs\StringUtils::htmlize($value, null, CLIENT_CHARSET);
        }
    }

    /**
     * Escapes a string by converting the characters that have a special meaning in HTML 4.01.
     * Uses the charset <code>CLIENT_CHARSET</code> to try to encode $value.
     * IMPORTANT: The value will be converted into a string.
     *
     * @param string|null $value
     *            value to be protected
     * @return string protected value or empty string if the encoding failed
     */
    public final static function html($value)
    {
        \PHPYAM\libs\StringUtils::htmlize($value, null, CLIENT_CHARSET);
        return $value;
    }

    /**
     * Protects a value (or a list of values), that should be integrated into an HTML page,
     * with {@link \PHPYAM\libs\StringUtils::jsonEncode()}.
     * Uses the charset {@link PHP_MANUAL#mb_internal_encoding()} to try to encode $value.
     * Note: if $value is a list of values, the keys stay unchanged (and therefore unprotected).
     * IMPORTANT: the value (or the list of values) $value will be converted as string(s).
     *
     * @param mixed|array|null $value
     *            value passed by reference (or list of values passed by reference), protected and then converted into string(s)
     */
    public final static function jsonEncodify(&$value)
    {
        if (is_array($value)) {
            array_walk_recursive($value, '\PHPYAM\libs\StringUtils::jsonEncode', mb_internal_encoding());
        } else {
            \PHPYAM\libs\StringUtils::jsonEncode($value, null, mb_internal_encoding());
        }
    }

    /**
     * Escapes a string by converting the characters that have a special meaning in Javascript.
     * Uses the charset {@link PHP_MANUAL#mb_internal_encoding()} to try to encode $value.
     * IMPORTANT: The value will be converted into a string.
     *
     * @param string|null $value
     *            value to be protected
     * @return string protected value or empty string if the encoding failed
     */
    public final static function jsonEncode($value)
    {
        \PHPYAM\libs\StringUtils::jsonEncode($value, null, mb_internal_encoding());
        return $value;
    }
}
