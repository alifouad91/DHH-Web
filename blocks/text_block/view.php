<?php  defined('C5_EXECUTE') or die("Access Denied.");
$nh = Loader::helper('navigation');
?>

<div class="textblock__simple" data-aos-delay="300" data-aos="fade-up">
<?php  if (!empty($field_3_image)): ?>
	<img class="emoji-img" src="<?php  echo $field_3_image->src; ?>" alt="Field image" />
<?php  endif; ?>
	<div>
		<?php  if (!empty($field_1_textbox_text)): ?>
			<h5><?php  echo htmlentities($field_1_textbox_text, ENT_QUOTES, APP_CHARSET); ?></h5>
		<?php  endif; ?>
		<?php  if (!empty($field_2_wysiwyg_content)): ?>
			<?php  echo $field_2_wysiwyg_content; ?>
		<?php  endif; ?>
	</div>
</div>






<?php  if (!empty($field_4_link_cID)):
	$link_url = $nh->getLinkToCollection(Page::getByID($field_4_link_cID), true);
	$link_text = empty($field_4_link_text) ? $link_url : htmlentities($field_4_link_text, ENT_QUOTES, APP_CHARSET);
	?>
	<!-- <a href="<?php  echo $link_url; ?>"><?php  echo $link_text; ?></a> -->
<?php  endif; ?>

<?php  if (!empty($field_5_link_url)):
	$link_url = $this->controller->valid_url($field_5_link_url);
	$link_text = empty($field_5_link_text) ? $field_5_link_url : htmlentities($field_5_link_text, ENT_QUOTES, APP_CHARSET);
	?>
	<!-- <a href="<?php  echo $link_url; ?>" target="_blank"><?php  echo $link_text; ?></a> -->
<?php  endif; ?>

<?php  if (!empty($field_6_textbox_text)): ?>
	<!-- <?php  echo htmlentities($field_6_textbox_text, ENT_QUOTES, APP_CHARSET); ?> -->
<?php  endif; ?>


