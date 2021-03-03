<?php  defined('C5_EXECUTE') or die("Access Denied.");
?>

<?php  if (!empty($field_1_image)): ?>
	<img src="<?php  echo $field_1_image->src; ?>" width="<?php  echo $field_1_image->width; ?>" height="<?php  echo $field_1_image->height; ?>" alt="" />
<?php  endif; ?>

<?php  if (!empty($field_2_textbox_text)): ?>
	<?php  echo htmlentities($field_2_textbox_text, ENT_QUOTES, APP_CHARSET); ?>
<?php  endif; ?>

<?php  if (!empty($field_3_date_value)): ?>
	<?php  echo date('d-m-Y', strtotime($field_3_date_value)); ?>
<?php  endif; ?>

<?php  if (!empty($field_4_textbox_text)): ?>
	<?php  echo htmlentities($field_4_textbox_text, ENT_QUOTES, APP_CHARSET); ?>
<?php  endif; ?>

<?php  if (!empty($field_5_textbox_text)): ?>
	<?php  echo htmlentities($field_5_textbox_text, ENT_QUOTES, APP_CHARSET); ?>
<?php  endif; ?>

<?php  if (!empty($field_6_textarea_text)): ?>
	<?php  echo nl2br(htmlentities($field_6_textarea_text, ENT_QUOTES, APP_CHARSET)); ?>
<?php  endif; ?>

<?php  if (!empty($field_7_textbox_text)): ?>
	<?php  echo htmlentities($field_7_textbox_text, ENT_QUOTES, APP_CHARSET); ?>
<?php  endif; ?>


<div class="landlord-review-item"

<?php  if (!empty($field_1_image)): ?>
	data-image="<?php  echo $field_1_image->src; ?>"
<?php  endif; ?>
<?php  if (!empty($field_2_textbox_text)): ?>
	data-userName="<?php  echo htmlentities($field_2_textbox_text, ENT_QUOTES, APP_CHARSET); ?>""
<?php  endif; ?>
<?php  if (!empty($field_3_date_value)): ?>
	data-createdAt="<?php  echo date('d-m-Y', strtotime($field_3_date_value)); ?>"
<?php  endif; ?>
<?php  if (!empty($field_4_textbox_text)): ?>
	data-location="<?php  echo htmlentities($field_4_textbox_text, ENT_QUOTES, APP_CHARSET); ?>"
<?php  endif; ?>
<?php  if (!empty($field_5_textbox_text)): ?>
	data-designation="<?php  echo htmlentities($field_5_textbox_text, ENT_QUOTES, APP_CHARSET); ?>"
<?php  endif; ?>

<?php  if (!empty($field_6_textarea_text)): ?>
	data-reviewComment="<?php  echo nl2br(htmlentities($field_6_textarea_text, ENT_QUOTES, APP_CHARSET)); ?>"
<?php  endif; ?>
>
</div>