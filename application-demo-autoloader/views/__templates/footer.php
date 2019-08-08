<?php
namespace PHPYAM\demo\application\views\__templates;

use PHPYAM\demo\confs\AppConfig;

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
 * because the calling controller is responsible for that!
 */
class Footer
{

    public static function render(array $props)
    {
        ?>

<script type="text/javascript" charset="UTF-8"
	src="<?=AppConfig::URL_PUB?>js/jquery-loading/1.2.0/jquery.loading<?=YAM_MIN?>.js"></script>
<script type="text/javascript" charset="UTF-8"
	src="<?=AppConfig::URL_PUB?>js/jquery-validation/1.15.1/jquery.validate<?=YAM_MIN?>.js"></script>
<script type="text/javascript" charset="UTF-8"
	src="<?=AppConfig::URL_PUB?>js/jquery-validation/1.15.1/additional-methods<?=YAM_MIN?>.js"></script>
<!-- <script type="text/javascript" charset="UTF-8"
	src="<?=AppConfig::URL_PUB?>js/jquery-validation/1.15.1/localization/messages_fr.js"></script> -->
</body>
</html><?php
    }
}
?>