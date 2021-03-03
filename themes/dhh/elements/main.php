

<?php
/* @var View $this */
/* @var Page $c */
defined('C5_EXECUTE') or die(_('Access Denied.'));

$page = $c;
$pageType = $page->getAttribute('page_type');
?>
<section class="page__limit page__wrapper <?php echo $pageType; ?>">
    <?php
    switch ($pageType) {
        case 'home':
            $this->inc('elements/custom/home.php');
            break;
        case 'become-host':
            $this->inc('elements/custom/host.php');
            break;
        // case 'about':
        // case 'contact':
        case 'behavior':
        case 'terms':
            $this->inc('elements/terms.php');
            break;
        case 'faq':
        case 'about':
        case 'contact':
        // case 'behavior':
            $this->inc('elements/custom/static.php');
            break;
        case 'privacy':
            $this->inc('elements/privacy.php');
            break;
        case 'house-rules':
            $this->inc('elements/house_rules.php');
            break;
        case 'cancellation-policy':
            $this->inc('elements/cancellation_policy.php');
            break;
        case 'terms-of-service':
            $this->inc('elements/terms_of_service.php');
            break;
        case 'guest-refund-policy':
            $this->inc('elements/guest_refund_policy.php');
            break;
        case 'blog':
            $this->inc('elements/custom/blog_item.php');
            break;
        case 'listing':
        case 'rental':
            $this->inc('elements/custom/listing.php');
            break;
        default:
            break;
    }
    ?>
</section>