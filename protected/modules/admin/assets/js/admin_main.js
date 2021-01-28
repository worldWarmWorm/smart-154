jQuery(document).ready(function() {
    hideNonRequired();

    /* Блок быстрого изменения цены */
	$('.price').on('click', function(){
	    //Если кликнули на цену, берем текст цены и показываем редактор цены
	    var price = $(this).text();
	    $(this).siblings().show()
	    $(this).siblings().find('.price_val').val(price);
	});
	// Если нажимаем ОК
	// То меняется цена на экране, и создается аякс запрос с новой ценой и ID товара.
	$('.price_status').click(function(){
	    var parnts = $(this).parents('.price_change');
	    var z = $(this).parents('.price_cotainer_change');
	    z.find('input').val();
	    var id = 0;
	    var cost = 0;
	    parnts.children('.price').text($.trim(parseFloat(z.find('input').val())));
	    $.ajax({
	        url: "/cp/shop/productupdate",
	        data: { id: $(this).attr('data-id'), save: true, price: $.trim(parseFloat(z.find('input').val())) },
	        context: document.body
	        }).done(function() 
	        {
	        $( this ).addClass( "done" );
	    });
	    //Скрываем редактор
	    $('.price_status').parents('.price_cotainer_change').hide();
	    $('.price_cotainer_change').hide();
	});
	/* Конец блока быстрого изменения цены */
});

editorCallBack = function() {
    //hideSpollers();
};

hideNonRequired = function() {
    var spollers = $('.non-required', $('#content .form'));
    
    $(spollers).each(function(i, item) {
        var label = $(item).parent().find('label');
        $(label).click(function() {
            $(item).toggleClass('hidden');
        }).addClass('open-link');
    });
};


