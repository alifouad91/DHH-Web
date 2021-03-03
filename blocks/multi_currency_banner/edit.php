<?php  defined('C5_EXECUTE') or die("Access Denied.");
$al = Loader::helper('concrete/asset_library');
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
	<h2>Cost (AED)</h2>
	<?php  echo $form->text('field_4_textbox_text', $field_4_textbox_text, array('style' => 'width: 95%;')); ?>
</div>

<div class="ccm-block-field-group">
	<h2>Cost (USD)</h2>
	<?php  echo $form->text('field_5_textbox_text', $field_5_textbox_text, array('style' => 'width: 95%;')); ?>
</div>

<div class="ccm-block-field-group">
	<h2>Cost (EUR)</h2>
	<?php  echo $form->text('field_6_textbox_text', $field_6_textbox_text, array('style' => 'width: 95%;')); ?>
</div>

<div class="ccm-block-field-group">
	<h2>Cost (SR)</h2>
	<?php  echo $form->text('field_7_textbox_text', $field_7_textbox_text, array('style' => 'width: 95%;')); ?>
</div>

<div class="ccm-block-field-group">
	<h2>Cost (RUB)</h2>
	<?php  echo $form->text('field_8_textbox_text', $field_8_textbox_text, array('style' => 'width: 95%;')); ?>
</div>

<div class="ccm-block-field-group">
	<h2>Cost (KWD)</h2>
	<?php  echo $form->text('field_9_textbox_text', $field_9_textbox_text, array('style' => 'width: 95%;')); ?>
</div>

<div class="ccm-block-field-group">
	<h2>Image</h2>
	<?php  echo $al->image('field_10_image_fID', 'field_10_image_fID', 'Choose Image', $field_10_image); ?>
</div>

<div class="ccm-block-field-group">
	<h2>Link</h2>
	<?php  echo $form->text('field_11_textbox_text', $field_11_textbox_text, array('style' => 'width: 95%;')); ?>
</div>


