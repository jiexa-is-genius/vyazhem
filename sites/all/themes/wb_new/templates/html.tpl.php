<!DOCTYPE html>
<html xml:lang="ru-ru" lang="ru-ru" dir="ltr">
<head>
    <!-- Yandex.Metrika counter -->
	<script type="text/javascript" >
       (function(m,e,t,r,i,k,a){m[i]=m[i]||function(){(m[i].a=m[i].a||[]).push(arguments)};
       m[i].l=1*new Date();k=e.createElement(t),a=e.getElementsByTagName(t)[0],k.async=1,k.src=r,a.parentNode.insertBefore(k,a)})
       (window, document, "script", "https://mc.yandex.ru/metrika/tag.js", "ym");
    
       ym(51793085, "init", {
            id:51793085,
            clickmap:true,
            trackLinks:true,
            accurateTrackBounce:true
       });
    </script>
    <noscript><div><img src="https://mc.yandex.ru/watch/51793085" style="position:absolute; left:-9999px;" alt="" /></div></noscript>
    <!-- /Yandex.Metrika counter -->

    <link href='https://fonts.googleapis.com/css?family=Lobster&subset=latin,cyrillic' rel='stylesheet' type='text/css'>
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <title><?php echo($head_title); ?></title>
    
    <?php echo($head); ?>
	<?php echo($styles); ?>
    <?php echo($scripts); ?>
    
    <?php
    	/**
		 * Блоки для мобильного меню
		 */
		$mobileBlocks = array('#block-views-news-block');
		$mobileMenu = array(
			'.region-main-menu .block .content' => t('Главное меню'),
			'.view-categories .view-content' => t('Категории товаров'),
		);
		$jsonMobileBlocks = json_encode($mobileBlocks);
		$jsonMobileMenu = json_encode($mobileMenu);
		$jsonMobileBlocks = str_replace('"', "'", $jsonMobileBlocks);
		$jsonMobileMenu = str_replace('"', "'", $jsonMobileMenu);
	?>
</head>

<body class="<?php print $classes; ?>" <?php print $attributes;?> data-mobile-blocks = "<?= $jsonMobileBlocks ?>" data-mobile-menu = "<?= $jsonMobileMenu ?>">
	<span id="to-top"></span>   
    <div id="skip-link">
        <a href="#main-content" class="element-invisible element-focusable">
            <?php print t('Skip to main content'); ?>
        </a>
    </div>
    <?php echo($page_top); ?>
    <?php echo($page); ?>
    <?php echo($page_bottom); ?>
</body>
</html>