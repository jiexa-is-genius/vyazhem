<?php
	$tid = isset($node->field_category['und'][0]['tid']) ? (int) $node->field_category['und'][0]['tid'] : 0;
	$term = taxonomy_term_load($tid);
	
	$product = array(
		'type' => isset($term->field_inc_field['und'][0]['value']) ? $term->field_inc_field['und'][0]['value'] : null,
		'title' => isset($node->title) ? htmlspecialchars($node->title) : null,
		'mainImage' => null,
		'price' => isset($node->field_price['und'][0]['value']) ? (int) $node->field_price['und'][0]['value'] : null,
		'oldPrice' => isset($node->field_oldprice['und'][0]['value']) ? (int) $node->field_oldprice['und'][0]['value'] : null,
		'body' => isset($node->body['und'][0]['value']) ? trim($node->body['und'][0]['value']) : null,
		'composition' => isset($node->field_composition['und'][0]['value']) ? $node->field_composition['und'][0]['value'] : null,
		'colors' => array(),
		'sizes' => array(),
		'isLeader' => false,
		'isSellOut' => false,
		'isNew' => false,
		'recommend' => false,
		'isUpdateColors' => false,
		'colorsCount' => 0,
	);
	
	if(trim($product['composition']) == '') { $product['composition'] = null; }
	
	if(isset($node->field_leader['und'][0]['value']) and $node->field_leader['und'][0]['value'] == '1') {
		$product['isLeader'] = true;
	}
	if(isset($node->field_sellout['und'][0]['value']) and $node->field_sellout['und'][0]['value'] == '1') {
		$product['isSellOut'] = true;
	}
	if(isset($node->field_isnew['und'][0]['value']) and $node->field_isnew['und'][0]['value'] == '1') {
		$product['isNew'] = true;
	}
	if(isset($node->field_recommend['und'][0]['value']) and $node->field_recommend['und'][0]['value'] == '1') {
		$product['recommend'] = true;
	}
		
	if(isset($node->field_updated_colors['und'][0]['value']) and $node->field_updated_colors['und'][0]['value'] == '1') {
		$product['isUpdateColors'] = true;
	}
	
	if(isset($node->field_image['und'][0]['uri'])) {
		$product['mainImage'] = image_style_url('product_main', $node->field_image['und'][0]['uri']);
	}
	if(isset($node->field_colors['und']) and is_array($node->field_colors['und']) and !empty($node->field_colors['und'])) {
		foreach($node->field_colors['und'] as $color) {
			$isExist = true;
			if($color['title'] <> '1') { $isExist = false; } else { $product['colorsCount']++; }
			$product['colors'][] = array(
				'fid' => isset($color['fid']) ? $color['fid'] : null,
				'min' => image_style_url('colors', $color['uri']),
				//'max' => image_style_url('colors_big', $color['uri']),
				'title' => isset($color['alt']) ? $color['alt'] : null,
				'isExist' => $isExist,
			);
		}
	}
	if(isset($node->field_size['und']) and is_array($node->field_size['und']) and !empty($node->field_size['und'])) {
		foreach($node->field_size['und'] as $size) {
			$product['sizes'][] = $size['value'];
		}
	}
	$wbProductType = !is_null($product['type']) ? 'wb-product-type-' . $product['type'] : null;
	
