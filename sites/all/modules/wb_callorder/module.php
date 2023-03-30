<?php
	/**
	 * Исходный код модуля
	 */

	function wb_callorder_form_alter(&$form, &$form_state, $form_id) {
		if($form_id == 'callorder_node_form') {
			drupal_set_title(t('Заказ звонка'));
			$form['actions']['submit']['#value'] = t('Заказать звонок');
			$form['actions']['submit']['#submit'][] = 'wb_callorder_form_node_form_submit';
			
		}
	}
	
	function wb_callorder_form_node_form_submit($form, &$form_state) {
		if (!empty($_SESSION['messages']['status'])) {
			unset($_SESSION['messages']['status']);
			drupal_set_message(t('Звонок заказан! Мы свяжемся с Вами в ближайшее время!'));
		}
		$form_state['redirect'] = 'frontpage';
	}
?>