<?php
	$isParent = false;
	$tid = isset($row->tid) ? $row->tid : null;
	//var_dump($row);
	/*if(isset($row->taxonomy_term_data_taxonomy_term_hierarchy_tid)) {
		$isParent = is_null($row->taxonomy_term_data_taxonomy_term_hierarchy_tid) ? true : false;
	}*/
	$uri = isset($_GET['q']) ? $_GET['q'] : null;
	$isActive = null;
	if($uri == 'category/' . $tid) {
		$isActive = 'active';
	}
?>
<?php if($isParent): ?>
	<span class="wb-categories-tree-parent"><?= $output ?></span>
<?php else: ?>
	<a href="/category/<?= $tid ?>" title="<?= $output ?>" class="<?= $isActive ?>">
		<?= $output ?>
	</a>
<?php endif; ?>