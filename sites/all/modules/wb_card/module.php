<?php
	/**
	 * Исходный код модуля
	 */
	
	function wb_card_block_info() {
		$blocks['wb_card'] = array(
			'info' => t('WB CARD'),
			'cache' => DRUPAL_CACHE_PER_ROLE,
		);
		return $blocks;
	}
	
	/**
	 * Блок с карзиной
	 */
	function wb_card_block_view($delta = '') {
		global $user;
		$uid = isset($user->uid) ? (int) $user->uid : null;
		if(empty($uid)) { $uid = null; }
		drupal_add_css(drupal_get_path('module', 'wb_card') .'/css/wb_card.css');
		$myCardUUID = wbGetCardUUID();
		
		$where = 'uuid = :uuid';
		$whereParams = [':uuid' => $myCardUUID];
		if(!is_null($uid)) {
			$where = '(uuid = :uuid or uid = :uid)';
			$whereParams[':uid'] = $uid;
		}
		
		$sql = "
			SELECT 
			  IFNULL(SUM(productCount), 0) as pCount,
			  IFNULL(SUM(productSumPrice), 0) as pPrice
			from wb_orders
			where " . $where . " and orderId is NULL";
		
		$cardData = db_query($sql, $whereParams)->fetchObject();
		$count = !is_null($cardData->pCount) ? (int) $cardData->pCount : 0;
		$price = !is_null($cardData->pPrice) ? (int) $cardData->pPrice : 0; 
		$fCount = str_replace(',', ' ', number_format($count));
		$fPrice = str_replace(',', ' ', number_format($price));

		$block['subject'] = null;
		$emptyVal = t('Пусто');
		//$cardTitleClass = !empty($count) ? null : 'wb-card-count-empty'; 
		$block['content'] = '
			<a href = "/mycard" title = "' . t('Корзина') . '" class = "wb-card-block">
				<span class = "wb-card-seperator">
					<span class = "wb-card-seperator-left">
						<span class = "wb-card-img">
							<img src = "/sites/all/modules/wb_card/img/wbCard.png" width = "40" height = "40" alt = "' . t('Корзина') . '" />
							<span class = "wb-card-count">' . $fCount . '</span>
						</span>
					</span>
					<span class = "wb-card-seperator-right">
						<span class = "block-card-title">' . t('Корзина') . '</span>
						<span class = "wb-card-price"><span class = "wb-card-value">' . $fPrice . '</span> тг.</span>
					</span>
				</span>
				
				
			</a>';
			
		return $block;
	}
	
	function wb_card_menu() {
		$items = array();
		$items['mycard'] = array(
			'title' => 'Корзина',
			'page callback' => 'wb_card_myCardPage',
			'access callback' => 'user_access',
			'access arguments' => array('access content'),
			'type' => MENU_NORMAL_ITEM,
		 );
		 
		 /*$items['mycard-add'] = array(
			'title' => null,
			'page callback' => 'myCardAddPage',
			'access callback' => 'user_access',
			'access arguments' => array('access content'),
			'type' => MENU_NORMAL_ITEM,
		 );*/
		 
		return $items;
	}
	
	function wb_card_myCardPage() {
		global $user;
		$uid = isset($user->uid) ? (int) $user->uid : null;
		if(empty($uid)) { $uid = null; }
		$myCardUUID = wbGetCardUUID();
		
		if(isset($_POST['count'])) {
			if(is_null($uid)) {
				/*drupal_set_message(t('Для оформления заказа необходимо войти на сайт или зарегистрироваться!'), 'error');
				header('Location: /mycard');
				die();*/
			}
			if(is_array($_POST['count']) and !empty($_POST['count'])) {
				foreach($_POST['count'] as $orderId => $count) {
					$sql = "
						select * from wb_orders
						where (uuid = :uuid) and orderId is NULL and id = :id";
					
					$order = db_query($sql, [':uuid' => $myCardUUID, /*':uid' => $uid,*/ ':id' => $orderId])->fetchObject();
					
					$allData = json_decode(isset($order->allData) ? $order->allData : null);
					$product = node_load(isset($order->nid) ? $order->nid : null);
					
					$price = isset($product->field_price['und'][0]['value']) ? (int) $product->field_price['und'][0]['value'] : 0;
					$count = (int) $count;
					$allSum = $price * $count;
					
					$allData->price = $price;
					$allData->count = $count;
					$allDataJson = json_encode($allData);
					
					$updParams = array(
						':productCount' => $count,
						':productPrice' => $price,
						':productSumPrice' => $allSum,
						':allData' => $allDataJson,
						//':uid' => $uid,
						//':uid1' => $uid,
						':uuid' => $myCardUUID,
						':id' => $orderId,
					);
					$sql = "
						update wb_orders set
							
							productCount = :productCount,
							productPrice = :productPrice,
							productSumPrice = :productSumPrice,
							allData = :allData
						where (uuid = :uuid) and
							  id = :id";
					db_query($sql, $updParams);
							  
				}
			}
			header('Location: /node/add/myoder');
			die();
		}
		
		drupal_add_css(drupal_get_path('module', 'wb_card') .'/css/wb_card.css');
		drupal_add_js(drupal_get_path('module', 'wb_card') .'/js/wb_card.js');
		
		$where = 'uuid = :uuid';
		$whereParams = [':uuid' => $myCardUUID];
		if(!is_null($uid)) {
			$where = '(uuid = :uuid or uid = :uid)';
			$whereParams = [':uuid' => $myCardUUID, ':uid' => $uid];
		}
		
		$sql = "
			SELECT 
			  *
			from wb_orders
			where " . $where . " and orderId is NULL";
		$dbRows = db_query($sql, $whereParams)->fetchAll();
		
		$commonSum = 0;
		$html = null;
		foreach($dbRows as $row) {
			
			$htmlRow = null;
			$product = node_load($row->nid);
			$productAttrs = json_decode($row->allData);
			
			
			if($product) {
				$subField = null;
				
				$sumPrice = (int) + $row->productSumPrice;
				$commonSum = $commonSum + $sumPrice;
				$colorView = null;
				// color
				if(!is_null($productAttrs->color->fid)) {
					$src = image_style_url('colors', $productAttrs->color->uri);
					
					$subField = '
						<span class = "wb-card-sub-field">
							( ' . t('цвет') . ': ' . htmlspecialchars($productAttrs->color->title) . ' ) 
						</span>';
					$colorView = '<img src = "' . $src . '" alt = "' . htmlspecialchars($productAttrs->color->title) . '"/>';
				}
				
				if(!is_null($productAttrs->size)) {
					$subField = '
						<span class = "wb-card-sub-field">
							( ' . t('размер') . ': ' . htmlspecialchars($productAttrs->size) . ' мм.) 
						</span>';
				}
				
				$htmlRow .= '
					<div class = "wb-card-row-title">
						<span>' . htmlspecialchars($product->title) . $subField . '</span>
						<span class = "wb-card-row-remove">' . t('Удалить') . '</span>
					</div>
					' . $colorView . '
					<div class = "wb-card-row-data">
						<label>
							Кол-во: <input type = "number" name = "count[' . $row->id . ']" value = "' . $row->productCount . '">
						</label>
						<span class = "wb-card-row-price" data-wb-product-price = "' . $row->productPrice . '">
							Итоговая сумма:
							<span class = "wb-card-row-price-value">' . $row->productSumPrice . '</span>
							тг.
						</span>
						
					</div>';
			}
			
			if(!is_null($row)) {
				$html .= '<div class = "wb-card-row" data-wb-cardrow-id = "' . $row->id . '">' . $htmlRow . '</div>';
			}
		}
		$isEmpty = is_null($html) ? 'wb-card-empty' : 'wb-card-not-empty';
		
		$isNotAuthMsg = null;
		/*
		if(is_null($uid)) {
			$isNotAuthMsg = '
				<div class = "wb-card-error wb-card-user-is-not-auth">
					Для оформления заказа необходимо <a href = "/user" title = "Войти на сайт">войти</a> на сайт или <a href = "/user/register" title = "Зарегистрироваться на сайте">зарегистрироваться</a>!
				</div>';
		}
		*/
		/**
		 * Определяем скидку
		 */
		$sales = array(
			20 => theme_get_setting('sale_20'),
			22 => theme_get_setting('sale_22'),
			25 => theme_get_setting('sale_25'),
		);
		foreach($sales as $key => $value) {
			if($value == '') {
				$sales[$key] = null;
			} else {
				$sales[$key] = (int) $value;
			}
		}
		
		$salesJson = json_encode($sales);
		$salesJson = str_replace('"', "'", $salesJson);
		
		$oldPrice = $commonSum;
		$sale = 0;
		
		$saleIsEnabledTmp = theme_get_setting('sale_enable');
		
		$saleIsEnabled = !is_null($saleIsEnabledTmp) && !empty($saleIsEnabledTmp);
		$saleIsEnabled = true;
		
		$saleClass = null;
		if($saleIsEnabled) {
			foreach($sales as $saleValue => $salePrice) {
				if(!is_null($salePrice) and trim($salePrice) <> '') {
					if($salePrice < $oldPrice) { $sale = $saleValue; }
				}			
			}
			if(!empty($sale)) {
				$saleFromSum = $sale * $oldPrice / 100;
				$saleFromSum = (int) round($saleFromSum);
				$commonSum = (int) round($commonSum - $saleFromSum);
			}
			
			if(!empty($sale)) {
				$saleClass = 'wb-card-sale-box-visible';
			}
		}
		
		return '
			' . $isNotAuthMsg . '
			<div class = "wb-card-warning">
				<ul>
					<li>' . t('Товар возврату и обмену не подлежит.') . '</li>
					<li>' . t('Все цены на товары равны ценам на текущий момент.') . '</li>
					<li>' . t('Заказ необходимо оплатить не позднее трёх дней с момента его оформления.') . '</li>
					<li>' . t('Мы собираем только оплаченные заказы. Если вы хотите оплатить в магазине, то оформлять заказ на сайте не нужно.') . '</li>
				</ul>
			</div>
			<form method = "post" class = "wb-card-form ' . $isEmpty . '" data-card-sales = "' . htmlspecialchars($salesJson) . '" data-card-sales-enabled = "' . ($saleIsEnabled ? 1 : 0) . '">
				<div class = "wb-card-empty-description">' . t('Ваша корзина пуста...') . '</div>
				<div class = "wb-card-rows">' . $html . '</div>
				<div class = "wb-card-sale-box ' . $saleClass . '">
					Сумма без скидки: <strong class = "wb-card-sale-price">' . $oldPrice . '</strong> тг. Скидка <strong class = "wb-card-sale-sale">' . $sale . '</strong> %. Сумма со скидкой: <strong class = "wb-card-sale-new-price">' . $commonSum . '</strong> тг.
				</div>
				<div style = "text-align: center;">
					<a href = "/" class = "wb-card-btn">
						' . t('Вернуться в магазин') . '
					</a>
					<button class = "wb-card-btn" type = "submit" data-noauth-title = "Оформление заказа" data-noauth-body = "Для оформления заказа необходимо войти на сайт или зарегистрироваться.">
						<strong>' . t('Оформить заказ') . '</strong>
						<span class = "wb-card-sum-btn" style = "margin-left: 5px;">
							Итого: <span class = "wb-card-sum-btn-value">' . $commonSum . '</span> тг.
						</span>
					</button>
				</div>
			</form>';
	}
	
	function wbGetCardUUID() {
		//$uuid = isset($_COOKIE['user_uuid']) ? $_COOKIE['user_uuid'] : wbGetUuid();
		//setcookie('user_uuid', $uuid, time() + (3600 * 24 * 1), "/", "." . $_SERVER['SERVER_NAME']);
		return isset($GLOBALS['USER_UUID']) ? $GLOBALS['USER_UUID'] : null;
	}
	
	function wb_card_form_alter(&$form, &$form_state, $form_id) {
		global $user;
		
		if($form_id == 'myoder_node_form') {
			drupal_set_title(t('Оформление заказа'));
			$form['actions']['submit']['#value'] = t('Оформить заказ');
			$form['actions']['submit']['#submit'][] = 'wb_card_form_node_form_submit';
			$form['#validate'][] = 'wb_card_myoder_form_validate';
			
			$userAccess = false;
			$roles = isset($user->roles) ? $user->roles : array();
			$noticeRolles = ['administrator', 'сustomer'];
			foreach($noticeRolles as $role) {
				if(in_array($role, $roles)) {
					$userAccess = true;
					break;
				}
			}
			if(!$userAccess) {
				// Проверяем, есть ли что-то в корзине
				$uid = isset($user->uid) ? (int) $user->uid : null;
				$myCardUUID = wbGetCardUUID();
				$where = 'uuid = :uuid';
				$whereParams = [':uuid' => $myCardUUID];
				if(!is_null($uid)) {
					$where = '(uuid = :uuid or uid = :uid)';
					$whereParams[':uid'] = $uid;
				}
				
				$sql = "
					SELECT 
					  count(*) as rc
					from wb_orders
					where " . $where . " and orderId is NULL";
				$dbRC = db_query($sql, $whereParams)->fetchObject();
				$rc = isset($dbRC->rc) ? (int) $dbRC->rc : 0;
				if(!in_array('сustomer', $user->roles) and !in_array('administrator', $user->roles) ) {
					if(empty($rc)) {
						drupal_set_message(t('Ваша корзина пуста! Оформление заказа не возможно!'), 'error');
						
						$log = array(
							'user' => $user,
							'uuid' => $myCardUUID,
						);
						$file = '
							<?php 
								return ' . var_export($log, true) . ';
								
								/*
								 * Date-time stamp: ' . time() . '
								 */';
						$filename = $_SERVER['DOCUMENT_ROOT'] . '/log_alter.php';
						file_put_contents($filename, $file);
						header('Location: /');
						die();
					}
				}
				
			}
		}
	}
	
	function wb_card_node_insert($node) {
		global $user;
		if($node->type == 'myoder') {
			$uuid = wbGetCardUUID();
			$uid = isset($user->uid) ? (int) $user->uid : null;
			if(!is_null($uid)) {
				$where = '(uuid = :uuid or uid = :uid)';
				$whereParams[':uid'] = $uid;
			}
			$sql = "
				update wb_orders set
					uid = :uid,
					orderId = :orderId
				where " . $where . " and orderId is null";
			db_query($sql, [':orderId' => $node->nid, ':uid' => $user->uid, ':uuid' => $uuid]);	
					
			db_query("insert into wb_notice(nid, noteDate, isNew) values(:nid, now(), 1)", [':nid' => $node->nid]);
		}
	}
	
	function wb_card_node_view($node, $view_mode, $langcode) {
		global $user;
		$noticeAccess = false;
		$roles = isset($user->roles) ? $user->roles : array();
		$noticeRolles = ['administrator', 'сustomer'];
		foreach($noticeRolles as $role) {
			if(in_array($role, $roles)) {
				$noticeAccess = true;
				break;
			}
		}
		if($node->type == 'myoder') {
			if($noticeAccess) {
				db_query("delete from wb_notice where nid = :nid", [':nid' => $node->nid]);
			}
		}
	}
	
	function wb_card_myoder_form_validate($form, &$form_state) {
		global $user;
		$uid = isset($user->uid) ? (int) $user->uid : null;
		$myCardUUID = wbGetCardUUID();
		$where = 'uuid = :uuid';
		$whereParams = [':uuid' => $myCardUUID];
		if(!is_null($uid)) {
			$where = '(uuid = :uuid or uid = :uid)';
			$whereParams[':uid'] = $uid;
		}
		$sql = "
			SELECT 
			  count(*) as rc
			from wb_orders
			where " . $where . " and orderId is NULL";
		$dbRC = db_query($sql, $whereParams)->fetchObject();
		$rc = isset($dbRC->rc) ? (int) $dbRC->rc : 0;

		if(!in_array('сustomer', $user->roles) and !in_array('administrator', $user->roles) ) {
			if(empty($rc)) {
				form_set_error('body', t('Ваша корзина пуста! Оформление заказа не возможно!'));
				$log = array(
					'user' => $user,
					'uuid' => $myCardUUID,
				);
				$file = '
					<?php 
						return ' . var_export($log, true) . ';
						
						/*
						 * Date-time stamp: ' . time() . '
						 */';
				$filename = $_SERVER['DOCUMENT_ROOT'] . '/log_validate.php';
				file_put_contents($filename, $file);
			}
		}
		
		
		$body = isset($form_state['values']['body']['und'][0]['value']) ? $form_state['values']['body']['und'][0]['value'] : null;
		$stripBody = strip_tags($body);
		if($body <> $stripBody) {
			form_set_error('body', t('Не допустимо вставлять HTML тэги в данное поле!'));
		}
	}
	
	function wb_card_form_node_form_submit() {
		if (!empty($_SESSION['messages']['status'])) {
			unset($_SESSION['messages']['status']);
			drupal_set_message(t('СПАСИБО! Ваша заявка принята! В ближайшее время наш менеджер свяжется с Вами,чтобы обсудить детали.'));
		}
	}