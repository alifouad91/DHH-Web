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

if(!$token_helper->validate('properties.fetch_user'))
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
    return false;
}

$ul = new UserList();
$ul->filterByGroup(LANDLORD_GROUP_NAME);
$ul->filterByKeywords($query);

$users = $ul->get();

$data = [];
/** @var UserInfo $user */
foreach ($users as $user) {
    $data[] = [
        'id'     => $user->getUserID(),
        'text'    => $user->getFullName(),
//        'caption' => $user->getCaption(),
    ];
}

$result['results']       = $data;
$result['total_count'] = count($data);
$ah->sendResult($result);
