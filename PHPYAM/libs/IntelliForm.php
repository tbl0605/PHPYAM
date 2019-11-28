<?php
namespace PHPYAM\libs;

/**
 * Prevent warnings of resubmitting posted forms, save form data, seed forms with a random token for recognition and to reduce cross site scripting.
 *
 * @author Anthony Gallon
 * @author Fabian Schmengler <fschmengler@sgh-it.eu>
 * @author Thierry Blind
 * @see http://www.phpclasses.org/discuss/package/3851/thread/1/
 * @package PHPYAM\libs
 */
class IntelliForm
{

    /**
     * Key used to store or retrieve \PHPYAM\libs\IntelliForm datas.
     *
     * @static string $ANTZ_KEY
     */
    const ANTZ_KEY = 'antz_seed';

    /**
     * How long are forms kept for (in seconds).
     *
     * @static int $expireTime
     */
    private static $expireTime = 300;

    /**
     * Run counter in one page load.
     *
     * @static int $hasRun
     */
    private static $hasRun = 0;

    /**
     * Getter.
     *
     * @return int
     */
    public static function getExpireTime()
    {
        return self::$expireTime;
    }

    /**
     * Setter.
     *
     * @param int $expireTime
     */
    public static function setExpireTime($expireTime)
    {
        self::$expireTime = $expireTime;
    }

    /**
     * Save form contents for later restoration.
     *
     * @param string $key
     * @param int $expire
     *            (seconds)
     */
    public static function save($key, $expire = null)
    {
        $expire = $expire === null ? self::$expireTime + time() : $expire + time();
        if (! isset($_SESSION['antz_intelliForm']) || ! is_array($_SESSION['antz_intelliForm'])) {
            $_SESSION['antz_intelliForm'] = array();
        }
        $_SESSION['antz_intelliForm'][$key] = $_POST;
        $_SESSION['antz_intelliForm'][$key]['antz_intelliFormExpires'] = $expire;
    }

    /**
     * Restore form contents from a previous save.
     *
     * @param string $key
     * @param bool $eraseOnFailure
     *            if true, clear $_POST when no previous save is available
     */
    public static function restore($key, $clearOnFailure = false)
    {
        if (isset($_SESSION['antz_intelliForm'][$key])) {
            $_POST = $_SESSION['antz_intelliForm'][$key];
        } elseif ($clearOnFailure) {
            $_POST = array();
        }
    }

    /**
     * Clear a saved form.
     *
     * @param string $key
     */
    public static function clear($key)
    {
        if (isset($_SESSION['antz_intelliForm'][$key]))
            unset($_SESSION['antz_intelliForm'][$key]);
    }

    /**
     * Clear all expired saves.
     */
    public static function purge()
    {
        if (isset($_SESSION['antz_intelliForm']) && is_array($_SESSION['antz_intelliForm'])) {
            foreach ($_SESSION['antz_intelliForm'] as $key => $post) {
                if ($post['antz_intelliFormExpires'] <= time()) {
                    unset($_SESSION['antz_intelliForm'][$key]);
                }
            }
        }

        // Clear form seeds (max 15 forms per page).
        while (isset($_SESSION[self::ANTZ_KEY]) && count($_SESSION[self::ANTZ_KEY]) > 15) {
            unset($_SESSION[self::ANTZ_KEY][0]);
            $_SESSION[self::ANTZ_KEY] = array_values($_SESSION[self::ANTZ_KEY]);
        }
    }

