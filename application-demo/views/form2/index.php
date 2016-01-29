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
?>
<h2>Form input</h2>

<form id="formulaire-id" name="formulaire" method="post"
	action="<?=URL.'form2/index'?>">
	<fieldset>
		<?=\PHPYAM\libs\IntelliForm::seed()?>

		<legend>Please provide your name, email address (won't be published)
			and a comment</legend>

		<p>
			<label for="cname">Name (required, at least 2 characters)</label> <input
				id="cname" name="name" minlength="2" type="text" required />
		</p>
		<p>
			<label for="cemail">E-Mail (required)</label> <input id="cemail"
				type="email" name="email" required />
		</p>
		<p>
			<label for="curl">URL (optional)</label> <input id="curl" type="url"
				name="url" />
		</p>
		<p>
			<label for="ccomment">Your comment (required)</label>
			<textarea id="ccomment" name="comment" required></textarea>
		</p>
	</fieldset>

	<input type="submit" name="submit-button" id="submit-button-id"
		style="margin-top: 6px;" value="Validate">

	<div style="display:<?=$_logs !== '' ? 'block' : 'none'?>">
		<div id="feedback-panel-id"><?=$_logs?></div>
	</div>
</form>

<script type="text/javascript">
$(document).ready(function() {
	// Initialization of the form validation plugin
	$("#formulaire-id").validate();

	// Attach a submit handler to the form
	$("#submit-button-id").click(function(event) {

		// Stop form from submitting normally
		event.preventDefault();

		if (!$("#formulaire-id").valid()) {
			return;
		}

		var input = $("#submit-button-id")[0];
		input.disabled = true;
		input.value = "In progress...";

		$("#feedback-panel-id").parent().hide();
		// Clear result div
		$("#feedback-panel-id").html("");
		var jqLoadPanel = $("body");
		jqLoadPanel.resize();
		jqLoadPanel.loading({
			message: "Processing..."
		});

		// Send the data using post and put the results in a div
		$.ajax({
			// The link we are accessing
			url: "<?=URL.'form2/ajaxValidate'?>",
			// The type of request
			type: "post",
			// Get values from elements in the formular
			data: $("#formulaire-id").serialize(),
			// The type of data that is getting returned
			dataType: "html",
			// Never cache url
			cache: false,
			contentType: "application/x-www-form-urlencoded;charset=utf-8",
			success: function(strData) {
				if (strData !== "") {
					input.disabled = false;
					input.value = "Validate";
					jqLoadPanel.loading("stop");
					$("#feedback-panel-id").html("").html(strData).parent().show();
				} else {
					$("#formulaire-id").submit();
				}
			},
			error: function() {
				input.disabled = false;
				input.value = "Validate";
				jqLoadPanel.loading("stop");
				$("#feedback-panel-id").html("").html("An error has occurred. Please retry later.").parent().show();
			}
		});

		// Prevent default click.
		return false;
	});
});
</script>
