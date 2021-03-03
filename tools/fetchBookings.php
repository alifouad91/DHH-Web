<?php
/**
 * Created by PhpStorm.
 * User: backend1
 * Date: 11/3/19
 * Time: 11:28 AM
 */

/** @var AjaxHelper $ah */
$ah = Loader::helper('ajax');
$th = Loader::helper('text');
/** @var Concrete5_Helper_Validation_Token $token_helper */
$token_helper = Loader::helper('validation/token');

$query = $th->sanitize($_GET['query']);

if(!$token_helper->validate('properties.fetch_booking'))
{
	return null;
}

$valid = false;
if(User::isLoggedIn()) {
    $user = new User();
    if($user->isAdmin()) {
        $valid = true;
    }
}
if(!$valid) {
    return null;
}

$bl = new BookingList();
$bl->filterByKeywords($query);
$bl->sortBy('b.bID','desc');
$bl->setItemsPerPage(10);
$bookings = $bl->getPage();

$data = [];
/** @var Booking $booking */
foreach ($bookings as $booking) {
    $data[] = [
        'id'     => $booking->getBID(),
        'text'    => $booking->getBookingNo(),
    ];
}

$result['results']       = $data;
$result['total_count'] = count($data);
$ah->sendResult($result);
