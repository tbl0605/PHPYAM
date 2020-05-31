<?php
namespace PHPYAM\demo\application\views\__templates;

use PHPYAM\core\Core;
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
 *   because the calling controller is responsible for that!
 */
class Header
{

    public static function render(array $props)
    {
        ?>
<!DOCTYPE html>
<html lang="<?=CLIENT_LANGUAGE?>">
<head>
	<meta charset="<?=CLIENT_CHARSET?>">
	<meta http-equiv="Content-Type" content="text/html; charset=<?=CLIENT_CHARSET?>">
	<meta http-equiv="X-UA-Compatible" content="IE=edge">
	<meta http-equiv="Cache-Control" content="no-cache, no-store, must-revalidate" />
	<meta http-equiv="Pragma" content="no-cache" />
	<meta http-equiv="Expires" content="-1" />
	<title><?=Core::html(AppConfig::TITLE)?></title>
	<meta name="description" content="<?=Core::html(AppConfig::TITLE)?>">
	<meta name="viewport" content="width=device-width, initial-scale=1.0, shrink-to-fit=no">

	<!-- css -->
	<link rel="stylesheet" type="text/css" href="<?=AppConfig::URL_PUB?>css/jquery-loading/1.2.0/jquery.loading<?=YAM_MIN?>.css" />

	<!-- local -->
	<link rel="stylesheet" type="text/css" href="<?=AppConfig::URL_PUB?>css/common.css" />

	<!-- javaScript -->
	<!-- jQuery -->
	<!--[if lt IE 9]>
	<script type="text/javascript" src="<?=AppConfig::URL_PUB?>js/jquery/1.12.4/jquery<?=YAM_MIN?>.js"></script>
	<![endif]-->
	<!--[if gte IE 9]><!-->
	<script type="text/javascript" src="<?=AppConfig::URL_PUB?>js/jquery/2.2.4/jquery<?=YAM_MIN?>.js"></script>
	<!--<![endif]-->
</head>
<body>
<?php

        if (isset($props['pageTitle'])) {
            ?>
	<h1><?=Core::html($props['pageTitle'])?></h1>
<?php
        }
        ?>

<?php
    }
}
?>