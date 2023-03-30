<?php
 	$sql = "SELECT SUM(productSumPrice) as sumPrice FROM wb_orders WHERE orderId = :orderId";
	$dbRow = db_query($sql, [':orderId' => $row->nid])->fetchObject();
	$price = isset($dbRow->sumPrice) ? (int) $dbRow->sumPrice : null;
	
	$sale = 0;
	$sales = array(
		20 => theme_get_setting('sale_20'),
		22 => theme_get_setting('sale_22'),
		25 => theme_get_setting('sale_25'),
	);
	foreach($sales as $saleValue => $value) {
		$salePrice = 0;
		if($value <> '') { $salePrice = (int) $value; }
		if(!empty($salePrice)) {
			if($price > $salePrice) { $sale = $saleValue; }
		}
	}
	
	$oldPrice = 0;
	$newPrice = 0;
	if(!empty($sale)) {
		$oldPrice = $price;
		$saleValie = $sale * $oldPrice / 100;
		$saleValie = (int) round($saleValie);
		$newPrice = $oldPrice - $saleValie;
		$newPrice = (int) round($newPrice);
	}
?>
<?php if(empty($sale)): ?>
	<?= $output; ?>
<?php else: ?>
	<div class="price-and-sale-box">
    	<div class="price-and-sale-box-row">
        	<div class="price-and-sale-box-label"><?= t('Цена без скидки') ?>:</div>
            <div class="price-and-sale-box-value"><?= str_replace(',', ' ', number_format($oldPrice)) ?> тг.</div>
        </div>
        <div class="price-and-sale-box-row">
        	<div class="price-and-sale-box-label"><?= t('Скидка') ?>:</div>
            <div class="price-and-sale-box-value"><?= $sale ?> %</div>
        </div>
        <div class="price-and-sale-box-row">
        	<div class="price-and-sale-box-label"><?= t('Цена со скидкой') ?>:</div>
            <div class="price-and-sale-box-value"><?= str_replace(',', ' ', number_format($newPrice)) ?> тг.</div>
        </div>
    </div>
<?php endif; ?>