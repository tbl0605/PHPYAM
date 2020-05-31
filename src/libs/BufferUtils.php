<?php
namespace PHPYAM\libs;

/**
 * Utility classes.
 *
 * @package PHPYAM\libs
 * @author Thierry BLIND
 * @author Fabien Potencier <fabien@symfony.com>
 * @version 1.0.0
 * @since 27/04/2020
 * @copyright 2020 Thierry BLIND
 */

/**
 * Utility class for output buffers handling.
 *
 * @see https://github.com/symfony/symfony/blob/master/src/Symfony/Component/HttpFoundation/Response.php
 */
class BufferUtils
{

    /**
     * Cleans or flushes output buffers up to target level.
     *
     * Resulting level can be greater than target level if a non-removable buffer has been encountered.
     *
     * @final
     */
    public static function closeOutputBuffers(int $targetLevel, bool $flush): void
    {
        $status = ob_get_status(true);
        $level = \count($status);
        $flags = PHP_OUTPUT_HANDLER_REMOVABLE | ($flush ? PHP_OUTPUT_HANDLER_FLUSHABLE : PHP_OUTPUT_HANDLER_CLEANABLE);

        while ($level -- > $targetLevel && ($s = $status[$level]) && (! isset($s['del']) ? ! isset($s['flags']) || ($s['flags'] & $flags) === $flags : $s['del'])) {
            if ($flush) {
                ob_end_flush();
            } else {
                ob_end_clean();
            }
        }
    }
}
?>