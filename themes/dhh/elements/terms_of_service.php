<?php
/* @var View $this */
/* @var Page $c */
defined('C5_EXECUTE') or die(_('Access Denied.'));

$page = $c;
$pageType = $page->getAttribute('page_type');

?>
<div class="page__section container">
  <div class="page__section__header">
    <h1><?php echo $page->getCollectionName(); ?></h1>
    <span class="sub-text"><?php echo $page->getCollectionDescription(); ?></span>
  </div>

  

  <div class="page__section__body">
    <div class="container-fluid">
      <?php $a = new Area('Terms of Service Content'); $a->display($c); ?>
    </div>
  </div>
</div>