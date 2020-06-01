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
<h2>Confirmation of data entry</h2>

<form id="form-id" name="form-id" method="post" action="<?=$_urlCreateAction?>">

	<?=\PHPYAM\libs\IntelliForm::seed()?>

	<table border="1" style="margin-bottom: 6px;">
		<thead>
			<tr>
				<th>Key</th>
				<th>Value</th>
			</tr>
		</thead>
		<tbody>
<?php

foreach ($formValues as $key => $value) {
    ?>
			<tr>
				<td><?=Core::html($key)?></td>
				<td><?=Core::html($value)?></td>
			</tr>
<?php
}
?>
		</tbody>
	</table>

	<input type="button" name="cancel-button" id="cancel-button-id"
		value="Cancel" onclick="location.href='<?=URL?>'"> <input
		type="submit" name="submit-button" id="submit-button-id"
		value="Submit">
</form>

<script type="text/javascript">
$(document).ready(function() {
	// Attach a submit handler to the form
	$("#submit-button-id").click(function(event) {
		// Stop form from submitting normally
		event.preventDefault();

		var input = this;
		input.disabled = true;
		input.value = "In progress...";

		input = $("#cancel-button-id")[0];
		input.disabled = true;

		var jqLoadPanel = $("body");
		jqLoadPanel.resize();
		jqLoadPanel.loading({
			message: "Processing..."
		});
		$("#form-id").submit();

		// Prevent default click.
		return false;
	});
});
</script>