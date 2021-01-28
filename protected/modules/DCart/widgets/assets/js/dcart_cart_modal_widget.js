/**
 * Скрипт инициализация для виджета корзины (DCart\widgets\CartWidget)
 */
$(function() {
	$(document).on('keyup', '.js-dcart-mcart .js-mcart-item .count .number input[name="count"]', DCartModalWidget.updateCount);
	$(document).on('click', '.js-dcart-mcart .js-mcart-item .count .number .down', DCartModalWidget.countDown);
	$(document).on('click', '.js-dcart-mcart .js-mcart-item .count .number .up', DCartModalWidget.countUp);
	$(document).on('click', '.js-dcart-mcart .js-mcart-item .js-mcart-btn-remove', DCartModalWidget.remove);
});