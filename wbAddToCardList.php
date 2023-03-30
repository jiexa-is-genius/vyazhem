<?php
	/**
	 * Проверяем термин на категориях
	 */
	chdir($_SERVER['DOCUMENT_ROOT']);
	define('DRUPAL_ROOT', $_SERVER['DOCUMENT_ROOT']);
	require_once './includes/bootstrap.inc';
	drupal_bootstrap(DRUPAL_BOOTSTRAP_FULL);
	//header('Content-Type: text/html; charset=utf-8');
	
	$nid = isset($_GET['product']) ? (int) $_GET['product'] : 0;
	$product = node_load($nid);
?>

<?php if(!$product): ?>
	<div class="wb-card-list-error"><?= t('Товар не был найден!'); ?></div>
    <?php die(); ?>
<?php endif; ?>

<?php 
	// Получаем категорию
	$category = null;
	if(is_null($response['error'])) {
		$tid = isset($product->field_category['und'][0]['tid']) ? (int) $product->field_category['und'][0]['tid'] : null;
		$category = taxonomy_term_load($tid);
	}
?>
<?php if(!$category): ?>
	<div class="wb-card-list-error"><?= t('Категория товара не определена!'); ?></div>
    <?php die(); ?>
<?php endif; ?>
<?php
	// Смотрим, нужны ли дополнительные поля
	$otherFields = null;
	if(is_null($response['error'])) {
		$otherFields = isset($category->field_inc_field['und'][0]['value']) ? $category->field_inc_field['und'][0]['value'] : null;
	}
?>
<div class="wb-list-card-product" data-list-card-product = "<?= $nid ?>">
	<div class="wb-list-card-row">
        <input type="number" id="wb-list-card-count" min = "1" placeholder = "<?= t('Укажите кол-во') ?>" />
    </div>
    <?php 
        if($otherFields == 'size') {
            $options = '<option value = "">' . t('Варианты размеров') . '</option>';
            if(isset($product->field_size['und']) and is_array($product->field_size['und']) and !empty($product->field_size['und'])) {
                foreach($product->field_size['und'] as $size) {
                    $options .= '<option value = "' . $size['value'] . '">' . $size['value'] . ' мм.</option>';
                }
            }
            ?>
                <div class="wb-list-card-row">
                    <select id="wb-list-card-size"><?= $options ?></select>
                </div>
            <?php
        }
    ?>
    
    <?php 
        if($otherFields == 'color') {
            $colors = array();
            if(isset($product->field_colors['und']) and is_array($product->field_colors['und']) and !empty($product->field_colors['und'])) {
                foreach($product->field_colors['und'] as $color) {
                    if($color['title'] == '1') {
                        $colors[] = array(
                            'fid' => isset($color['fid']) ? $color['fid'] : null,
                            'min' => image_style_url('colors', $color['uri']),
                            'title' => isset($color['alt']) ? $color['alt'] : null,
                        );
                    }
                }
            }
            ?>
                <div class="wb-list-card-row">
                    <h4><?= t('Варианты цвета') ?></h4>
                    <?php foreach($colors as $color): ?>
                    <div class="wb-list-card-color" data-list-card-color-id = "<?= $color['fid'] ?>">
                        <img src="<?= $color['min'] ?>" />
                        <span class="wb-list-card-color-label"><?= $color['title'] ?></span>    
                        <span class="wb-list-card-color-selector"></span>              
                    </div>
                    <?php endforeach; ?>
                </div>
            <?php
        }
    ?>
</div>
<?php die();