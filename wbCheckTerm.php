<?php
	/**
	 * Проверяем термин на категориях
	 */
	chdir($_SERVER['DOCUMENT_ROOT']);
	define('DRUPAL_ROOT', $_SERVER['DOCUMENT_ROOT']);
	require_once './includes/bootstrap.inc';
	drupal_bootstrap(DRUPAL_BOOTSTRAP_FULL);
	
	$tid = isset($_GET['tid']) ? $_GET['tid'] : 0;
	$term = taxonomy_term_load($tid);
	$res = isset($term->field_inc_field['und'][0]['value']) ? $term->field_inc_field['und'][0]['value'] : null;
	echo($res);
	die();