<?php  defined('C5_EXECUTE') or die("Access Denied.");
$nh = Loader::helper('navigation');
?>

<div class="col-sm-12">
	<div class="banner__promo__full" data-aos="fade-in" data-aos-delay="300" style="background-image:url(<?php echo $field_8_image->src;?>)">
		<?php  if (!empty($field_8_image)): ?>
			<!-- <img src="<?php  echo $field_8_image->src; ?>" alt="<?php  echo $field_8_image_altText; ?>" /> -->
		<?php  endif; ?>
		<div class="container-fluid">
			<div class="row">
				<div class="col-sm-0 col-md-7"></div>
				<div class="col-sm-5">
					<div class="banner__promo__full__title">
						<?php  if (!empty($field_1_textbox_text)): ?>
							<h1>
								<?php  echo htmlentities($field_1_textbox_text, ENT_QUOTES, APP_CHARSET); ?>
							</h1>
						<?php  endif; ?>
						<?php  if (!empty($field_2_textbox_text)): ?>
							<h1><?php  echo htmlentities($field_2_textbox_text, ENT_QUOTES, APP_CHARSET); ?></h1>
						<?php  endif; ?>
					</div>
				</div>
			</div>
		</div>
		<div class="separator">
			<?php  if (!empty($field_7_wysiwyg_content)): ?>
				<?php  echo $field_7_wysiwyg_content; ?>
			<?php  endif; ?>
			<div class="quarter-circle"></div>
			<h5>Driven<br/>Holiday<br/>Homes</h5>
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
	<?php  //echo $field_7_wysiwyg_content; ?>
<?php  endif; ?>

<?php  if (!empty($field_8_image)): ?>
	<!-- <img src="<?php  echo $field_8_image->src; ?>" width="<?php  echo $field_8_image->width; ?>" height="<?php  echo $field_8_image->height; ?>" alt="<?php  echo $field_8_image_altText; ?>" /> -->
<?php  endif; ?>

<?php  if (!empty($field_9_textbox_text)): ?>
	<?php // echo htmlentities($field_9_textbox_text, ENT_QUOTES, APP_CHARSET); ?>
<?php  endif; ?>


