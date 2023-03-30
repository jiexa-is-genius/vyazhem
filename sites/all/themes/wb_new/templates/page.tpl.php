<?php 
	global $user; 
	// Смотрим, разрешаем ли администраторам смотреть секции на сайте
	$roles = isset($user->roles) ? $user->roles : array();
	$adminRoles = ['administrator', 'сustomer'];
	$isAdmin = false;
	foreach($adminRoles as $role) {
		if(in_array($role, $roles)) {
			$isAdmin = true;
			break;
		}
	}
?>
<div class="page-separator">
	<div class="page-separator-top">
    	<div class="page-content">
        	<div class="page-wrapper">
            	<div class="page-wrapper-white">
                	<header>
                    	<div class="header-top">
                        	<div class="header-top-separator">
                            	<div class="header-top-separator-left">
                                	<nav><?php if ($page['header_menu']) { print render($page['header_menu']); } ?></nav>
                            	</div>
                                <div class="header-top-separator-center">
                                	<?php if ($page['header_search']) { print render($page['header_search']); } ?>
                            	</div>
                                <div class="header-top-separator-right">
                            		<!--div class="block login-block">
										<?php if(isset($user->uid) and $user->uid > 0): ?>
                                            <a href="/user" title="<?= $user->mail ?>"><?= $user->mail ?></a>
                                            <a href="/user/logout" title="<?= $user->mail ?>"><?= t('Выход') ?></a>
                                        <?php else: ?>
                                            <a href="/user" title="<?= t('Вход') ?>"><?= t('Вход') ?></a>
                                            <a href="/user/register" title="<?= t('Вход') ?>"><?= t('Регистрация') ?></a>
                                        <?php endif; ?>
                                    </div-->
                                </div>
                            </div>
                        </div>
                        <div class="header-sub">
                        	<div class="header-sub-separator">
                            	<div class="header-sub-separator-left">
                                	<a href="/" title="<?= t('Главная страница') ?>" id="logo">
                                        <img src="/sites/all/themes/wb_new/logo.png" width="291" height="80" alt="<?= $site_name ?>" />
                                    </a>
                                </div>
                                <div class="header-sub-separator-center">
                                    <div class="header-phones">
                                        <?php if(theme_get_setting('company_phone1')): ?>
                                            <div class="header-phone-row header-phone-row-first">
                                                <?= theme_get_setting('company_phone1') ?>
                                            </div>
                                        <?php endif; ?>
                                        <?php if(theme_get_setting('company_phone2')): ?>
                                            <div class="header-phone-row">
                                                <?= theme_get_setting('company_phone2') ?>
                                            </div>
                                        <?php endif; ?>
                                        <!--div class="header-phone-button">
                                            <a href="/node/add/callorder" class="btn-order-call" title="<?= t('Заказать звонок') ?>">
                                                <?= t('Заказать звонок') ?>
                                            </a>
                                        </div-->
                                    </div>
                                </div>
                                <div class="header-sub-separator-right">
                                	<?php if ($page['card']) { print render($page['card']); } ?>
                                </div>
                            </div>
                        </div>
                    </header>
                    
                    <div class="mobile-menu">
                    	<div class="mobile-menu-button"><?= t('Меню') ?></div>
                        <div class="mobile-menu-content"></div>
                    </div>
                    
                    <div class="main-menu">
                    	<div class="page-column-separator">
                        	<div class="page-column-left">
                                <div class="menu-categories-title">
                                	<?= t('Категории') ?>
                                </div>
                            </div>
                            <div class="page-column-right">
                               <nav><?php if ($page['main_menu']) { print render($page['main_menu']); } ?></nav>
                            </div>
                        </div>
                    </div>
                    
                    <div class="main-content">
                    	<div class="page-column-separator">
                        	<div class="page-column-left">
                                <?php if ($page['left_block']) { print render($page['left_block']); } ?>
                            </div>
                            <div class="page-column-right">
								<div class="page-content-padding">
                                	<article>
									<?php
										$noticeAccess = false;
										$noticeRolles = ['administrator', 'сustomer'];
										foreach($noticeRolles as $role) {
											if(in_array($role, $roles)) {
												$noticeAccess = true;
												break;
											}
										}
									?>
                                    <?php if($isAdmin): ?>  
                                        <div class="wb-new-callorder">
                                            <h3><?= t('Звонок заказан') ?></h3>
                                            <div class="wb-notice-box-text">
                                                <a href="/callorder" title="<?= t('Просмотреть') ?>"><?= t('Заказан новый звонок') ?></a>
                                            </div>
                                        </div>
                                                    
                                        <div class="wb-notice-box">
                                            <h3><?= t('Новые заказы') ?></h3>
                                            <ul></ul>
                                        </div>
                                    <?php endif; ?>
                                    
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
                                    <div class="mobile-bottom-content-box"></div>
                                </div>
                            </div>
                        </div>
                    </div>
                    
                </div>
            </div>
        </div>
    </div>
    <div class="page-separator-bottom">
    	<div class="page-wrapper">
    		<div class="footer-box">
            	&copy; &laquo;<?= $site_name ?>&raquo;, <?= date('Y') ?>
            </div>
		</div>
    </div>
</div>
