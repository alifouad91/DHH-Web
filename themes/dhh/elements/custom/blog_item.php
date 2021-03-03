<?php
$dh = Loader::helper("date");
$title = $c->getCollectionName();
$date = $c->getCollectionDatePublic();
$date = $dh->formatCustom('M Y', $c->getCollectionDatePublic());
$category = $c->getAttribute('category');
$author = $c->getAttribute('author');
$page_image = $c->getAttribute('page_image');
?>

<div class="page__blogitem">
  <div class="container">
    <div class="row">
      <div class="col-lg-2">
        <a href="<?php echo View::url('/blog'); ?>" class="back-to-blog">
        <img src="<?php echo $this->getThemePath(); ?>/dist/images/back-to-blog.svg" />
        Back to Blog</a>
      </div>
      <div class="col-lg-10">
        <div class="page__blogitem__title">
          <p class="small"><?php echo $category; ?></p>
          <h1><?php echo $title; ?></h1>
          <span><?php echo $author.' · '.$date;?></span>
        </div>

        <div class="page__blogitem__details">
          <img src="<?php echo $page_image->getURL();?>" alt="">
          <div class="page__blogitem__details__content__heading">
            <?php $a = new Area('News Heading'); $a->display($c); ?>
          </div>
          <div class="page__blogitem__details__content">
            <?php $a = new Area('News Content'); $a->display($c); ?>
          </div>
        </div>
      </div>
    </div>
  </div>
</div>