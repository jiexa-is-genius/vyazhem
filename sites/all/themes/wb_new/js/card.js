(function($) {
	
	function checkSale(price) {
		var jsonSales = $('.wb-card-form').attr('data-card-sales');
		var enabledSales = +$('.wb-card-form').attr('data-card-sales-enabled');
		
		if(enabledSales == 1) {
			var sales = {};
			if(jsonSales != undefined) { 
				jsonSales = jsonSales.replace(/'/g, '"');
				sales = JSON.parse(jsonSales);
			}
			sale = 0;
			Object.keys(sales).forEach(function(saleValue){
				var salePrice = +sales[saleValue];
				if(price > salePrice) {
					sale = +saleValue;
				}
			});
			
			if(sale > 0) {
				$('.wb-card-sale-box .wb-card-sale-price').html(price);
				var salePrice = sale * price / 100;
				salePrice = +Math.round(salePrice);
	
				price = price - salePrice;
				price = +Math.round(price);
				
				$('.wb-card-sale-box .wb-card-sale-sale').html(sale);
				$('.wb-card-sale-box .wb-card-sale-new-price').html(price);
				
				$('.wb-card-sale-box').slideDown(300);
			} else {
				$('.wb-card-sale-box').slideUp(300);
			}
		}
		
		return price;
	}
	
	function reCalc() {
		var sum = 0;
		$('.wb-card-row').each(function() {
			var count = +$('input', $(this)).val();
			var price = +$('.wb-card-row-price', $(this)).attr('data-wb-product-price');
			var sumPrice = count * price;
			sum = sum + sumPrice;
			$('.wb-card-row-price-value', $(this)).html(sumPrice);
		});
		
		sum = checkSale(sum);
		
		$('.wb-card-sum-btn-value').html(sum);
		if($('.wb-card-row').length == 0) {
			$('.wb-card-form').removeClass('wb-card-not-empty');
			$('.wb-card-form').addClass('wb-card-empty');
		}
	}
	
	function checkNote() {
		$.get('/wbNotice.php', function(data) {
			if(data.result != undefined && data.result == true) {
				$('.wb-notice-box ul').html('');
				if(data.rows.length > 0) {
					var isNew = false;
					$.each(data.rows, function( index, value ) {
						if(value.isNew != undefined && value.isNew == '1') { isNew = true; }
						var li = '';
						li += '<a href = "/node/' + value.nid + '">[ ' + value.noteDate + ' ] Новый заказ на сайте!</a>';
						li += ' [ <span class = "wb-note-hide" data-note-id = "' + value.id + '">Скрыть</span> ]';
						if(li != '') { li = '<li>' + li + '</li>'; }
						$('.wb-notice-box ul').append(li);
					});
					
					if(isNew) {
						/*if($('#iframeAudio').length > 0) { $('#iframeAudio').remove(); }
						$('body').append('<iframe src="/sites/all/themes/wb/audio.mp3" allow="autoplay" style="display:none" id="iframeAudio"></iframe>');*/
					}
					if($('.wb-notice-box ul li').length > 0) {
						$('.wb-notice-box').slideDown(200);
					} else {
						$('.wb-notice-box').slideUp(200);
					}
				}
			}
		});
	}
	
	$(document).ready(function() {
		$('.product-page-product-count-minus button').click(function() {
			var current = +$('#card-products-count').val();
			current = current - 1;
			if(current < 1) { current = 1; }
			$('#card-products-count').val(current);
		});
		$('.product-page-product-count-plus button').click(function() {
			var current = +$('#card-products-count').val();
			current = current + 1;
			$('#card-products-count').val(current);
		});
		
		
		/* Уведомления */
		$('.play-audio').click(function() {
			
			console.log('HIRE AAA');
			/*var a = new Audio('/sites/all/themes/wb/audio.mp3');
				a.autoplay = true;
				a.loop = true;
				a.volume = 1;
				a.play();
				a.pause();*/
			/*intro.autoplay = true;
			intro.loop = true;
			var media = document.getElementById("carteSoudCtrl");
			const playPromise = media.play();
			if (playPromise !== null){
				playPromise.catch(() => { media.play(); })
			}*/
		});
		if($('.wb-notice-box').length > 0) {
			setTimeout(function() {
				// Первый запуск через 5ть секунд
				checkNote();
				// После проверяем каждую минуту
				setInterval(function() { checkNote(); }, 1000 * 60);
			}, 1000);
		}
		
		$(document).on('click', '.wb-note-hide', function() {
			var li = $(this).parent('li');
			$.get('/wbNotice.php', {'noteID' : $(this).attr('data-note-id')}, function(data) {
				li.remove();
				if($('.wb-notice-box ul li').length > 0) {
					$('.wb-notice-box').slideDown(200);
				} else {
					$('.wb-notice-box').slideUp(200);
				}
			});
		});
		/* Уведомления */
		
		/* Добавление в корзину */
		$('.wb-products-add-to-card-btn').click(function() {
			var btn = $(this);
			if($('.wb-product-page').length > 0) {
				var product = +$('.node-product').attr('data-product-id');
				var color = null;
				var size = null;
				
				// Количество
				var count = +$('#card-products-count').val();
				if(count < 1) {
					$.alert({
						title: 'Добавление в корзину',
						content: 'Кол-во не может быть меньше одного!',
					});
					return false;
				}
				
				// Если товар у которого есть цвет
				if($('.node-product').hasClass('wb-product-type-color')) {
					if($('.wb-product-color-row-active').length == 0) {
						$.alert({
							title: 'Добавление в корзину',
							content: 'Для данного товара необходимо выбрать цвет, перед добавлением в корзину!',
						});
						return false;
					} else {
						color = $('.wb-product-color-row-active').attr('data-color-id');
					}
				}
				
				// Размер
				if($('.node-product').hasClass('wb-product-type-size')) {
					if($('#wb-product-size-lines').val() == '') {
						$.alert({
							title: 'Добавление в корзину',
							content: 'Для данного товара необходимо выбрать размер, перед добавлением в корзину!',
						});
						return false;
					} else {
						size = $('#wb-product-size-lines').val();
					}
				}
				
				var reqParams = {
					'product' : product,
					'count' : count,
					'color' : color,
					'size' : size,
				}
				var btnText = btn.html();
				btn.attr('disabled', 'disabled');
				btn.html('Добавление...');
				$.get('/wbAddToCard.php', reqParams, function(data) {
					if(data['error'] != null) {
						$.alert({
							title: 'Добавление в корзину',
							content: data['error'],
						});
					} else {
						$('#block-wb-card-wb-card .wb-card-count').html(data['card']['count']);
						$('#block-wb-card-wb-card .wb-card-price .wb-card-value').html(data['card']['price']);
						$.confirm({
							title: 'Добавление в корзину!',
							content: 'Товар был добавлен в корзину!',
							buttons: {
								cancel: { text: 'Отмена', },
								somethingElse: {
									text: 'Оформить заказ',
									btnClass: 'btn-blue',
									keys: ['enter', 'shift'],
									action: function(){ document.location = '/mycard'; }
								}
							}
						});
					}
				}).fail(function() {
					$.alert({
						title: 'Добавление в корзину',
						content: 'Ошибка выполнения запроса! Повторите попытку.',
					});
				}).always(function() {
					btn.html(btnText);
					btn.removeAttr('disabled');
				});				
			} else {
				var nid = +btn.attr('data-product-id');
				
				var modalWindow = $.confirm({
					title: 'Добавить в корзину',
					content: 'url:/wbAddToCardList.php?product=' + nid,
					columnClass: 'medium',
					buttons: {
						cancel: { text: 'Отмена', },
						sel: {
							text: 'Добавить',
							btnClass: 'btn-blue wb-card-list-sel-btn',
							keys: ['enter', 'shift'],
							action: function(btn){
								var mBtn = $('.wb-card-list-sel-btn');
								var mReqParams = {
									'product' : nid,
									'count' : null,
									'color' : null,
									'size' : null,
								}
																
								if(mReqParams['product'] == null) {
									$.alert({
										title: 'Добавление в корзину',
										content: 'Товар не найден',
									});
									return false;
								}
								
								if($('#wb-list-card-count').length > 0) { mReqParams['count'] = +$('#wb-list-card-count').val(); }
								if(mReqParams['count'] < 1) {
									$.alert({
										title: 'Добавление в корзину',
										content: 'Кол-во не может быть меньше одного!',
									});
								}
								
								if($('.wb-list-card-color').length > 0) {
									if($('.wb-list-card-color-selected').length == 0) {
										$.alert({
											title: 'Добавление в корзину',
											content: 'Для данного товара необходимо выбрать цвет, перед добавлением в корзину!',
										});
										return false;
									} else {
										mReqParams['color'] = +$('.wb-list-card-color-selected').attr('data-list-card-color-id');
									}
								}
								
								if($('#wb-list-card-size').length > 0) {
									if($('#wb-list-card-size').val() == '') {
										$.alert({
											title: 'Добавление в корзину',
											content: 'Для данного товара необходимо выбрать размер, перед добавлением в корзину!',
										});
										return false;
									} else {
										mReqParams['size'] = $('#wb-list-card-size').val();	
									}
								}
								
								var btnText = mBtn.html();
								mBtn.attr('disabled', 'disabled');
								mBtn.html('Добавление...');
								$.get('/wbAddToCard.php', mReqParams, function(data) {
									if(data['error'] != null) {
										$.alert({
											title: 'Добавление в корзину',
											content: data['error'],
										});
									} else {
										$('#block-wb-card-wb-card .wb-card-count .wb-card-value').html(data['card']['count']);
										$('#block-wb-card-wb-card .wb-card-price .wb-card-value').html(data['card']['price']);
										modalWindow.close();
									}
								}).fail(function() {
									$.alert({
										title: 'Добавление в корзину',
										content: 'Ошибка выполнения запроса! Повторите попытку.',
									});
								}).always(function() {
									mBtn.html(btnText);
									mBtn.removeAttr('disabled');
								});	
								return false;
							}
						}
					}
				});
			}
		});
		/* Добавление в корзину */
		
		/*$(document).on('click', '.wb-list-card-color', function() {
			$('.wb-list-card-color').removeClass('wb-list-card-color-selected');
			$(this).addClass('wb-list-card-color-selected')
		});*/
		
		$('.wb-product-color-row').click(function() {
			if($('.wb-product-color-row-is-exist-false', $(this)).length == 0) {
				$('.wb-product-color-row').removeClass('wb-product-color-row-active');
				$(this).addClass('wb-product-color-row-active');
			} else {
				$.alert({
					title: 'Добавление в корзину',
					content: 'Товара нет в наличии!',
				});
			}
			
		});
		$('.node-myoder-form .field-name-field-mobile .form-text').mask('+7(999) 999 99 99');
		
		$('.wb-card-row input').change(function() { reCalc(); });
		$('.wb-card-row-remove').click(function() {
			var row = $(this).parents('.wb-card-row');
			var id = +row.attr('data-wb-cardrow-id');

			$.confirm({
				title: 'Удаление из корзины!',
				content: 'Вы желаете удалить товар из корзины?',
				buttons: {
					cancel: { text: 'Отмена' },
					remove: {
						text: 'Удалить',
						btnClass: 'btn-blue',
						keys: ['enter', 'shift'],
						action: function(){
							$.get('/wbRemoveFromCard.php', {'id' : id}, function(data) {
								row.remove();
								reCalc();
							});
						}
					}
				}
			});
		});
		$('.page-mycard .wb-card-form').submit(function() {
			if($('.wb-card-user-is-not-auth').length > 0) {
				$.confirm({
					title: 'Оформление заказа',
					content: 'Для оформления заказа необходимо войти на сайт или зарегистрироваться!',
					buttons: {
						cancel: { text: 'Отмена' },
						remove: {
							text: 'Войти',
							btnClass: 'btn-blue',
							keys: ['enter', 'shift'],
							action: function(){
								document.location = '/user';
							}
						}
					}
				});
				return false;
			}
		});
	});
})(jq331);