    /**
     * Call this before doing anything else, to bypass the pesty confirm prompt
     * that appears when resubmitting post content.
     */
    public static function antiRepost($url)
    {
        // Just in case the function gets called twice in one page load, we would get a bad loop happening!
        if (self::$hasRun > 0) {
            return;
        }
        self::$hasRun ++;

        if (isset($_POST[self::ANTZ_KEY])) {
            // Form has been submitted.
            $_SESSION['antz_post'] = $_POST;

            // Move the files to a new temp location.
            foreach ($_FILES as $k => $file) {
                $suffix = rand(0, 999);
                if (trim($file['tmp_name']) === '') {
                    continue;
                }
                // TODO: specify a temp dir other than sys_get_temp_dir().
                rename($file['tmp_name'], sys_get_temp_dir() . '/' . $suffix . $file['name']);
                //echo $file['tmp_name'].'<br />';
                $_FILES[$k]['tmp_name'] = sys_get_temp_dir() . '/' . $suffix . $file['name'];
                chmod(sys_get_temp_dir() . '/' . $suffix . $file['name'], 0777);
            }

            $_SESSION['antz_files'] = $_FILES;

            // Work out the requested page and redirect to it.
            header('location:' . $url);

            // http://stackoverflow.com/questions/8665985/php-utilizing-exit-or-die-after-headerlocation
            exit()/*('<script>window.location="' . $url . '"</script>
            <a href="' . $url . '">Continue >></a>')*/;
        } elseif (isset($_SESSION['antz_post'])) {
            $_POST = $_SESSION['antz_post'];
            $_FILES = $_SESSION['antz_files'];
            $_REQUEST = array_replace($_REQUEST, $_POST);
            unset($_SESSION['antz_post']);

            if (defined('ANTZ_DEBUG') && ANTZ_DEBUG) {
                if (! isset($_SESSION['antz_debug'])) {
                    $_SESSION['antz_debug'] = array(
                        'post_count' => 0
                    );
                }
                $_SESSION['antz_debug']['post_count'] ++;
            }
        }
    }

    /**
     * Checks to see if the form has been submitted with a valid seed.
     *
     * @param bool $del
     *            delete the seed
     * @param string $id
     *            namespace
     * @return bool $isSubmitted
     * @throws \Exception on usage mistake, but only when
     *         constant ANTZ_DEBUG is defined and set to true
     */
    public static function submitted($del = true, $id = 'default')
    {
        if (! isset($_POST[self::ANTZ_KEY])) {
            return false;
        }

        $seed = $_POST[self::ANTZ_KEY];

        if (defined('ANTZ_DEBUG') && ANTZ_DEBUG) {
            $msg = null;
            if (! isset($_SESSION['antz_debug']['post_count'])) {
                $msg = '\\IntelliForm::antiRepost() must be called before calling the \\IntelliForm::submitted() method.';
            } elseif ($_SESSION['antz_debug']['post_count'] === 1 && ! isset($_SESSION[self::ANTZ_KEY])) {
                $msg = "IntelliForm: the seed '{$seed}' was found but the user session related to this seed has been destroyed meanwhile. You must create a new seed before calling the \\IntelliForm::submitted() method.";
            }
            if ($msg !== null) {
                if (defined('USE_LOG4PHP') && USE_LOG4PHP) {
                    \Logger::getLogger(__CLASS__)->error($msg);
                }
                $exceptionCode = defined('ANTZ_DEBUG_EXCEPTION_CODE') ? (int) ANTZ_DEBUG_EXCEPTION_CODE : 0;
                throw new \Exception($msg, $exceptionCode);
            }
        }

        if (! isset($_SESSION[self::ANTZ_KEY]) || ! is_array($_SESSION[self::ANTZ_KEY])) {
            $_SESSION[self::ANTZ_KEY] = array();
        }

        if (! isset($_SESSION[self::ANTZ_KEY][$id]) || ! is_array($_SESSION[self::ANTZ_KEY][$id])) {
            $_SESSION[self::ANTZ_KEY][$id] = array();
        }

        if (in_array($seed, $_SESSION[self::ANTZ_KEY][$id])) {
            $tmp = array_flip($_SESSION[self::ANTZ_KEY][$id]);
            if ($del) {
                $_SESSION[self::ANTZ_KEY][$id][$tmp[$seed]] = mt_rand(0, 99999999);
            }
            unset($tmp, $seed);
            return true;
        }

        return false;
    }

    /**
     * Plant a seed to ensure forms are accepted by a verified session.
     * Check with {@link \PHPYAM\libs\IntelliForm::submitted()}.
     *
     * @param string $id
     * @return string $htmlHiddenInputAsText
     */
    public static function seed($id = 'default')
    {
        $seed = mt_rand(0, 99999999);
        if (! isset($_SESSION[self::ANTZ_KEY]) || ! is_array($_SESSION[self::ANTZ_KEY])) {
            $_SESSION[self::ANTZ_KEY] = array();
        }
        if (! isset($_SESSION[self::ANTZ_KEY][$id]) || ! is_array($_SESSION[self::ANTZ_KEY][$id])) {
            $_SESSION[self::ANTZ_KEY][$id] = array();
        }
        $_SESSION[self::ANTZ_KEY][$id][] = $seed;
        return '<div style="display: none"><input type="hidden" name="' . self::ANTZ_KEY . '" value="' . $seed . '"></div>';
    }
}
