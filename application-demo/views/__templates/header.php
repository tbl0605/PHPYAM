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
use \PHPYAM\core\Core as Core;
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
	<title><?=Core::html(TITLE)?></title>
	<meta name="description" content="<?=Core::html(TITLE)?>">
	<meta name="viewport" content="width=device-width, initial-scale=1.0">

	<!-- css -->
	<link rel="stylesheet" type="text/css" href="<?=URL_PUB?>css/jquery-loading/1.2.0/jquery.loading<?=YAM_MIN?>.css" />

	<!-- local -->
	<link rel="stylesheet" type="text/css" href="<?=URL_PUB?>css/common.css" />

	<!-- javaScript -->
	<!-- jQuery -->
	<!--[if lt IE 9]>
	<script type="text/javascript" src="<?=URL_PUB?>js/jquery/1.12.4/jquery<?=YAM_MIN?>.js"></script>
	<![endif]-->
	<!--[if gte IE 9]><!-->
	<script type="text/javascript" src="<?=URL_PUB?>js/jquery/2.2.4/jquery<?=YAM_MIN?>.js"></script>
	<!--<![endif]-->
</head>
<body>
<?php if (isset($_pageTitle)) {?>
	<h1><?=Core::html($_pageTitle)?></h1>
<?php }?>

