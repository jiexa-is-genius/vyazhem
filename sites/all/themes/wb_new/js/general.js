(function($) {
	function sliderChange(c, n, notHover) {
		if(notHover === undefined) { notHover = false; } else { notHover = true; }
		var change = true;
		
		if(!notHover) {
			if($('.view-slider').hasClass('view-slider-hovered')) {
				change = false;
			}	
		}
		
		if(change) {
			$('.view-slider .views-row-' + n + ' img').animate({ opacity: 1 }, 400, function() {
				$('.view-slider .views-row-' + c + ' img').animate({ opacity: 0 }, 400, function() {
					$('.slider-buttons').attr('data-current-slide', n);
					$('.slider-buttons .slider-button').removeClass('slider-button-active');
					$('.slider-buttons .slider-button-' + n).addClass('slider-button-active');
				});
			});
		}
			
	}
	function checkCallorder() {
		$.get('/wbCallorderCheck.php', {}, function(response) {
			if($.trim(response) == '1') {
				$('.wb-new-callorder').show();
			} else {
				$('.wb-new-callorder').hide();
			}
		});
	}
	
	$(document).ready(function() {
		
		if($('.wb-new-callorder').length > 0) {
			checkCallorder();
			setInterval(checkCallorder, 1000 * 60 * 3);
		}		
		
		$('.field-name-field-mobile .form-text').mask('+7(999) 999 99 99');
		$('#search-block-form .form-text').attr('placeholder', 'Поиск на сайте...');
		$('#search-block-form .form-text').attr('autocomplete', 'off');
		
		$('.view-categories .view-content li').each(function() {
			if($('ul', $(this)).length > 0) {
				$('>.views-field >.field-content > a, >.views-field >.field-content > span', $(this)).addClass('categories-parent-link');	
			}
		});
		$('.view-categories .view-content li a').click(function() {
			if($(this).hasClass('categories-parent-link')) {
				return false;	
			}
		});
		
		// SLIDER
		if($('.view-slider').length > 0) {
			setInterval(function() {
				var sliderCount = +$('.view-slider .views-row').length;
				var cSlide = +$('.slider-buttons').attr('data-current-slide');
				var nSlide = cSlide + 1;
				if(nSlide > sliderCount) { nSlide = 1; }
				sliderChange(cSlide, nSlide);
			}, 5000);
			$('.view-slider').hover(function() {
				$(this).addClass('view-slider-hovered');
			}, function() {
				$(this).removeClass('view-slider-hovered');
			});
			$('.slider-buttons .slider-button').click(function() {
				var cSlide = +$('.slider-buttons').attr('data-current-slide');
				var nSlide = +$(this).attr('data-slider-slide');
				sliderChange(cSlide, nSlide, true);
			});
		}
		
		
		$('.region-left-block .view-categories li').each(function() {
			if($('ul', $(this)).length == 0) {
				$(this).addClass('category-last-node');
			}
		});
		
		$('.region-left-block .view-categories a, .region-left-block .view-categories span').click(function() {
			var li = $(this).parent().parent().parent();
			if($('> .item-list > ul', li).length > 0) {
				var childUl = $('> .item-list > ul', li);
				if(childUl.css('display') == 'none') {
					li.addClass('category-open');
					childUl.slideDown(300);
					if(!li.hasClass('last')) { li.addClass('category-border-bottom'); }
				} else {
					li.removeClass('category-open');
					childUl.slideUp(300);
					if(!li.hasClass('last')) { li.removeClass('category-border-bottom'); }
				}
				return false;	
			}
		});
		
		$('.region-left-block .view-categories a.active').parent().parent().parent().parents('li').each(function() {
			$('> .views-field > .field-content > span, > .views-field > .field-content > a', $(this)).click();
		});	
		
		
		if($('.callorder-change-status').length > 0) {
			$('.callorder-change-status').click(function() {
				var tid = $(this).attr('data-tid');
				var nid = $(this).attr('data-nid');
				$('#change-callorder-status select option').removeAttr('selected');
				$('#change-callorder-status select option').each(function() {
					if($(this).attr('value') == tid) { $(this).attr('selected', 'selected'); }
				});
				$('#change-callorder-status select').attr('data-current-nid', nid);
				$("#change-callorder-status").dialog();
			});
			
			$('#change-callorder-close').click(function() { $("#change-callorder-status").dialog('close'); });
			
			$('#change-callorder-ok').click(function() {
				var params = {
					'nid' : $('#change-callorder-status select').attr('data-current-nid'),
					'tid' : $('#change-callorder-status select').val(),
				};
				$('#change-callorder-ok').attr('disabled', 'disabled');
				$.get('/wbCallorderStatus.php', params, function(response) {
					if(response.result != undefined && response.result == true) {
						$('.callorder-change-status').each(function() {
							if($(this).attr('data-nid') == response.nid) {
								$(this).html(response.name);
								$(this).attr('data-tid', response.code);
							}
						});
						$("#change-callorder-status").dialog('close');
					}
				}).always(function() {
					$('#change-callorder-ok').removeAttr('disabled');
				});
			});
		}
	});
})(jQuery);