<?php
	function wb_new_form_system_theme_settings_alter(&$form, &$form_state, $form_id = NULL)  {
		if (isset($form_id)) { return; }
		$form['company_phone1'] = array(
			'#type' => 'textfield',
			'#title' => 'Телефон № 1',
			'#default_value' => theme_get_setting('company_phone1'),
			'#size' => 60,
			'#maxlength' => 128,
			'#required' => true,
		);
		$form['company_phone2'] = array(
			'#type' => 'textfield',
			'#title' => 'Телефон № 2',
			'#default_value' => theme_get_setting('company_phone2'),
			'#size' => 60,
			'#maxlength' => 128,
			'#required' => false,
		);
		$form['sale_enable'] = array(
			'#type' => 'checkbox',
			'#title' => t('Активировать скидки.'),
			'#default_value' => theme_get_setting('sale_enable'),
		);
		$form['sale_20'] = array(
			'#type' => 'textfield',
			'#title' => 'Скидка 20% начиная от суммы',
			'#default_value' => theme_get_setting('sale_20'),
			'#size' => 60,
			'#maxlength' => 128,
			'#required' => false,
		);
		$form['sale_22'] = array(
			'#type' => 'textfield',
			'#title' => 'Скидка 22% начиная от суммы',
			'#default_value' => theme_get_setting('sale_22'),
			'#size' => 60,
			'#maxlength' => 128,
			'#required' => false,
		);
		$form['sale_25'] = array(
			'#type' => 'textfield',
			'#title' => 'Скидка 25% начиная от суммы',
			'#default_value' => theme_get_setting('sale_25'),
			'#size' => 60,
			'#maxlength' => 128,
			'#required' => false,
		);
	}
?>