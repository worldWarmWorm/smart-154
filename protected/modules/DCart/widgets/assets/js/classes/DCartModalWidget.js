/**
 * DCartWidget class
 * Скрипт класса для виджета корзины (DCart\widgets\CartWidget)
 * 
 */
var DCartModalWidget = {
	refresh: function(html) {
		$('.js-dcart-mcart').parent().html(html);
	},
	
	addItem: function(itemHtml) {
		$('.js-mcart-items').append(itemHtml);
	},
		
	countUp: function(e) {
		e.preventDefault();
		return DCartModalWidget._updateCount($(e.target).siblings('[name="count"]'), function(count) {
			return isNaN(count) ? 1 : count+1;
		});
	},
	
	countDown: function(e) {
		e.preventDefault();
		return DCartModalWidget._updateCount($(e.target).siblings('[name="count"]'), function(count) {
			return isNaN(count) ? 1 : count-1;
		});
	},
	
	updateCount: function(e) {
		e.preventDefault();
		return DCartModalWidget._updateCount($(e.target));
	},
	
	remove: function(e) {
		e.preventDefault();
		if(confirm("Удалить товар из корзины?")) {
			var hash=$(e.target).data("hash");
			DCart.remove("/dCart/remove", hash, function(data) {
				if(data.success) {
					$('.js-mcart-item[data-hash="'+hash+'"]').remove();
				}
				else {
					if(data.error) alert(data.error);
				}
				DCartModalWidget.updateTotal(data);
			});
		}
		
		return false;
	},
	
	updateItemByHash: function(hash, data) {
		return DCartModalWidget.updateItem($('.js-mcart-item[data-hash="'+hash+'"]'), data);
	},
	
	updateItem: function($item, data) {
		if(data.data.count > 0) {
			$item.find('.total-price').html(DCart.v(data.data.totalPrice,'-'));
			$item.find('.count .number input[name="count"]').val(data.data.count);
		}
		else {
			$item.remove();
		}
	},
	
	updateTotal: function(data) {
		if(!$('.js-mcart-item').length) {
			if($('.js-dcart-mcart').is('.in-content')) window.location.reload();
			$('.js-dcart-mcart').html('<div class="dcart-mcart-empty">Ваша корзина пуста</div>');
		}
		
		$('.dcart-total-price').html(DCart.v(data.data.cartTotalPrice,'0'));
		$('.dcart-total-count').html(DCart.v(data.data.cartTotalCount,'-'));
	},
	
	hAddAjaxSuccess: function(data) {
		if(data.data.isFirstItem) {
			DCartModalWidget.refresh(data.data.html);
		}
		else {
			if(data.data.isNewItem){
				DCartModalWidget.addItem(data.data.html);
			}
			else {
				DCartModalWidget.updateItemByHash(data.data.hash, data);
			}
		}
		DCartModalWidget.updateTotal(data);
	},
	
	_updateCount: function($count, hEvalCount) {
		if(!$count.length) { 
			return DCart.exit('Count input not found');
		}
		
		var count=+$count.val();
		
		if(typeof(hEvalCount) == 'function') {
			count=hEvalCount(count);
		}
		if(isNaN(count)) {
			$count.addClass('error');
			return false;
		}
		else if($count.is('.error')) $count.removeClass('error');
		
		var hash=$count.data('hash');
		return DCart.updateCount(hash, count, function(data) {
			if(data.success == true) {
				DCartModalWidget.updateItemByHash(hash, data);
				DCartModalWidget.updateTotal(data);
			}
			else {
				if(data.error) alert(data.error);
			}
		});
	}
}


