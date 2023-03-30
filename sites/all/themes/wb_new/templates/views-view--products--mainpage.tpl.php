<style>
	.frontapge-product-tabs { margin: 20px 0; padding: 0; text-align: center; display: table; width: 100%; }
	.frontapge-product-tabs li { margin: 0; padding: 0; list-style: none; display: inline-block; margin: 0 10px; }
	.frontapge-product-tabs li span { 
		padding: 10px; 
		text-transform: uppercase; 
		cursor: pointer; 
		border: 1px solid #FFFFFF;
		display: inline-block;
	}
	.frontapge-product-tabs li.active span, .frontapge-product-tabs li:hover span {
		border: 1px solid #f3718e;
		border-radius: 3px;
			-webkit-border-radius: 3px;
			-moz-border-radius: 3px;
		color: #f36786;
	}
	.front-product-tab-box { margin: 0; padding: 0; margin-top: 35px; }
	.front-product-tab-box li { margin: 0; padding: 0; list-style: none; display: none; }
	.front-product-tab-box li.active { display: block; }
	
	@media (max-width: 770px) {
		.frontapge-product-tabs li { display: block; margin-top: 10px; }
		.frontapge-product-tabs li:first-child { margin-top: 0; }
	}
</style>

<script>
	(function($) {
		$(document).ready(function() {
			$('.frontapge-product-tabs li').click(function() {
				$('.frontapge-product-tabs li').removeClass('active');
				$(this).addClass('active');
				
				$('.front-product-tab-box li').removeClass('active');
				$('.front-product-tab-box li.' + $(this).attr('data-product-tab-class')).addClass('active');
			});
		});
	})(jQuery);
</script>

<ul class="frontapge-product-tabs">
	<li class="active" data-product-tab-class = "front-tab-leader">
    	<span><?= t('Лидеры продаж') ?></span>
    </li>
    <li data-product-tab-class = "front-tab-isnew"><span><?= t('Новинки') ?></span></li>
    <li data-product-tab-class = "front-tab-sellout"><span><?= t('Распродажа') ?></span></li>
    <li data-product-tab-class = "front-tab-recommend"><span><?= t('Советуем') ?></span></li>
</ul>

<div class="<?php print $classes; ?>">
  <?php print render($title_prefix); ?>
  <?php if ($title): ?>
    <?php print $title; ?>
  <?php endif; ?>
  <?php print render($title_suffix); ?>
  <?php if ($header): ?>
    <div class="view-header">
      <?php print $header; ?>
    </div>
  <?php endif; ?>

  <?php if ($exposed): ?>
    <div class="view-filters">
      <?php print $exposed; ?>
    </div>
  <?php endif; ?>

  <?php if ($attachment_before): ?>
    <div class="attachment attachment-before">
      <?php print $attachment_before; ?>
    </div>
  <?php endif; ?>

	<ul class="front-product-tab-box">
    	<li class="front-tab-leader active">
			<?php if ($rows): ?>
            	<div class="view-content">
            		<?php print $rows; ?>
            	</div>
            <?php elseif ($empty): ?>
            	<div class="view-empty">
            		<?php print $empty; ?>
            	</div>
            <?php endif; ?>
        </li>
        <li class="front-tab-isnew">
        	<?= views_embed_view('products', 'block_isnew'); ?>
        </li>
        <li class="front-tab-sellout">
        	<?= views_embed_view('products', 'block_sellout'); ?>
        </li>
        <li class="front-tab-recommend">
        	<?= views_embed_view('products', 'block_recommend'); ?>
        </li>
    </ul>
  
  <?php if ($pager): ?>
    <?php print $pager; ?>
  <?php endif; ?>

  <?php if ($attachment_after): ?>
    <div class="attachment attachment-after">
      <?php print $attachment_after; ?>
    </div>
  <?php endif; ?>

  <?php if ($more): ?>
    <?php print $more; ?>
  <?php endif; ?>

  <?php if ($footer): ?>
    <div class="view-footer">
      <?php print $footer; ?>
    </div>
  <?php endif; ?>

  <?php if ($feed_icon): ?>
    <div class="feed-icon">
      <?php print $feed_icon; ?>
    </div>
  <?php endif; ?>

</div><?php /* class view */ ?>