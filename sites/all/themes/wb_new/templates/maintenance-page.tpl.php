<!DOCTYPE html>
<html xml:lang="ru-ru" lang="ru-ru" dir="ltr">
    <head>
        <meta name="viewport" content="width=device-width, initial-scale=1.0" />
        <title><?php echo($head_title); ?></title>
        
        <?php echo($head); ?>
        <?php echo($styles); ?>
        <?php echo($scripts); ?>
        <link rel="shortcut icon" href="/sites/all/themes/wb_new/img/ico.png" type="image/png" />
    </head>
    <body class="<?php print $classes; ?>" <?php print $attributes;?>>
    	<div style="padding-top: 30px;">
       		<div class="mainstance-box">
            	<a href="/" title="<?= $site_name ?>">
                	<img src="/sites/all/themes/wb_new/logo.png" align="<?= $site_name ?>" />
                </a>
                <div class="mainstance-box-content">
                	<?php print $content; ?>
                </div>
            </div>
        </div>
    </body>
</html>