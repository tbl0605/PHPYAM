<?php
namespace PHPYAM\demo\application\views\form2;

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
class Confirm
{

    public static function render(array $props)
    {
        ?>
<form id="form-id" name="form-id" method="post"
	action="<?=$props['urlCreateAction']?>">

	<?=\PHPYAM\libs\IntelliForm::seed()?>

	<table>
		<thead>
			<tr>
				<th>Key</th>
				<th>Value</th>
			</tr>
		</thead>
		<tbody>
<?php

        foreach ($props['formValues'] as $key => $value) {
            ?>
			<tr>
				<td><?=Core::html($key)?></td>
				<td class="text-break"><?=Core::html($value)?></td>
			</tr>
<?php
        }
        ?>
		</tbody>
	</table>

	<input type="button" name="cancel-button" id="cancel-button-id"
		value="Cancel" onclick="location.href='<?=URL?>'" class="width-auto">
	<input type="submit" name="submit-button" id="submit-button-id"
		value="Submit" class="width-auto">
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
<?php
    }
}
?>