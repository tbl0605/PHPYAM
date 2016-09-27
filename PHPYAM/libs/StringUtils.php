<?php
namespace PHPYAM\libs;

/**
 * Utility classes.
 *
 * @package PHPYAM.libs
 * @author Thierry BLIND
 * @version 1.0.0
 * @since 01/01/2014
 * @copyright 2014-2016 Thierry BLIND
 */

/**
 * Utility class for string manipulations.
 * All methods here can be used as parameter of the function {link PHP_MANUAL#array_walk()}.
 *
 * @author Thierry BLIND
 * @todo create a multi-bytes version of this class
 */
class StringUtils
{

    /**
     * Returns a translated gettext message.
     *
     * @param string $message
     *            message to translate (from the PHPYAM domain)
     * @return string translated message
     */
    public final static function gettext($message)
    {
        return mb_convert_encoding(dgettext('PHPYAM', $message), CLIENT_CHARSET, 'UTF-8');
    }

    /**
     * Escapes a string by converting the characters that have a special meaning in HTML 4.01.
     * Warning: the parameter $value is first "casted" as "string" before being processed.
     * Also be aware that if the input string contains an invalid code unit sequence within
     * the given charset an empty string will be returned.
     *
     * @param string|null $value
     *            value to be protected (passed by reference)
     * @param string $encodingTo
     *            encoding to use.
     * @param mixed|null $key
     *            key (not used)
     * @see array_walk()
     */
    public final static function htmlize(&$value, $key, $encodingTo = 'UTF-8')
    {
        // Note: since PHP 5.5 we have to specify the encoding for htmlspecialchars() and htmlentities(), because:
        // - they now use UTF-8 as the default encoding
        // - according to the official doc:  If the input string contains an invalid code unit sequence within
        // the given charset an empty string will be returned, unless either the ENT_IGNORE or ENT_SUBSTITUTE flags are set.
        // Note 2: ENT_HTML401 is only available since PHP 5.4.0
        // Note 3: ENT_QUOTES will convert both double and single quotes for HTML and Javascript values so they
        // can be injected into HTML tag attributes.
        $value = htmlentities((string) $value, ENT_COMPAT | ENT_QUOTES /*| ENT_HTML401*/, $encodingTo);
    }

    /**
     * Convert a string from charset $charsets['from'] to the charset $charsets['to'].
     * <b>Converts $value only if it's of type string!</b>.
     *
     * @param string $value
     *            value to be protected (passed by reference)
     * @param mixed|null $key
     *            key (not used)
     * @param array $charsets
     *            $charsets['from'] => source encoding.
     *            $charsets['to'] => target encoding.
     * @see array_walk()
     * @see is_string()
     */
    public final static function stringEncode(&$value, $key, $charsets = array('from' => 'ISO-8859-1', 'to' => 'UTF-8'))
    {
        if (is_string($value)) {
            $value = mb_convert_encoding($value, $charsets['to'], $charsets['from']);
        }
    }

    /**
     * Escapes a string by converting the characters that have a special meaning in Javascript.
     * Warning: the parameter $value is first "casted" as "string" before being processed.
     *
     * @param string $value
     *            value to be protected (passed by reference)
     * @param mixed|null $key
     *            key (not used)
     * @param string $decodingFrom
     *            charset encoding of $value
     * @see array_walk()
     */
    public final static function jsonEncode(&$value, $key, $decodingFrom = 'UTF-8')
    {
        $value = (string) $value;
        // jsonEncode() only handles values encoded in UTF-8.
        self::stringEncode($value, $key, array(
            'from' => $decodingFrom,
            'to' => 'UTF-8'
        ));
        $value = jsonEncode($value);
    }
}
?>