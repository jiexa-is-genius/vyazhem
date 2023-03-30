function Rubber() {
	var rubber = this;
	var $ = null;
	
	// Инициализируем блоки
	rubber.bottomBlocks = function() {
		var dataJson = $('body').attr('data-mobile-blocks');
		if(dataJson != undefined) {
			dataJson = dataJson.replace(/'/g, '"');
			var data = JSON.parse(dataJson);
			$.each(data, function(index, selector) {
				if($(selector).length > 0) {
					$('.mobile-bottom-content-box').append($(selector).html());
				}
			});
		}		
	}
	
	// Инициализируем меню
	rubber.menu = function() {
		var dataJson = $('body').attr('data-mobile-menu');
		if(dataJson != undefined) {
			dataJson = dataJson.replace(/'/g, '"');
			var data = JSON.parse(dataJson);
			Object.keys(data).map(function(selector) {
				var title = data[selector];
				if($(selector).length > 0) {
					var html = '<div class = "mobile-menu-box">';
					html += '<div class = "mobile-menu-box-title">' + title + '</div>';
					html += '<div class = "mobile-menu-box-content">' + $(selector).html() + '</div>';
					html += '</div>';
					$('.mobile-menu .mobile-menu-content').append(html);
				}
			});
		}
	}
	
	rubber.init = function(jQuery) { 
		$ = jQuery; 
		rubber.bottomBlocks(); 
		rubber.menu(); 
	}
} var rubber = new Rubber();

(function($) {
	$(document).ready(function() {
		rubber.init($);
		
		/**
		 * Мобильное меню
		 */
		$('.mobile-menu-button').click(function() {
			if($('.mobile-menu-content').css('display') == 'none') {
				$('.mobile-menu-content').slideDown(300);
			} else {
				$('.mobile-menu-content').slideUp(300);
			}
		});
		
		$('.mobile-menu-box-title').click(function() {
			var box = $(this).parent('.mobile-menu-box');
			if($('.mobile-menu-box-content > ul, .mobile-menu-box-content > div > ul', box).css('display') == 'none') {
				$(this).addClass('mobile-menu-box-title-expanded');
				$('.mobile-menu-box-content > ul, .mobile-menu-box-content > div > ul', box).slideDown(300);
			} else {
				$(this).removeClass('mobile-menu-box-title-expanded');
				$('.mobile-menu-box-content > ul, .mobile-menu-box-content > div > ul', box).slideUp(300);
			}
		});
		
		$('.mobile-menu-box a, .mobile-menu-box span').click(function() {
			var target = $(event.target);
			if(target.is('span') && $(this).children().length > 0) { return false; }
			
			var li = $(this).closest('li');
			var ul = $('ul:first', li);
			
			if(ul != undefined && ul.length > 0) {
				if(ul.css('display') == 'none') {
					li.addClass('mobile-menu-li-open');
					ul.slideDown(300);
				} else {
					li.removeClass('mobile-menu-li-open');
					ul.slideUp(300);
				}
				return false;	
			}
		});
	});
})(jq331);