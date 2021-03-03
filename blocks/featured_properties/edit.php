<?php  defined('C5_EXECUTE') or die("Access Denied.");
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
	<h2>Filter Keyword</h2>
	<?php  echo $form->text('field_2_textbox_text', $field_2_textbox_text, array('style' => 'width: 95%;')); ?>
</div>

<div class="ccm-block-field-group">
	<h2>Select Filter</h2>
	<h6>(This can be property name, location, or description)</h6>
	<?php
	    echo $form->select('field_3_select_value', $field_3_select_options, $field_3_select_value);
	?>
</div>

<div class="ccm-block-field-group">
	<h2>Items to Display</h2>
	<?php 
	$options = array(
		// '2' => '--Choose One--',
		'3' => '3',
		'4' => '4',
	);
	echo $form->select('field_4_select_value', $options, $field_4_select_value);
	?>
</div>

<div class="ccm-block-field-group">
	<h2>Extra</h2>
	<?php  echo $form->text('field_5_textbox_text', $field_5_textbox_text, array('style' => 'width: 95%;')); ?>
</div>


