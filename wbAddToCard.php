<?php
	
	
	chdir($_SERVER['DOCUMENT_ROOT']);
	define('DRUPAL_ROOT', $_SERVER['DOCUMENT_ROOT']);
	require_once './includes/bootstrap.inc';
	drupal_bootstrap(DRUPAL_BOOTSTRAP_FULL);
	
	global $user;
	$uid = isset($user->uid) ? (int) $user->uid : null;
	
	if(empty($uid)) { $uid = null; }
	/**
	 * Проверяем термин на категориях
	 */
	$uuid = isset($_COOKIE['user_uuid']) ? $_COOKIE['user_uuid'] : null;
	
	$response = array(
		'error' => null,
		'card' => array(
			'count' => null,
			'price' => null,
		),
	);
	// Получаем пользователя
	
	if(is_null($uuid)) {
		$response['error'] = t('Пользователь не идентифицирован! Проверьте, поддерживает ли Ваш браузер COOKIE.');
	}
	
	// Получаем кол-во
	$count = null;
	if(is_null($response['error'])) {
		if(isset($_POST['count'])) { $count = (int) $_POST['count']; }
		if(isset($_GET['count'])) { $count = (int) $_GET['count']; }
		if($count < 1) {
			if(!$product) { $response['error'] = t('Не указано количество!'); }
		}
	}
	
	// Получаем товар
	$product = null;
	if(is_null($response['error'])) {
		if(isset($_POST['product'])) { $product = node_load($_POST['product']); }
		if(isset($_GET['product'])) { $product = node_load($_GET['product']); }
		if(!$product) { $response['error'] = t('Товар не найден!'); }
	}
	
	// Получаем категорию
	$category = null;
	if(is_null($response['error'])) {
		$tid = isset($product->field_category['und'][0]['tid']) ? (int) $product->field_category['und'][0]['tid'] : null;
		$category = taxonomy_term_load($tid);
		if(!$category) { $response['error'] = t('Категория не найдена!'); }
	}
	
	// Смотрим, нужны ли дополнительные поля
	$otherFields = null;
	if(is_null($response['error'])) {
		$otherFields = isset($category->field_inc_field['und'][0]['value']) ? $category->field_inc_field['und'][0]['value'] : null;
	}
	
	// Если нужен размер
	$size = null;
	if(is_null($response['error']) and $otherFields == 'size') {
		$sizes = array();
		if(isset($product->field_size['und']) and is_array($product->field_size['und']) and !empty($product->field_size['und'])) {
			foreach($product->field_size['und'] as $s) {
				$sizes[] = $s['value'];	
			}
		}
		if(isset($_POST['size'])) { $size = $_POST['size']; }
		if(isset($_GET['size'])) { $size = $_GET['size']; }
		if(!in_array($size, $sizes)) {
			$response['error'] = t('Не указан размер товара!');
			$size = null;
		}
	}
	
	// Если нужен цвет
	$color = null;
	if(is_null($response['error']) and $otherFields == 'color') {
		$fid = null;
		if(isset($_POST['color'])) { $fid = $_POST['color']; }
		if(isset($_GET['color'])) { $fid = $_GET['color']; }

		if(isset($product->field_colors['und']) and is_array($product->field_colors['und']) and !empty($product->field_colors['und'])) {
			foreach($product->field_colors['und'] as $c) {
				if($c['fid'] == $fid) {
					$color = $c;
					break;
				}
			}
		}
		$isActive = isset($color['title']) ? (int) $color['title'] : 0;
		if(!isset($color['alt']) or empty($isActive)) {
			$response['error'] = t('Не указан цвет товара!');
			$color = null;
		}
	}
	
	/**
	 * Тут прошли все логические контроли и начинаем собирать всё в кучу
	 */
	$orderData = array(
		'uuid' => $uuid,
		'uid' => $uid,
		'nid' => (int) $product->nid,
		'title' => $product->title,
		'price' => isset($product->field_price['und'][0]['value']) ? (int) $product->field_price['und'][0]['value'] : null,
		'count' => $count,
		'size' => $size,
		'color' => array(
			'fid' => null,
			'title' => null,
			'uri' => null,
		),
	);
	if(!is_null($color)) {
		$orderData['color'] = array(
			'fid' => $color['fid'],
			'title' => $color['alt'],
			'uri' => $color['uri'],
		);
	}
	
	/**
	 * Смотрим, был ли такой товар в корзине перед добавлением
	 */
	$query = "
		WHERE `orderId` is NULL and 
			  `uuid` = :uuid and 
			  `nid` = :nid";
	$params = array(
		':uuid' => $orderData['uuid'],
		':nid' => $orderData['nid'],
	);
	if(!is_null($uid)) {
		$query = "
			WHERE `orderId` is NULL and 
				  (`uuid` = :uuid or uid = :uid) and 
				  `nid` = :nid";
		$params = array(
			':uuid' => $orderData['uuid'],
			':uid' => $uid,
			':nid' => $orderData['nid'],
		);
	}
	
	if(!is_null($orderData['color']['fid'])) {
		$query .= ' and `fid` = :fid';
		$params[':fid'] = $orderData['color']['fid'];
	}
	
	if(!is_null($size)) {
		$query .= ' and `size` = :size';
		$params[':size'] = (string) $orderData['size'];
	}
	
	$sql= "SELECT * from wb_orders " . $query;
	$dbRow = db_query($sql, $params)->fetchObject();
	if(isset($dbRow->id)) {
		// Значит такой товар уже ложили в корзину
		$orderData['count'] = $orderData['count'] + $dbRow->productCount;
		$sql= "delete from wb_orders " . $query;
		$dbRow = db_query($sql, $params);
	}
	
	/**
	 * Добавляем товары в корзину
	 */
	$sql = "
		INSERT INTO wb_orders(uuid, uid, nid, fid, size, productCount, productPrice, productSumPrice, allData)
		VALUES(:uuid, :uid, :nid, :fid, :size, :productCount, :productPrice, :productSumPrice, :allData)";
	$sumPrice = $orderData['count'] * $orderData['price'];
	
	db_query($sql, array(
		':uuid' => $orderData['uuid'], 
		':uid' => $uid, 
		':nid' => $orderData['nid'], 
		':fid' => $orderData['color']['fid'], 
		':size' => $orderData['size'], 
		':productCount' => $orderData['count'], 
		':productPrice' => $orderData['price'], 
		':productSumPrice' => $sumPrice, 
		':allData' => json_encode($orderData),
	));
	
	
	
	$sql = "
		SELECT 
		  IFNULL(SUM(productCount), 0) as pCount,
		  IFNULL(SUM(productSumPrice), 0) as pPrice
		from wb_orders
		where uuid = :uuid and orderId is NULL";
	
	$cardData = db_query($sql, [':uuid' => $uuid])->fetchObject();
	$count = !is_null($cardData->pCount) ? (int) $cardData->pCount : 0;
	$price = !is_null($cardData->pPrice) ? (int) $cardData->pPrice : 0; 
	
	$response['card'] = array(
		'count' => str_replace(',', ' ', number_format($count)),
		'price' => str_replace(',', ' ', number_format($price)),
	);
	
	header('Content-Type: application/json');
	echo json_encode($response);
	die();