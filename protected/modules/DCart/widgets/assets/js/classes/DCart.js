/**
 * Класс DCart
 * 
 * @use json_decode.js http://phpjs.org/functions/json_decode/
 * @use DCartHelper.js
 */
if(typeof(DEBUG_MODE) == 'undefined') {
	var DEBUG_MODE = false;
}

var DCart = {
	/**
	 * Get value
	 * @param mixed value value.
	 * @param mixed def default value.
	 */
	v: function(val, def) {
		return (typeof(val) != 'undefined') ? val : def;
	},
	
	exit: function(msg) {
		if(typeof(msg) != 'undefined') console.log(msg);
		return false;
	},

	/**
	 * Добавление товара в корзину
	 * @param string url Ссылка на действие добавления в корзину
	 * @param object data Дополнительные данные. (например: выбранный цвет).
	 * @param jQuery.Event event Объект события    
	 */
	add: function(url, data, event) {
		
		if((typeof(data) != 'object') && (typeof(data) != 'string')) 
			return false;
		
		if(typeof(data) == 'string') 
			data = json_decode(data);
		
		if(!data) return false;
		
		var $eventTarget = null;
		if(event instanceof jQuery.Event) {
			if($.inArray($(event.target).prop("tagName"), ["A", "INPUT", "BUTTON"]) > -1) {
				$eventTarget = $(event.target);
			}
			else if($(event.target).parents("a:first,button:first,input:first").length) {
				$eventTarget = $(event.target).parents("a:first,button:first,input:first");
			}
		}
		
		var hasError = false;
		var _data = {};
		for(var key in data) {
			if(data[key].length && (data[key].indexOf("js:") === 0)) {
				_data[key]=eval(data[key].replace("js:", "") + "();");
			}
			else if($(data[key]).length) {
				var value = $(data[key]).val();
				if(key == 'count') {
					if(isNaN(+value) || (+value <= 0)) {
						value = 1;
					}
				}
				if(!value && (typeof(event) == 'object')) {
					hasError = true;
				}
				
				_data[key] = value;
			}
		}
		
		if(!hasError) { 
			var data = { data: _data };
			
			if(event instanceof jQuery.Event) {
				data.model = $eventTarget.attr("data-dcart-model");
				if(!data.model && DEBUG_MODE) { 
					return DCart.exit('Warning: (DCart.js) Model not defined.');
				}
			} 
			
			if(typeof(DCartWidget) == 'object') 
				DCartWidget.prepareData(data);
			
			$.post(url, data, function(data) {
				if(typeof(data) != 'object') {
					return DEBUG_MODE ? DCart.exit('Error: (DCart.js) Invalid server responce.') : false;
				}
				if(data.success) {
					if(typeof(DCartModalWidget) == 'object') DCartModalWidget.hAddAjaxSuccess(data);
					if($('.dcart-total-count').length)
						$('.dcart-total-count').html(DCart.v(data.data.cartTotalCount,'-'));
					if($('.dcart-total-price').length)
						$('.dcart-total-price').html(DCart.v(data.data.cartTotalPrice,'0'));
				}
				else {
					if(data.error) alert(data.error);
				}
	        }, "json");
		}
		
		return !hasError;
	},
	
	/**
	 * Получить количество товара в корзине
	 * @param string hash хэш товара в корзине.
	 */
	getCount: function(hash) {
		var count = -1;
		
		$.ajax({
			url: "/dCart/getCount",
			type: "post",
			async: false,
			data: {hash: hash}, 
			dataType: "json",
			success: function(json) {
				if(json.success) 
					count = json.data.count; 
				else 
					DCart.showErrors(json);
			}
		});
		
		return count;
	},
	
	/**
	 * Обновить количество
	 * @param string hash cart item hash.
	 * @param integer count item new count.
	 * @param function hSuccess ajax success callback обработчик. 
	 */
	updateCount: function(hash, count, hSuccess) {
		if (count <= 0) {
			if(!confirm('Вы хотите удалить товар из корзины?')) {
				return false;
			}
	    }
	    
		$.post('/dCart/updateCount', {hash: hash, count: count}, hSuccess, 'json');
		
		return false;
	},
	
	/**
	 * Удаление позиции товара из корзины
	 * @param string url ссылка на действие удаления 
	 * @param string hash хэш удаляемого из корзины товара 
	 * @param function hSuccess callback обработчик. 
	 */
	remove: function(url, hash, hSuccess)
	{
		if(typeof(hSuccess) != 'function') {
			hSuccess = function(data) {
				if(data.success) {
					window.location.reload(); 
				}
				else 
					DCart.showErrors(data);
			};
		}
			
		$.post(url, {hash: hash}, hSuccess, "json"); 
	},
	
	/**
	 * Очистка корзины
	 * @param string url ссылка на действие очистки
	 * @param function hSuccess callback обработчик. 
	 */
	clear: function (url, hSuccess) {
		if(typeof(hSuccess) != 'function') {
			hSuccess = function(data) {
				if(data.success) {
					window.location.reload(); 
				}
				else 
					DCart.showErrors(data);
			};
		}
			
		$.post(url, {clear: 'clear'}, hSuccess, "json"); 
	},
	
	/**
	 * Обработка ошибок по умолчанию
	 * @param object data Данные после ajax запроса.
	 */
	_logErrors: function(data) {
		if(!DEBUG_MODE) return true;
		
		if(!data.success) {
			if(data.errors.length > 0) {
				$(data.errors).each(function(i) {
					console.log(data.errors[i]);
				});
			} else {
				if(data.error) console.log(data.error);
				else if(data.errorDefaultMessage) console.log(data.errorDefaultMessage);
			}
		}
	}
}