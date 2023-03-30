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
	$(document).ready(function() {
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
		
		$('.main-menu-btn').click(function() {
			var box = $(this).parent();
			if($('ul.menu', box).css('display') == 'none') {
				$('ul.menu', box).slideDown(200);	
			} else  {
				$('ul.menu', box).slideUp(200);	
			}
		});
		$('.page-column-left #block-views-categories-block h2').click(function() {
			var box = $(this).parent();
			if($('.view-categories .view-content', box).css('display') == 'none') {
				$('.view-categories .view-content', box).slideDown(200);	
			} else  {
				$('.view-categories .view-content', box).slideUp(200);	
			}
		});
		$('#block-views-categories-block span.wb-categories-tree-parent, #block-views-categories-block a').click(function() {
			var box = $(this).parents('li:first');
			if($('>.item-list > ul', box).length > 0) {
				if($('>.item-list > ul', box).css('display') == 'none') {
					$('>.item-list > ul', box).slideDown(200);	
				} else  {
					$('>.item-list > ul', box).slideUp(200);	
				}
			}
		});
		
		
	});
})(jQuery);