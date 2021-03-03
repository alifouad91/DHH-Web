<?php  defined('C5_EXECUTE') or die("Access Denied.");
$nh = Loader::helper('navigation');
?>

<?php  if (!empty($field_1_textbox_text)): ?>
	<?php  //echo htmlentities($field_1_textbox_text, ENT_QUOTES, APP_CHARSET); ?>
<?php  endif; ?>

<?php  if (!empty($field_2_textbox_text)): ?>
	<?php  //echo htmlentities($field_2_textbox_text, ENT_QUOTES, APP_CHARSET); ?>
<?php  endif; ?>

<?php  if (!empty($field_3_textbox_text)): ?>
	<?php  //echo htmlentities($field_3_textbox_text, ENT_QUOTES, APP_CHARSET); ?>
<?php  endif; ?>

<?php  if (!empty($field_4_textbox_text)): ?>
	<?php  //echo htmlentities($field_4_textbox_text, ENT_QUOTES, APP_CHARSET); ?>
<?php  endif; ?>

<div class="col-sm-12 col-md-6" data-aos="fade-in">
	<div class="banner__promo">
		<div class="banner__promo__bg" style="background-image: url(<?php echo $field_8_image->src;?>)"></div>
		<div class="banner__promo__remarks">
			<?php  if (!empty($field_3_textbox_text)): ?>
				<p><?php  echo htmlentities($field_3_textbox_text, ENT_QUOTES, APP_CHARSET); ?> properties</p>
			<?php  endif; ?>
			<?php  if (!empty($field_4_textbox_text)): ?>
				<p>From <b><?php  echo htmlentities($field_4_textbox_text, ENT_QUOTES, APP_CHARSET); ?></b> per night</p>
			<?php  endif; ?>
		</div>
		<div class="banner__promo__title">
			<?php  if (!empty($field_1_textbox_text)): ?>
				<span class="large-text">
					<?php  echo htmlentities($field_1_textbox_text, ENT_QUOTES, APP_CHARSET); ?>
				</span>
			<?php  endif; ?>
			<?php  if (!empty($field_2_textbox_text)): ?>
				<p><?php  echo htmlentities($field_2_textbox_text, ENT_QUOTES, APP_CHARSET); ?></p>
			<?php  endif; ?>
		</div>
	</div>
</div>

<?php  if (!empty($field_5_link_url)):
	$link_url = $this->controller->valid_url($field_5_link_url);
	$link_text = empty($field_5_link_text) ? $field_5_link_url : htmlentities($field_5_link_text, ENT_QUOTES, APP_CHARSET);
	?>
	<!-- <a href="<?php  echo $link_url; ?>" target="_blank"><?php  echo $link_text; ?></a> -->
<?php  endif; ?>

<?php  if (!empty($field_6_link_cID)):
	$link_url = $nh->getLinkToCollection(Page::getByID($field_6_link_cID), true);
	$link_text = empty($field_6_link_text) ? $link_url : htmlentities($field_6_link_text, ENT_QUOTES, APP_CHARSET);
	?>
	<!-- <a href="<?php  echo $link_url; ?>"><?php  echo $link_text; ?></a> -->
<?php  endif; ?>

<?php  if (!empty($field_7_wysiwyg_content)): ?>
	<?php  echo $field_7_wysiwyg_content; ?>
<?php  endif; ?>

<?php  if (!empty($field_8_image)): ?>
	<!-- <img src="<?php  echo $field_8_image->src; ?>" width="<?php  echo $field_8_image->width; ?>" height="<?php  echo $field_8_image->height; ?>" alt="<?php  echo $field_8_image_altText; ?>" /> -->
<?php  endif; ?>

<?php  if (!empty($field_9_textbox_text)): ?>
	<?php // echo htmlentities($field_9_textbox_text, ENT_QUOTES, APP_CHARSET); ?>
<?php  endif; ?>


