<?php  defined('C5_EXECUTE') or die("Access Denied.");
$al = Loader::helper('concrete/asset_library');
$dt = Loader::helper('form/date_time');
?>

<style type="text/css" media="screen">
	.ccm-block-field-group h2 { margin-bottom: 5px; }
	.ccm-block-field-group td { vertical-align: middle; }
</style>

<div class="ccm-block-field-group">
	<h2>Avatar</h2>
	<?php  echo $al->image('field_1_image_fID', 'field_1_image_fID', 'Choose Image', $field_1_image); ?>
</div>

<div class="ccm-block-field-group">
	<h2>Name</h2>
	<?php  echo $form->text('field_2_textbox_text', $field_2_textbox_text, array('style' => 'width: 95%;')); ?>
</div>

<div class="ccm-block-field-group">
	<h2>Date</h2>
	<?php  echo $dt->date('field_3_date_value', $field_3_date_value); ?>
</div>

<div class="ccm-block-field-group">
	<h2>Property Name</h2>
	<?php  echo $form->text('field_4_textbox_text', $field_4_textbox_text, array('style' => 'width: 95%;')); ?>
</div>

<div class="ccm-block-field-group">
	<h2>Designation</h2>
	<?php  echo $form->text('field_5_textbox_text', $field_5_textbox_text, array('style' => 'width: 95%;')); ?>
</div>

<div class="ccm-block-field-group">
	<h2>Comment</h2>
	<textarea id="field_6_textarea_text" name="field_6_textarea_text" rows="5" style="width: 95%;"><?php  echo $field_6_textarea_text; ?></textarea>
</div>

<div class="ccm-block-field-group">
	<h2>Extra</h2>
	<?php  echo $form->text('field_7_textbox_text', $field_7_textbox_text, array('style' => 'width: 95%;')); ?>
</div>


