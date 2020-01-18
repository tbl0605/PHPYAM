<?php
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
 * - contain "$this" or "self", i.e. no direct reference to methods and properties of the calling controller!
 * - directly access to the context of the web application (variables $_GET, $_POST, $_SESSION, $GLOBALS, etc...),
 *   because the calling controller is responsible for that!
 */
use PHPYAM\core\Core;
?>
<div>
	<ul>
		<?php
if (is_array($_listOfErrors)) {
    foreach ($_listOfErrors as $error) {
        if ($error instanceof Exception) {
            echo '<li><span>' . preg_replace('/\\r\\n?|\\n/', '<br />', Core::html($error)) . '</span></li>';
        } else {
            echo '<li><span>' . Core::html($error) . '</span></li>';
        }
    }
}
?>
	</ul>
</div>
