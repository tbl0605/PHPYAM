<?php
namespace PHPYAM\demo\application\views\__templates;

use PHPYAM\core\Core;

/**
 * GOOD PRACTICE FOR VIEWS:
 *
 * A view should only display data and should not access the methods of the calling controller.
 * A view can:
 * - use the content of PHP variables defined by the calling controller + action
 * - define PHP variables locally (be careful to not crush the PHP variables defined by the calling controller)
 * - use utility classes to do data formatting
 * - integrate other views
 * - contain client code for web browsers (html, css, javascript, etc...)
 * - be tested to display data without using any controller
 * A view MUST NOT:
 * - directly access to the context of the web application (variables $_GET, $_POST, $_SESSION, $GLOBALS, etc...),
 *   because the calling controller is responsible for that!
 */
class Error
{

    public static function render(array $props)
    {
        ?>
<div>
	<ul>
		<?php
        if (isset($props['listOfErrors']) && is_array($props['listOfErrors'])) {
            foreach ($props['listOfErrors'] as $error) {
                if ($error instanceof \Exception) {
                    echo '<li><span>' . preg_replace('/\\r\\n?|\\n/', '<br />', Core::html($error)) . '</span></li>';
                } else {
                    echo '<li><span>' . Core::html($error) . '</span></li>';
                }
            }
        }
        ?>
	</ul>
</div>
<?php
    }
}
?>