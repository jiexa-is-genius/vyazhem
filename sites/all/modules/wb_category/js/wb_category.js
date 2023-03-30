(function($) {
	// Раскрыть, свернуть размеры
	function fieldSizeTogle() {
		if($('.field-name-field-size').hasClass('field-name-field-size-open')) {
			$('.field-name-field-size').removeClass('field-name-field-size-open');
		} else {
			$('.field-name-field-size').addClass('field-name-field-size-open');
		}
	}
	
	// Проверка на доп поля
	function checkOtherFields(tid) {
		
		$('.field-name-field-colors').hide();
		$('.field-name-field-size').hide();
		$('.node-product-form').removeClass('node-product-form-color');
		$('.node-product-form').removeClass('node-product-form-size');
		$.get('/wbCheckTerm.php', {'tid' : tid}, function(data) {
			if($.trim(data) == 'color') {
				$('.field-name-field-colors').show();
				$('.node-product-form').addClass('node-product-form-color');
				$('.field-name-field-colors input[type=text], .field-name-field-colors select').attr('required', 'required');
			} else {
				//console.log($('.field-name-field-colors input[type=text], .field-name-field-colors select').length);
				$('.field-name-field-colors').hide();
				$('.field-name-field-colors .plup-remove-item').click();
				$('.node-product-form').removeClass('node-product-form-color');
				$('.field-name-field-colors input[type=text], .field-name-field-colors select').removeAttr('required');
			}
			if($.trim(data) == 'size') {
				$('.field-name-field-size').show();
				$('.node-product-form').addClass('node-product-form-size');
			}
		});
	}
	
	$(document).ready(function() {		
		if($('.node-product-form').length > 0) {
			$('.field-name-field-category li').each(function() {
				if($('ul', $(this)).length > 0) {
					$('> .form-item > .form-radio', $(this)).remove();	
				} else {
					$('.no-term-reference-tree-button', $(this)).remove();	
					$(this).addClass('wb-category-item');
				} 
			});
			
			if($('.field-name-field-size input:checked').length > 0) { fieldSizeTogle(); }
			$('.field-name-field-size > .form-item > label').click(function() {
				fieldSizeTogle();
			});
			
			if($('.field-name-field-category input.form-radio:checked').length > 0) {
				checkOtherFields($('.field-name-field-category input.form-radio:checked').val());
			} else { checkOtherFields(0); }
			
			$('.field-name-field-category input.form-radio').change(function() {
				checkOtherFields($(this).val());
			});
		}
	});
})(jQuery);