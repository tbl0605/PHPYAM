<?php
namespace PHPYAM\demo\application\views\form1;

use PHPYAM\core\Core;
use PHPYAM\libs\Store;

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
class Index
{

    public static function render(array $props)
    {
        ?>
<form id="form-id" name="form-id" method="post" action="<?=Store::getRequired('URL') . 'form1/index'?>">
	<fieldset>
		<?=\PHPYAM\libs\IntelliForm::seed()?>

		<legend>Please provide your name, email address (won't be published)
			and a comment</legend>

		<input placeholder="Name (required, at least 2 characters)" id="cname"
			name="name" minlength="2" type="text" required />

		<input placeholder="E-Mail (required)" id="cemail"
			name="email" type="email" required />

		<input placeholder="URL (optional)" id="curl"
			name="url" type="url" />

		<textarea placeholder="Your comment (required)" id="ccomment"
			name="comment" required></textarea>
	</fieldset>

	<input type="submit" name="submit-button" id="submit-button-id"
		value="Validate and submit">

	<div style="display: <?=$props['logs'] !== '' ? 'block' : 'none'?>;">
		<div id="feedback-panel-id"><?=$props['logs']?></div>
	</div>
</form>

<div style="position: fixed; bottom: 0; margin: 10px;">
	<a href="<?=Core::url(Store::getRequired('DEFAULT_CONTROLLER'), Store::getRequired('DEFAULT_ACTION'))?>">Go back to
		home page</a>
</div>

<script type="text/javascript">
$(document).ready(function() {
	// Initialization of the form validation plugin
	$("#form-id").validate();

	// Attach a submit handler to the form
	$("#submit-button-id").click(function(event) {

		// Stop form from submitting normally
		event.preventDefault();

		var jqForm = $("#form-id");

		if (!jqForm.valid()) {
			return;
		}

		jqForm[0].submit();

		// Prevent default click.
		return false;
	});
});
</script>
<?php
    }
}
?>