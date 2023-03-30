<?php
	/**
	 * Основные РНР функции
	 */
	
	function wb_new_process_html(&$variables) {
	  	$variables['styles'] = preg_replace('/\.css\?.*"/','.css"', $variables['styles']);
		if(isset($variables['classes_array'])) {
			if(in_array('node-type-myoder', $variables['classes_array'])) {
				/*$variables['head_title_array']['title']
				$variables['head_title']
				$variables['head_array']['title']
				var_dump();
				die;*/
			}
		}
	}
	