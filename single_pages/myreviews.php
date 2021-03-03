<?php
$page = $c;
?>
<div class="page__wrapper page__limit page__myreviews" >
  <div class="page__section container-fluid">
    <div class="row">
      <div class="col-lg-8 col-lg-offset-1">
          <div class="container-fluid">
            <div class="row">
              <div class="page__section__header">
                <h1>My Reviews</h1>
                <span class="sub-text"><?php echo $page->getCollectionDescription(); ?></span>
              </div>
            </div>
          </div>
      </div>
      <div class="col-lg-offset-1 col-lg-8 page__myreviews__render page__myreviews__details "  data-id="1"></div>
    </div>
  </div>
</div>