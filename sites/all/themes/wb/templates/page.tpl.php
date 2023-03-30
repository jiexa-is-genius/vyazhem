<?php global $user; ?>
<div class="main-page-box">
	<div class="page-content-box">
    	<div class="page-content-padding">
            <header>
                <div class="header-box">
                	<div class="page-content">
                    	<div class="page-wrapper">
                        	<div class="header-left">
                                <div class="logo">
                                    <a href="/" title="<?= htmlspecialchars($site_name) ?>">
                                        <span class="site-name"><?= $site_name ?></span>
                                        <span class="site-slogan"><?= $site_slogan ?></span>
                                    </a>
                                </div>
                            </div>
                            <div class="header-right">
                                <div class="header-card-block">
									<?php 
                                        if ($page['card']) {
                                            print render($page['card']);
                                        } 
                                    ?>
                                </div>
                            </div>
						</div>
                    </div>
                </div>
            </header>
            
            <div class="main-menu">
                <div class="page-content">
                    <div class="page-wrapper">
                        <nav>
							<div class="main-menu-box">
								<div class="main-menu-btn">
                                	<?= t('Главное меню') ?>
                                </div>
								<nav>
								<?php 
                                    if ($page['main_menu']) {
                                        print render($page['main_menu']);
                                    } 
                                ?>
                                </nav>
                            </div>
                        </nav>
                    </div>
                </div>
            </div>
            <?php if(isset($user->uid) and !empty($user->uid)): ?>
            <div class="page-wrapper">               
                <div class="wb-notice-box">
                    <h3><?= t('Новые заказы') ?></h3>
                    <ul><li>12321</li></ul>
                </div>
            </div>
            <?php endif; ?>
            <div class="slider">
            	<?php 
					if ($page['slider']) {
						print render($page['slider']);
					} 
				?>
            </div>
            
            <?php if(drupal_is_front_page()): ?>
            <!--div class="page-wrapper"> 
                <div class="frontpage-info-block">             
                    <p><?= t('Уважаемые покупатели!'); ?></p>
                    <p><?= t('При заказе через сайт действует скидка -10% на весь ассортимент.'); ?></p>
                </div>
            </div-->
            <?php endif; ?>
            
            <div class="page-content">
                <div class="page-wrapper">
                	<div class="page-columns">
                    	<div class="page-column-left">
                        	<nav>
							<?php 
								if ($page['left_block']) {
									print render($page['left_block']);
								} 
							?>
                            </nav>
                        </div>
                        <div class="page-column-right">
							<article>
							<?php if($messages): ?>
                                <div id="messages">
                                    <div class="section clearfix">
                                        <?php print $messages; ?>
                                    </div>
                                </div>
                            <?php endif; ?>
                            
                            <?php $tabsPrint = render($tabs); ?>
                            <?php if ($tabsPrint <> ''): ?>
                                <div class="tabs">
                                    <?php print $tabsPrint; ?>
                                </div>
                            <?php endif; ?>
                            
                            <?php print render($title_prefix); ?>
                            <?php print render($title_suffix); ?>
                            
                            <?php print render($page['help']); ?>
                            
                            <?php if ($action_links): ?>
                                <ul class="action-links">
                                    <?php print render($action_links); ?>
                                </ul>
                            <?php endif; ?>
                            
                            <?php if ($title): ?>
                                <h1 class="title" id="page-title">
                                  <span><?php print $title; ?></span>
                                </h1>
                            <?php endif; ?>
                            <?php print render($page['content']); ?>
                            </article>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <div class="page-footer-box">
    	<footer>
        	<?php if(drupal_is_front_page()): ?>
                <div class="sub-footer-box">
                    <div class="page-wrapper">
                        <div class="site-contact-row-address" style="margin-top: 0;">
                            <?= t('г. Павлодар, ул. Камзина 29/1') ?>
                        </div>
                        <div class="site-contact-row-address">
                            <?= t('г. Павлодар, ул. Торайгырова 56 (ТД "Барыс", 91 бутик)') ?>
                        </div>
                        <div class="site-contact-row-phone">
                            <a href="https://wa.me/77786001793" title="WhatsApp" class="whatsapp-lnk" target="_blank">+7 778 600 17 93</a>
                        </div>
                    </div>
                    <div class="yandex-map">
                        
                        <script type="text/javascript" charset="utf-8" async src="https://api-maps.yandex.ru/services/constructor/1.0/js/?um=constructor%3Aa38b9bc797ed0a144d3f7e2953256231e29f00ce9cc439cd92b40f361c19dea1&amp;width=100%25&amp;height=400&amp;lang=ru_RU&amp;scroll=true"></script>
                        
                    </div>
                </div>
            <?php endif; ?>
            <div class="footer-box">
            	<div class="page-wrapper">
                	<div class="footer-left">
                    	&copy; <?= $site_name ?> <?= date('Y') ?>
                    </div>
                    <div class="footer-right">
                    	<a href="/user" title="<?= t('Вход в консоль администратора') ?>">
                        	<?= t('Вход в консоль администратора') ?>
						</a>
                    </div>
				</div>
            </div>
		</footer>
    </div>
</div>
<div class="whatapp-button">
    <a href="https://wa.me/77786001793" title="WhatsApp" target="_blank">
    <div class="whatapp-button-relative">
        <svg xmlns="http://www.w3.org/2000/svg" xmlns:xlink="http://www.w3.org/1999/xlink" viewBox="0 0 32 32" class="wh-messenger-svg-whatsapp wh-svg-icon"><path d=" M19.11 17.205c-.372 0-1.088 1.39-1.518 1.39a.63.63 0 0 1-.315-.1c-.802-.402-1.504-.817-2.163-1.447-.545-.516-1.146-1.29-1.46-1.963a.426.426 0 0 1-.073-.215c0-.33.99-.945.99-1.49 0-.143-.73-2.09-.832-2.335-.143-.372-.214-.487-.6-.487-.187 0-.36-.043-.53-.043-.302 0-.53.115-.746.315-.688.645-1.032 1.318-1.06 2.264v.114c-.015.99.472 1.977 1.017 2.78 1.23 1.82 2.506 3.41 4.554 4.34.616.287 2.035.888 2.722.888.817 0 2.15-.515 2.478-1.318.13-.33.244-.73.244-1.088 0-.058 0-.144-.03-.215-.1-.172-2.434-1.39-2.678-1.39zm-2.908 7.593c-1.747 0-3.48-.53-4.942-1.49L7.793 24.41l1.132-3.337a8.955 8.955 0 0 1-1.72-5.272c0-4.955 4.04-8.995 8.997-8.995S25.2 10.845 25.2 15.8c0 4.958-4.04 8.998-8.998 8.998zm0-19.798c-5.96 0-10.8 4.842-10.8 10.8 0 1.964.53 3.898 1.546 5.574L5 27.176l5.974-1.92a10.807 10.807 0 0 0 16.03-9.455c0-5.958-4.842-10.8-10.802-10.8z" fill-rule="evenodd"></path></svg>
        <!--div class="whatapp-button-tooltip">
            <?= t('Задайте свой вопрос прямо сейчас') ?>
        </div-->
    </div>
    </a>
</div>