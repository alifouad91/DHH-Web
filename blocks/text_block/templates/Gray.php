<?php  defined('C5_EXECUTE') or die("Access Denied.");
$nh = Loader::helper('navigation');
?>

<div class="textblock__gray" data-aos="fade-up" data-aos-delay="300"">
<?php  if (!empty($field_3_image)): ?>
      <div class="background-img" style="background-image:url(<?php echo $field_3_image->src; ?>)" ></div>
    <?php  endif; ?>
  <div className="title">
    <?php  if (!empty($field_1_textbox_text)): ?>
			<h3><?php  echo htmlentities($field_1_textbox_text, ENT_QUOTES, APP_CHARSET); ?></h3>
    <?php  endif; ?>
   
    <?php  if (!empty($field_3_image)): ?>
      <!-- <img class="" src="<?php  //echo $field_3_image->src; ?>" alt="" /> -->
    <?php  endif; ?>
  </div>
  <div>
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


