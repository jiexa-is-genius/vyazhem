<?php
	/**
	 * Основные РНР функции
	 */
	
	function wbuild_process_html(&$variables) {
	  	$variables['styles'] = preg_replace('/\.css\?.*"/','.css"', $variables['styles']);
	}