<?php

	chdir($_SERVER['DOCUMENT_ROOT']);
	define('DRUPAL_ROOT', $_SERVER['DOCUMENT_ROOT']);
	require_once './includes/bootstrap.inc';
	drupal_bootstrap(DRUPAL_BOOTSTRAP_FULL);
	
	global $user; 
	// Смотрим, разрешаем ли администраторам смотреть секции на сайте
	$roles = isset($user->roles) ? $user->roles : array();
	$adminRoles = ['administrator', 'сustomer'];
	$isAdmin = false;
	foreach($adminRoles as $role) {
		if(in_array($role, $roles)) {
			$isAdmin = true;
			break;
		}
	}
	$response = array(
		'result' => false,
		'code' => null,
		'name' => null,
	);
	if($isAdmin) {
		$nid = isset($_GET['nid']) ? (int) $_GET['nid'] : null;
		$tid = isset($_GET['tid']) ? (int) $_GET['tid'] : null;
		$node = node_load($nid);
		$term = taxonomy_term_load($tid);
		if(isset($node->nid) and isset($term->tid)) {
			$node->field_call_status['und'][0]['tid'] = $term->tid;
			node_save($node);
		}
		$response = array(
			'result' => true,
			'nid' => $nid,
			'code' => $term->tid,
			'name' => $term->name,
		);
	}
	
	header('Content-Type: application/json');
	echo json_encode($response);
	die();