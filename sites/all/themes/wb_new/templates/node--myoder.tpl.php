<?php
	global $user;
	$uuid = isset($GLOBALS['USER_UUID']) ? $GLOBALS['USER_UUID'] : null;
	$sql = "select count(*) as rc from wb_orders where orderId = :nid and uuid = :uuid";
	$checker = db_query($sql, [':nid' => $node->nid, ':uuid' => $uuid])->fetchObject();
	$rc = isset($checker->rc) ? (int) $checker->rc : 0;
	$uid = isset($user->uid) ? (int) $user->uid : 0;
?>
<?php if(!empty($rc) or !empty($uid)): ?>
<div id="node-<?php print $node->nid; ?>" class="<?php print $classes; ?> clearfix"<?php print $attributes; ?>>

  <?php print render($title_prefix); ?>
  <?php if (!$page): ?>
    <h2<?php print $title_attributes; ?>>
      <a href="<?php print $node_url; ?>"><?php print $title; ?></a>
    </h2>
  <?php endif; ?>
  <?php print render($title_suffix); ?>

  <?php if ($display_submitted): ?>
    <div class="meta submitted">
      <?php print $user_picture; ?>
      <?php print $submitted; ?>
    </div>
  <?php endif; ?>
	<div class="content clearfix"<?php print $content_attributes; ?>>
        <div class="myorder-page-fio">
            <span class="myorder-page-label"><?= t('Номер заказа') ?>:</span>
            <span class="myorder-page-value"><?= $node->nid ?></span>
        </div>
        <?php 
			$status = null;
			if(isset($node->field_order_status['und'][0]['taxonomy_term']->name)) {
				$status = $node->field_order_status['und'][0]['taxonomy_term']->name;
			}
		?>
        <?php if(!is_null($status)): ?>
        	<div class="myorder-page-fio" style="margin-top: 10px;">
                <span class="myorder-page-label"><?= t('Статус заказа') ?>:</span>
                <span class="myorder-page-value"><?= $status ?></span>
            </div>
        <?php endif; ?>
        <div class="myorder-page-fio" style="margin-top: 10px;">
            <span class="myorder-page-label"><?= t('Фамилия') ?>:</span>
            <span class="myorder-page-value"><?= $title ?></span>
        </div>
		<?php print render($content); ?>
	</div>
    
    <div class="myorder-page-products">
    	<h4><?= t('Товары в корзине') ?></h4>
        
        <?php
        	$sql = "select * from wb_orders where orderId = :nid";
			$products = db_query($sql, [':nid' => $node->nid])->fetchAll();
			$fullSum = 0;
			if($products <> false and is_array($products) and !empty($products)) {
				
				foreach($products as $product) {
					$allData = json_decode($product->allData);
					?>
                        <div class="myorder-page-product-row">
                    <?php
					?>
						<div class="myorder-page-product-item">
                            <span class="myorder-page-product-row-title"><?= t('Наименование товара') ?>:</span>
                            <span class="myorder-page-product-row-value"><?= $allData->title ?></span>
                        </div>
                        <?php if(!is_null($product->fid)): ?>
                        	<div class="myorder-page-product-item">
                                <span class="myorder-page-product-row-title"><?= t('Цвет') ?>:</span>
                                <span class="myorder-page-product-row-value">
									<?= $allData->color->title ?>
                                </span>
                            </div>
                            <div class = "myorder-page-product-item-color">
                            	 <?php $src = image_style_url('colors', $allData->color->uri); ?>
                                 <img src="<?= $src ?>" alt="<?= $allData->color->title ?>" />
                            </div>
                        <?php endif; ?>
                        <div class="myorder-page-product-item">
                            <span class="myorder-page-product-row-title"><?= t('Количество') ?>:</span>
                            <span class="myorder-page-product-row-value"><?= $product->productCount ?></span>
                        </div>
                        <div class="myorder-page-product-item">
                            <span class="myorder-page-product-row-title"><?= t('Цена за товар') ?>:</span>
                            <span class="myorder-page-product-row-value">
                                <?= str_replace(',', ' ', number_format($product->productPrice)) ?> тг.
                             </span>
                        </div>
                        <div class="myorder-page-product-item">
                            <span class="myorder-page-product-row-title"><?= t('Итоговая сумма') ?>:</span>
                            <span class="myorder-page-product-row-value">
                            	<?php $fullSum += (int) $product->productSumPrice; ?>
                                <?= str_replace(',', ' ', number_format($product->productSumPrice)) ?> тг.
                             </span>
                        </div>
                        <?php if(!is_null($product->size)): ?>
                        	<div class="myorder-page-product-item">
                                <span class="myorder-page-product-row-title"><?= t('Размер') ?>:</span>
                                <span class="myorder-page-product-row-value"><?= $product->size ?> мм.</span>
                            </div>
                        <?php endif; ?>
                        
					<?php
					?>
                        </div>
                    <?php
				}
				
			}
		?>
    </div>
    <div class="myorder-price-report">
		<?php
			$oldPrice = $fullSum;
        	$newPrice = $fullSum;
        	$sales = array(
				20 => theme_get_setting('sale_20'),
				22 => theme_get_setting('sale_22'),
				25 => theme_get_setting('sale_25'),
			);
			$sale = 0;
			foreach($sales as $key => $value) {
				$salesPrice = 0;
				if($value <> '') { $salesPrice = (int) $value; }
				if($salesPrice < $fullSum) { $sale = (int) $key; }
			}
			/*if(!empty($sale)) {
				$saleFromSum = $sale * $oldPrice / 100;
				$saleFromSum = (int) round($saleFromSum);
				$newPrice = (int) round($oldPrice - $saleFromSum);
			}*/
		?> 
        
        <?php if(true): ?>
			Итоговая сумма: <strong><?= str_replace(',', ' ', number_format($newPrice)) ?></strong> тг.
        <?php else: ?>
        	Итого без скидки: <strong><?= str_replace(',', ' ', number_format($oldPrice)) ?></strong> тг.
            Скидка: <strong><?= $sale ?></strong> %.
            Итого со скидкой: <strong><?= str_replace(',', ' ', number_format($newPrice)) ?></strong> тг.
        <?php endif; ?>
    </div>
</div>
<?php else: ?>
	<h1 class = "title" style="display: block;">
      Страница закрыта для просмотра
    </h1>
	У вас нет доуступа к этой странице сайта!
<?php endif; ?>