?>
<div id="node-<?php print $node->nid; ?>" data-product-id = "<?php print $node->nid; ?>" class="<?php print $classes; ?> clearfix <?= $wbProductType ?>"<?php print $attributes; ?>>
  
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
	<div class="wb-product-page">
		<div class="wb-product-tbl">
        	<div class="wb-product-tbl-left">
            	<?php if(!is_null($product['mainImage'])): ?>
                    <div class="wb-product-image">
                        <img src="<?= $product['mainImage'] ?>" width="350" height="350" />
                        
                        <div class="product-page-lables">
							<?php if($product['isLeader']): ?>
                                <span class="product-page-leader"><?= t('Лидер продаж') ?></span>
                            <?php endif; ?>
                            <?php if($product['isNew']): ?>
                                <span class="product-page-isnew"><?= t('Новинка') ?></span>
                            <?php endif; ?>
                            <?php if($product['isSellOut']): ?>
                                <span class="product-page-sellout"><?= t('Распродажа') ?></span>
                            <?php endif; ?>
                            <?php if($product['recommend']): ?>
                                <span class="product-page-recommend"><?= t('Советуем') ?></span>
                            <?php endif; ?>
                        </div>
                        
                        <?php if(!empty($product['colorsCount'])): ?>
                            <div class="wb-product-page-counter">
                                <?php if($product['isUpdateColors']): ?>
                                    <div class="wb-product-page-counter-updated">
                                        <span><?= t('Обновлённые цвета') ?></span>
                                    </div>
                                <?php endif; ?>
                                <div class="wb-product-page-counter-count">
                                	<span><?= t('Цветов в наличии') ?>: <strong><?= $product['colorsCount'] ?></strong></span>
                                </div>
                            </div>
                        <?php endif; ?>
                    </div>
                <?php endif; ?>
            </div>
            <div class="wb-product-tbl-right">
                <?php if(!is_null($product['price'])): ?>
                    <div class="wb-product-price" data-price-raw = "<?= $product['price'] ?>">
                        <span class="wb-product-value"><?= str_replace(',', ' ', number_format($product['price'])) ?> тг.</span>
                        <?php if(!is_null($product['oldPrice'])): ?>
                            <span class="wb-product-old-price-value">
                                <?= str_replace(',', ' ', number_format($product['oldPrice'])) ?> тг.
                            </span>
                        <?php endif; ?>
                    </div>
                <?php endif; ?>
                <div class="wb-product-card">
                    <div class="product-page-product-count">
                    	<div class="product-page-product-count-minus">
                            <button type="button"> - </button>
                        </div>
                        <div class="product-page-product-count-value">
                            <input type="text" value="1" min = "1" max = "1000" id="card-products-count" readonly="readonly" placeholder = "Кол-во" />
                        </div>
                        <div class="product-page-product-count-plus">
                            <button type="button"> + </button>
                        </div>	
                    </div>
                    <button type="button" class="wb-products-add-to-card-btn">
                        <?= t('В корзину') ?>
                    </button>
                </div>
                <?php if($product['type'] == 'size' and !empty($product['sizes'])): ?>
                    <div class="wb-product-sizes">
                        <h3><?= t('Варианты размеров') ?></h3>
                        <div class="wb-product-size-rows">
                            <select id="wb-product-size-lines">
                            	<option value="" selected="selected"><?= t('- Размер не выбран -') ?></option>
								<?php foreach($product['sizes'] as $size): ?>
                                	<option value="<?= $size ?>"><?= $size ?> мм.</option>
                                    <!--div class="wb-product-size-row">
                                        <label>
                                            <input type="radio" name="product_size" value="<?= $size ?>" />
                                            <?= $size ?> мм.
                                        </label>
                                    </div-->
                                <?php endforeach; ?>
                            </select>
                        </div>
                    </div>
                <?php endif; ?>
                
                <?php if(!is_null($product['composition'])): ?>
                	<div class="wb-product-composition">
                        <h3><?= t('Состав') ?></h3>
                        <div class="wb-product-composition-content"><?= $product['composition'] ?></div>
                    </div>
				<?php endif; ?>
                
                <?php if(!is_null($product['body'])): ?>
                    <div class="wb-product-body">
                        <h3><?= t('Описание товара') ?></h3>
                        <?= $product['body'] ?>
                    </div>
                <?php endif; ?>
                
            </div>
        </div>
        
        
        
		<?php if($product['type'] == 'color' and !empty($product['colors'])): ?>
        	<div class="wb-product-colors">
            	<h3><?= t('Варианты цвета') ?></h3>
                <div class="wb-product-color-rows">
                	<?php foreach($product['colors'] as $color): ?>
          				<div class="wb-product-color-row" data-color-id = "<?= $color['fid'] ?>">
                        	<div class="wb-product-color-row-image">
                            	<img src="<?= $color['min'] ?>" style="width: 150px; height: auto;" alt="<?= htmlspecialchars($color['title']) ?>" />
                                <?php if(isset($color['min'])): ?>
                                    <div class="wb-product-color-row-max-image">
                                        <img src="<?= $color['min'] ?>" style="width: 300px; height: auto;" alt="<?= htmlspecialchars($color['title']) ?>" />
                                    </div>
                                <?php endif; ?>
                            </div>
                            <div class="wb-product-color-row-title">
                            	<?= htmlspecialchars($color['title']) ?>
                            </div>
                            <div class="wb-product-color-row-is-exist">
								<?php if($color['isExist']): ?>
                                	<div class="wb-product-color-row-is-exist-true"><?= t('В наличии')?></div>
								<?php else: ?>
                                	<div class="wb-product-color-row-is-exist-false"><?= t('Нет наличии')?></div>
                                <?php endif; ?>
                            </div>
                            <div class="wb-product-color-active"></div>
                        </div>          	
                    <?php endforeach?>
                </div>
            </div>
        <?php endif; ?>
    </div>
  </div>
</div>