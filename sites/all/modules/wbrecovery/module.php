<?php
	
	/**
	 * Восстановление пароля
	 */
	function wbrecovery_menu() {
		$items['account/recovery'] = array(
			'title' => 'Восстановление пароля',
			'page callback' => 'render_wbrecovery_form',
			'page arguments' => array('wbrecovery_form'),
			'access callback' => 'user_access',
			'access arguments' => array('access content'),
			'type' => MENU_NORMAL_ITEM,
		 );
		 
		 $items['account/recovery/%'] = array(
			'title' => 'Восстановление пароля',
			'page callback' => 'render_wbrecovery_change_form',
			'page arguments' => array('wbrecovery_change_form'),
			'access callback' => 'user_access',
			'access arguments' => array('access content'),
			'type' => MENU_NORMAL_ITEM,
		 );
		 
		return $items;
	}
	function wbrecovery_form($form, &$form_state) {
		$form['email'] = array(
			'#type' => 'textfield',
			'#title' => 'E-mail',
			'#size' => 60,
			'#maxlength' => 255,
			'#required' => TRUE,
		);
		$form['iin'] = array(
			'#type' => 'textfield',
			'#title' => 'ИИН',
			'#size' => 60,
			'#maxlength' => 255,
			'#required' => TRUE,
		);
		
		$form['submit_button'] = array(
			'#type' => 'submit',
			'#value' => t('Восстановить пароль'),
		);
	  
	  	return $form;
	}
	
	function render_wbrecovery_form() {
		global $user;
		if(isset($user->uid) and !empty($user->uid)) {
			header('Location: /');
			die;
		}
		$form = drupal_get_form('wbrecovery_form');
		drupal_add_css(drupal_get_path('module', 'wbrecovery') .'/css/wbrecovery.css');
		drupal_add_js(drupal_get_path('module', 'wbrecovery') .'/js/wbrecovery.js');
		return wbrecovery_usertabs() . drupal_render($form);
	}
	
	function wbrecovery_change_form($form, &$form_state) {
		
		$form['password'] = array(
			'#type' => 'password',
			'#title' => 'Пароль',
			'#size' => 60,
			'#maxlength' => 32,
 			'#required' => TRUE,
		);
		
		$form['password_confirm'] = array(
			'#type' => 'password',
			'#title' => 'Подтвердите пароль',
			'#size' => 60,
			'#maxlength' => 32,
 			'#required' => TRUE,
		);
		
		$form['submit_button'] = array(
			'#type' => 'submit',
			'#value' => t('Сохранить пароль'),
		);
	  
	  	return $form;
	}
	
	function render_wbrecovery_change_form() {
		$token = wbrecovery_get_token();
		
		$db = db_query('
			SELECT uid 
			FROM wb_recovery
			WHERE recoveryDate > DATE_SUB(NOW(), INTERVAL 30 MINUTE) AND 
			token = :token
			limit 1', [':token' => $token])->fetchObject();
		$uid = isset($db->uid) ? $db->uid : null;
		
		if(is_null($uid)) {
			db_query('delete from wb_recovery where token = :token', [ ':token' => $token ]);
			drupal_set_message(t('Время жизни сессии истекло!'), 'error');
			header('Location: /account/recovery');
			die;
		}
		
		$form = drupal_get_form('wbrecovery_change_form');
		drupal_add_css(drupal_get_path('module', 'wbrecovery') .'/css/wbrecovery.css');
		drupal_add_js(drupal_get_path('module', 'wbrecovery') .'/js/wbrecovery.js');
		return wbrecovery_usertabs() . drupal_render($form);
	}
	
	function wbrecovery_change_form_validate($form, &$form_state) {
		if(strlen($form['password']['#value']) < 6) {
			form_set_error('password', t('Пароль не может содержать в себе менее 6-ти символов!'));
		}
		if(strlen($form['password']['#value']) > 32) {
			form_set_error('password', t('Пароль не может содержать в себе более 32-х символов!'));
		}
		if(preg_match( '#[а-яА-Я]#i', $form['password']['#value'])) {
			form_set_error('password', t('Пароль не должен содержать в себе кирилические символы!'));
		}
		if($form['password']['#value'] <> $form['password_confirm']['#value']) {
			form_set_error('password', t('Введённые пароли не совпадают!'));
			form_set_error('password_confirm', '');
		}
	}
	function wbrecovery_change_form_submit($form, &$form_state) {
		$token = wbrecovery_get_token();
		
		$db = db_query('
			SELECT uid 
			FROM wb_recovery
			WHERE recoveryDate > DATE_SUB(NOW(), INTERVAL 30 MINUTE) AND 
			token = :token
			limit 1', [':token' => $token])->fetchObject();
		db_query('delete from wb_recovery where token = :token', [ ':token' => $token ]);
		$uid = isset($db->uid) ? $db->uid : null;
		
		if(is_null($uid)) {
			db_query('delete from wb_recovery where token = :token', [ ':token' => $token ]);
			drupal_set_message(t('Время жизни сессии истекло!'), 'error');
			header('Location: /account/recovery');
			die;
		}
		
		$user = user_load($uid);
		if(isset($user->uid)) {
			require_once DRUPAL_ROOT . '/' . variable_get('password_inc', 'includes/password.inc');
			$hash = user_hash_password($form['password']['#value']);
			
			db_update('users') ->fields(['pass' => $hash])->condition('uid', $user->uid, '=')->execute();
			
			drupal_set_message(t('Пароль успешно восстановлен!'));
			$account = array('uid' => $user->uid);
			user_login_submit(array(), $account);
			header('Location: /');
			die;
		}
	}
	
	function wbrecovery_form_validate($form, &$form_state) {
		$validate = true;
		$email = isset($form['email']['#value']) ? $form['email']['#value'] : null;
		if(!filter_var($email, FILTER_VALIDATE_EMAIL)) {
			form_set_error('email', t('E-mail указан не верно!'));
			$validate = false;
		}
		
		$iin = isset($form['iin']['#value']) ? $form['iin']['#value'] : null;
		if(!is_numeric($iin)) {
			form_set_error('iin', t('ИИН указан не верно!'));
			$validate = false;
		}
		
		if($validate) {
			$userExist = false;
			$user = user_load_by_mail($email);
			if(isset($user->uid) and !empty($user->uid)) {
				$userIIN = isset($user->field_iin['und'][0]['value']) ? $user->field_iin['und'][0]['value'] : null;
				if($userIIN == $iin) { $userExist = true; }
			}
			if(!$userExist) { 
				form_set_error('email', t('Пользователь с указанным E-mail и ИИН не найден!')); 
				form_set_error('iin', '');
			}
		}
	}
	
	function wbrecovery_form_submit($form, &$form_state) {
		$email = isset($form['email']['#value']) ? $form['email']['#value'] : null;
		$iin = isset($form['iin']['#value']) ? $form['iin']['#value'] : null;
		$user = user_load_by_mail($email);
		$token = wbrecovery_token();
		db_query('delete from wb_recovery where uid = :uid', [
			':uid' => isset($user->uid) ? $user->uid : null
		]);
		db_query('insert into wb_recovery(token, uid, recoveryDate) values(:token, :uid, now())', [
			':uid' => isset($user->uid) ? $user->uid : null,
			':token' => $token,
		]);
		header('Location: /account/recovery/' . $token);
		die();
	}
	
	function wbrecovery_uuid() {
		return sprintf( '%04x%04x-%04x-%04x-%04x-%04x%04x%04x',
			// 32 bits for "time_low"
			mt_rand( 0, 0xffff ), mt_rand( 0, 0xffff ),
	
			// 16 bits for "time_mid"
			mt_rand( 0, 0xffff ),
	
			// 16 bits for "time_hi_and_version",
			// four most significant bits holds version number 4
			mt_rand( 0, 0x0fff ) | 0x4000,
	
			// 16 bits, 8 bits for "clk_seq_hi_res",
			// 8 bits for "clk_seq_low",
			// two most significant bits holds zero and one for variant DCE1.1
			mt_rand( 0, 0x3fff ) | 0x8000,
	
			// 48 bits for "node"
			mt_rand( 0, 0xffff ), mt_rand( 0, 0xffff ), mt_rand( 0, 0xffff )
		);
	}
	function wbrecovery_token() {
		return wbrecovery_uuid() . '-' . wbrecovery_uuid() . '-' . wbrecovery_uuid();
	}
	
	function wbrecovery_usertabs() {
		return '
			<div class="tabs">
				<ul class="tabs primary">
					<li>
						<a href="/user/register">' . t('Регистрация') . '</a>
					</li>
					<li>
						<a href="/user">' . t('Войти') . '</a>
					</li>
					<li class="active">
						<a href="/account/recovery" class="active">' . t('Забыли пароль?') . '</a>
					</li>
				</ul>
			</div>';
	}
	
	function wbrecovery_get_token() {
		$uri = isset($_GET['q']) ? $_GET['q'] : null;
		$tmpUri = explode('?', $uri);
		$uri = isset($tmpUri[0]) ? $tmpUri[0] : null;
		$tmpUri = explode('/', $uri);
		return isset($tmpUri[2]) ? $tmpUri[2] : null;
	}
	
	/**
	 * Редиректим с реальной формы восстановления пароля
	 */
	global $user;
	if(!isset($user->uid) or empty($user->uid)) {
		$uri = isset($_GET['q']) ? $_GET['q'] : null;
		if($uri == 'user/password') {
			header('Location: /account/recovery');
			die;
		}
	}
	
	/**
	 * Перенаправляем на форму редактирования, если ИИН не заполнен
	 */
	if(isset($user->uid) and !empty($user->uid)) {
		
		$ignoreRoles = ['administrator', 'сustomer'];
		$ignore = false;
		foreach($ignoreRoles as $role) {
			if(in_array($role, $user->roles)) {
				$ignore = true;
				break;
			}
		}
		
		if(!$ignore) {
			$db = db_query("
				select 
					field_iin_value as iin 
				from field_data_field_iin 
				where bundle = 'user' and 
					  entity_id = :uid 
				limit 1", [':uid' => $user->uid])->fetchObject();
			
			$iin = isset($db->iin) ? $db->iin : null;
			if(trim($iin) == '') { $iin = null; }
			
			if(is_null($iin)) {
				$uri = isset($_GET['q']) ? $_GET['q'] : null;
				if($uri <> 'user/' . $user->uid . '/edit' and $uri <> 'user/edit') {
					drupal_set_message(t('Для того, чтобы продолжить работу с сайтом, необходимо указать ИИН! Это необходимо для восстановления доступа к сайту, в случае если пароль будет забыт или утерян.'), 'warning');
					header('Location: /user/' . $user->uid . '/edit');
					die;
				}
			}
		}
		
	}
?>