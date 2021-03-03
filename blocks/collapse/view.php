<?php  defined('C5_EXECUTE') or die("Access Denied.");
?>

<div data-collapse="true">
	<?php  if (!empty($field_1_textbox_text)): ?>
		<span><?php  echo htmlentities($field_1_textbox_text, ENT_QUOTES, APP_CHARSET); ?></span>
	<?php  endif; ?>

	<?php  if (!empty($field_2_wysiwyg_content)): ?>
		<div>
		<?php  echo $field_2_wysiwyg_content; ?>
		</div>
	<?php  endif; ?>
</div>



