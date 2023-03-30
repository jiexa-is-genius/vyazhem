(function($) {
	var isChange = false;
	function parseFormFieldAlt(row) {
		$('label', row).html('Наименование цвета <span class="form-required" title="This field is required.">*</span>');
		$('.description', row).remove();
	}
	function parseFormFieldTitle(row) {
		$('label', row).html('Наличие цвета <span class="form-required" title="This field is required.">*</span>');
		$('.description', row).remove();
		if($('input', row).length > 0) {
			var id = $('input', row).attr('id');
			var name = $('input', row).attr('name');
			var cls = 'form-select';
			var value = $('input', row).val();
			var html = '<select name = "' + name + '" id = "' + id + '" class = "' + cls + '">';
			if(value == '1' || value == '') {
				html += '<option value = "1" selected = "selected">В наличии</option>';
				html += '<option value = "none">Нет в наличии</option>';
			} else {
				html += '<option value = "1">В наличии</option>';
				html += '<option value = "none" selected = "selected">Нет в наличии</option>';
			}
			html += '</select>'; 
			$('input', row).remove();
			row.append(html);
		}
	}
	
	function parseFormField(row) {
		isChange = true;
		$('.form-item', row).each(function() {
			if($('input.form-text', $(this)).attr('id') != undefined) {
				if($('input.form-text', $(this)).attr('id') != $('input.form-text', $(this)).attr('id').replace('-alt', '')) {
					parseFormFieldAlt($(this));
				}
				if($('input.form-text', $(this)).attr('id') != $('input.form-text', $(this)).attr('id').replace('-title', '')) {
					parseFormFieldTitle($(this));
				}
			}
		});
		isChange = false;
	}
	
	function plupFormAlt(obj) {
		if(obj.attr('placeholder') != undefined) {
			obj.attr('placeholder', 'Наименование цвета');
			//obj.attr('required', 'required');
		}
	}
	
	function plupFormTitle(obj) {
		var row = obj.parent();
		var id = obj.attr('id');
		var name = obj.attr('name');
		var cls = 'form-select';
		var value = obj.val();
		var html = '<select name = "' + name + '" id = "' + id + '" class = "' + cls + '">';
		if(value == '1' || value == '') {
			html += '<option value = "1" selected = "selected">В наличии</option>';
			html += '<option value = "none">Нет в наличии</option>';
		} else {
			html += '<option value = "1">В наличии</option>';
			html += '<option value = "none" selected = "selected">Нет в наличии</option>';
		}
		html += '</select>'; 
		obj.remove();
		row.append(html);
	}
	
	function plupFormFields() {
		isChange = true;
		$('#plup-list li').each(function() {
			var row = $(this);
			$('input', row).each(function() {
				if($(this).attr('name') != $(this).attr('name').replace('[title]', '')) {
					plupFormTitle($(this))
				}
				if($(this).attr('name') != $(this).attr('name').replace('[alt]', '')) {
					plupFormAlt($(this));
				}
			});
		});
		isChange = false;
	}
	
	$(document).ready(function() {
				
		if($('#plup-list li').length > 0) {
			if(!isChange) { plupFormFields(); }
			document.body.addEventListener('DOMSubtreeModified', function () {
				if(!isChange) { plupFormFields(); }
			}, false);
		}
		
		$('form.node-product-form').submit(function() {
			if($('.node-product-form').hasClass('node-product-form-color')) {
				if($('#plup-list li').length == 0) {
					alert('Необходимо указать минимум один цвет для товара!');
					return false;
				}
			}
			if($('.node-product-form').hasClass('node-product-form-size')) {
				if($('.field-name-field-size .form-checkbox:checked').length == 0) {
					alert('Необходимо указать минимум один размер для товара!');
					return false;
				}
			}
		});
	});
})(jQuery);