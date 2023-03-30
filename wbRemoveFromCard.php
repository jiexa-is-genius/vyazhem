<?php
	/**
	 * Проверяем термин на категориях
	 */
	chdir($_SERVER['DOCUMENT_ROOT']);
	define('DRUPAL_ROOT', $_SERVER['DOCUMENT_ROOT']);
	require_once './includes/bootstrap.inc';
	drupal_bootstrap(DRUPAL_BOOTSTRAP_FULL);
	global $user;
	
	$uuid = isset($_COOKIE['user_uuid']) ? $_COOKIE['user_uuid'] : null;
	$id = isset($_GET['id']) ? $_GET['id'] : null;
	$uid = isset($user->uid) ? (int) $user->uid : null;
	
	/*if(!is_null($uid)) {
		$sql = "
			delete from wb_orders
			WHERE id = :id and
				  uid = :uid";
		db_query($sql, [':id' => $id, ':uid' => $uid]);
	} else {
		$sql = "
			delete from wb_orders
			WHERE id = :id and
				  uuid = :uuid";
		db_query($sql, [':id' => $id, ':uuid' => $uuid]);
	}*/
	$sql = "
		delete from wb_orders
		WHERE id = :id";
	db_query($sql, [':id' => $id]);
	
	die('finish');