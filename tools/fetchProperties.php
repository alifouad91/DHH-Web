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

$query = $th->sanitize($_GET['query']);
$page  = (int) $th->sanitize($_GET['page']) ? : 1;

$valid = false;
if(User::isLoggedIn()) {
    $user = new User();
    if($user->isAdmin()) {
        $valid = true;
    }
}

if(!$valid) {
    return false;
}

$pl = new PropertyList();
$pl->setItemsPerPage(10);
$pl->filterByKeywords($query);

$properties = $pl->getPage($page);

$data = [];

foreach ($properties as $property) {
    $data[] = [
        'id'     => $property->getID(),
        'text'    => $property->getName() .' - '.$property->getCaption(),
//        'caption' => $property->getCaption(),
    ];
}

$result['results']       = $data;
$result['total_count'] = count($data);
$ah->sendResult($result);