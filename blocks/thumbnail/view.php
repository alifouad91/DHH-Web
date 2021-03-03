<?php  defined('C5_EXECUTE') or die("Access Denied.");
$nh = Loader::helper('navigation');
?>

<div class="col-lg-3 thumb">
	<div class=" thumb__team">
		<?php  if (!empty($field_3_image)): ?>
			<img src="<?php  echo $field_3_image->src; ?>" alt="Field Image" />
		<?php  endif; ?>
		<?php  if (!empty($field_1_textbox_text)): ?>
			<span class="sub-header-1"><?php  echo htmlentities($field_1_textbox_text, ENT_QUOTES, APP_CHARSET); ?></span>
		<?php  endif; ?>

		<?php  if (!empty($field_2_textbox_text)): ?>
			<p class="small"><?php  echo htmlentities($field_2_textbox_text, ENT_QUOTES, APP_CHARSET); ?></p>
		<?php  endif; ?>
	</div>
</div>




<?php  if (!empty($field_4_wysiwyg_content)): ?>
	<?php  //echo $field_4_wysiwyg_content; ?>
<?php  endif; ?>

<?php  if (!empty($field_5_link_cID)):
	$link_url = $nh->getLinkToCollection(Page::getByID($field_5_link_cID), true);
	$link_text = empty($field_5_link_text) ? $link_url : htmlentities($field_5_link_text, ENT_QUOTES, APP_CHARSET);
	?>
	<!-- <a href="<?php  echo $link_url; ?>"><?php  echo $link_text; ?></a> -->
<?php  endif; ?>

<?php  if (!empty($field_6_link_url)):
	$link_url = $this->controller->valid_url($field_6_link_url);
	$link_text = empty($field_6_link_text) ? $field_6_link_url : htmlentities($field_6_link_text, ENT_QUOTES, APP_CHARSET);
	?>
	<!-- <a href="<?php  echo $link_url; ?>" target="_blank"><?php  echo $link_text; ?></a> -->
<?php  endif; ?>


