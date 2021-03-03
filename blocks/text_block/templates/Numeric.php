<?php  defined('C5_EXECUTE') or die("Access Denied.");
$nh = Loader::helper('navigation');
?>

<div class="textblock__numeric" data-aos="fade-up" data-aos-delay="300">
  <?php  if (!empty($field_6_textbox_text)): ?>
    <span><?php  echo htmlentities($field_6_textbox_text, ENT_QUOTES, APP_CHARSET); ?></span>
  <?php  endif; ?>
  <?php  if (!empty($field_1_textbox_text)): ?>
    <h5><?php  echo htmlentities($field_1_textbox_text, ENT_QUOTES, APP_CHARSET); ?></h5>
  <?php  endif; ?>
  <?php  if (!empty($field_2_wysiwyg_content)): ?>
    <?php  echo $field_2_wysiwyg_content; ?>
  <?php  endif; ?>
  <?php  if (!empty($field_4_link_cID)):
    $link_url = $nh->getLinkToCollection(Page::getByID($field_4_link_cID), true);
    $link_text = empty($field_4_link_text) ? $link_url : htmlentities($field_4_link_text, ENT_QUOTES, APP_CHARSET);
    ?>
    <a href="<?php  echo $link_url; ?>"><?php  echo $link_text; ?></a>
  <?php  endif; ?>
</div>