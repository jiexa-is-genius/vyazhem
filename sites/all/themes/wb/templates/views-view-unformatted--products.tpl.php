<?php if (!empty($title)): ?>
	<h3><?php print $title; ?></h3>
<?php endif; ?>
<?php foreach ($rows as $id => $row): ?>
	<div<?php if ($classes_array[$id]): ?> class="<?php print $classes_array[$id]; ?>"<?php endif; ?>>
		<?php print $row; ?>
        
		<div class="wb-products-add-to-card">
        	<?php $nid = isset($view->result[$id]->nid) ? $view->result[$id]->nid : null; ?>
            <span class="wb-products-add-to-card-btn" data-product-id = "<?= $nid ?>">
            	<?= t('В корзину') ?>
            </span>
        </div>
	</div>
<?php endforeach; ?>