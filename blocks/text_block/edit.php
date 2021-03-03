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
	<h2>Content</h2>
	<?php  Loader::element('editor_controls'); ?>
	<textarea id="field_2_wysiwyg_content" name="field_2_wysiwyg_content" class="ccm-advanced-editor"><?php  echo $field_2_wysiwyg_content; ?></textarea>
</div>

<div class="ccm-block-field-group">
	<h2>Image/Icon</h2>
	<?php  echo $al->image('field_3_image_fID', 'field_3_image_fID', 'Choose Image', $field_3_image); ?>
</div>

<div class="ccm-block-field-group">
	<h2>Page Link</h2>
	<?php  echo $ps->selectPage('field_4_link_cID', $field_4_link_cID); ?>
	<table border="0" cellspacing="3" cellpadding="0" style="width: 95%;">
		<tr>
			<td align="right" nowrap="nowrap"><label for="field_4_link_text">Link Text:</label>&nbsp;</td>
			<td align="left" style="width: 100%;"><?php  echo $form->text('field_4_link_text', $field_4_link_text, array('style' => 'width: 100%;')); ?></td>
		</tr>
	</table>
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
	<h2>Extra</h2>
	<?php  echo $form->text('field_6_textbox_text', $field_6_textbox_text, array('style' => 'width: 95%;')); ?>
</div>


