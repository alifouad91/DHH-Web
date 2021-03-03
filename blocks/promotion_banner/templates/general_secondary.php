<?php  defined('C5_EXECUTE') or die("Access Denied.");
$nh = Loader::helper('navigation');
?>
<div class="banner__general banner__general__secondary" data-aos="fade-in" data-aos-delay="300">
  <div>
  <?php  if (!empty($field_1_textbox_text)): ?>
    <h2>
      <?php  echo htmlentities($field_1_textbox_text, ENT_QUOTES, APP_CHARSET); ?>
    </h2>
  <?php  endif; ?>
  <?php  if (!empty($field_2_textbox_text)): ?>
    <p><?php  echo htmlentities($field_2_textbox_text, ENT_QUOTES, APP_CHARSET); ?></p>
  <?php  endif; ?>
  <?php  if (!empty($field_6_link_cID)):
    $link_url = $nh->getLinkToCollection(Page::getByID($field_6_link_cID), true);
    $link_text = empty($field_6_link_text) ? $link_url : htmlentities($field_6_link_text, ENT_QUOTES, APP_CHARSET);
	?>
	<a href="<?php  echo $link_url; ?>" class="ant-btn ant-btn-secondary"><?php  echo $link_text; ?></a>
  <?php  endif; ?>
  </div>
  
  
  
  <?php  if (!empty($field_8_image)): ?>
    <img src="<?php  echo $field_8_image->src; ?>"  alt="<?php  echo $field_8_image_altText; ?>" />
  <?php  endif; ?>
  </div>