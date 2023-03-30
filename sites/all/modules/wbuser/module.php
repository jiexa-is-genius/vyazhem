<?php
	/**
	 * Перенаправление со страницы профиля пользователя на страницу с моими заказами
	 */
	global $user;
	$q = isset($_GET['q']) ? $_GET['q'] : null;
	$tmpQ = explode('?', $q);
	$q = isset($tmpQ[0]) ? $tmpQ[0] : null;
	$urlParams = explode('/', $q);
	$urlParamsCount = count($urlParams);
	
	if($urlParamsCount == 1 or $urlParamsCount == 2) {
		$uid = isset($user->uid) ? (int) $user->uid : null;
		if(empty($uid)) { $uid = null; }
		if(($q == 'user' or $q == 'user/' . $uid) and !is_null($uid)) {
			header('Location: /user-profile');
			die;
		}
	}
?>