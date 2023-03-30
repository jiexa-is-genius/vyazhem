<?php

	chdir($_SERVER['DOCUMENT_ROOT']);
	define('DRUPAL_ROOT', $_SERVER['DOCUMENT_ROOT']);
	require_once './includes/bootstrap.inc';
	drupal_bootstrap(DRUPAL_BOOTSTRAP_FULL);
	$sql = "
		SELECT COUNT(*) as rc
		from node inner JOIN 
			 field_data_field_call_status on field_data_field_call_status.revision_id = node.nid INNER JOIN 
			 taxonomy_term_data on taxonomy_term_data.tid = field_data_field_call_status.field_call_status_tid
		WHERE node.type = 'callorder' AND taxonomy_term_data.tid = 58";
	$count = (int) db_query($sql)->fetchField();
	if($count > 0) { $count = 1; }
	echo($count);
?>