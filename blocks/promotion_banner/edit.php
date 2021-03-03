<?php  defined('C5_EXECUTE') or die("Access Denied.");
$al = Loader::helper('concrete/asset_library');
$ps = Loader::helper('form/page_selector');
Loader::element('editor_config');
?>

<style type="text/css" media="screen">
	.ccm-block-field-group h2 { margin-bottom: 5px; }
	.ccm-block-field-group td { vertical-align: middle; }
</style>

<div class="ccm-block-field-group">
	<h2>Title</h2>
	<?php  echo $form->text('field_1_textbox_text', $field_1_textbox_text, array('style' => 'width: 95%;')); ?>
</div>

<div class="ccm-block-field-group">
	<h2>Subtitle</h2>
	<?php  echo $form->text('field_2_textbox_text', $field_2_textbox_text, array('style' => 'width: 95%;')); ?>
</div>

<div class="ccm-block-field-group">
	<h2>Number of Properties</h2>
	<?php  echo $form->text('field_3_textbox_text', $field_3_textbox_text, array('style' => 'width: 95%;')); ?>
</div>

<div class="ccm-block-field-group">
	<h2>Average Cost</h2>
	<?php  echo $form->text('field_4_textbox_text', $field_4_textbox_text, array('style' => 'width: 95%;')); ?>
</div>

<div class="ccm-block-field-group">
	<h2>External Link</h2>
	<table border="0" cellspacing="3" cellpadding="0" style="width: 95%;">
		<tr>
			<td align="right" nowrap="nowrap"><label for="field_5_link_url">Link to URL:</label>&nbsp;</td>
			<td align="left" style="width: 100%;"><?php  echo $form->text('field_5_link_url', $field_5_link_url, array('style' => 'width: 100%;')); ?></td>
		</tr>
		<tr>
			<td align="right" nowrap="nowrap"><label for="field_5_link_text">Link Text:</label>&nbsp;</td>
			<td align="left" style="width: 100%;"><?php  echo $form->text('field_5_link_text', $field_5_link_text, array('style' => 'width: 100%;')); ?></td>
		</tr>
	</table>
</div>

<div class="ccm-block-field-group">
	<h2>Page Link</h2>
	<?php  echo $ps->selectPage('field_6_link_cID', $field_6_link_cID); ?>
	<table border="0" cellspacing="3" cellpadding="0" style="width: 95%;">
		<tr>
			<td align="right" nowrap="nowrap"><label for="field_6_link_text">Link Text:</label>&nbsp;</td>
			<td align="left" style="width: 100%;"><?php  echo $form->text('field_6_link_text', $field_6_link_text, array('style' => 'width: 100%;')); ?></td>
		</tr>
	</table>
</div>

<div class="ccm-block-field-group">
	<h2>Content</h2>
	<?php  Loader::element('editor_controls'); ?>
	<textarea id="field_7_wysiwyg_content" name="field_7_wysiwyg_content" class="ccm-advanced-editor"><?php  echo $field_7_wysiwyg_content; ?></textarea>
</div>

<div class="ccm-block-field-group">
	<h2>Image</h2>
	<?php  echo $al->image('field_8_image_fID', 'field_8_image_fID', 'Choose Image', $field_8_image); ?>

	<table border="0" cellspacing="3" cellpadding="0" style="width: 95%; margin-top: 5px;">
		<tr>
			<td align="right" nowrap="nowrap"><label for="field_8_image_altText">Alt Text:</label>&nbsp;</td>
			<td align="left" style="width: 100%;"><?php  echo $form->text('field_8_image_altText', $field_8_image_altText, array('style' => 'width: 100%;')); ?></td>
		</tr>
	</table>
</div>

<div class="ccm-block-field-group">
	<h2>Extra</h2>
	<?php  echo $form->text('field_9_textbox_text', $field_9_textbox_text, array('style' => 'width: 95%;')); ?>
</div>


