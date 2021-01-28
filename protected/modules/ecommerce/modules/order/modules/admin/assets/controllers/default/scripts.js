/**
 * Скрипт для \ecommerce\modules\order\modules\admin\controllers\DefaultController
 */
window.ecommerce_modules_order_modules_admin_controllers_DefaultController=(function(){
    var _this={
        options: {}
    };    
    var $box;
    
    // @function получить значение переменной
    function v(obj, prop, def) {
        if((typeof(obj)!="undefined") && (typeof(obj[prop])!="undefined")) return obj[prop];
        return (typeof(def)=="undefined") ? null : def;
    }
    // @function получить элемент по data-js
    function j(name, filter) {
        $elm=$box.find("[data-js='"+name+"']");
        if($elm && (typeof(filter)!="undefined")) return $elm.filter(filter);
        return $elm;
    }
    // @function получить значение опции
    function o(name) {
        var value=_this.options;
        name.split(".").forEach(function(name){value=v(value,name);});
        return value;
    }
    // @function получить элемент поля атрибута модели
    function jm(attribute) {
        return $('#'+o("model_name") + "_" + attribute);
    }
    // @function получить элемент поля атрибута модели
    function m(attribute) {
        return '#'+o("model_name") + "_" + attribute;
    }
    // @function получить основной элемент-родитель записи
    function parent(e) {
        return $(e.target).parents("tr[data-id]:first");
    }
    
    /**
     * Инициализация
     * 
     * @param options параметры
     * "box": string 
     * "complete": string
     * "paid": string
     * "order_title": string
     */
    _this.init=function(options) {
        _this.options=options;
        $box=$(o("box"));

        $(document).on("click", o("box") + " " + o("complete"), _this.onChangeCompleted);
        $(document).on("click", o("box") + " " + o("paid"), _this.onChangePaid);
        $(document).on("click", o("box") + " " + o("order_title"), _this.onOrderTitleClick);
        $(document).on("click", o("box") + " .col-order-id", _this.onOrderTitleClick);
        $(document).on("blur", o("box") + " textarea[name='comment']", _this.onCommentBlur);
    };
    
    /**
     * Открытие данных заказа
     */
    _this.onOrderTitleClick=function(e) {
        e.preventDefault();
        var $parent=parent(e);
        var $detail=$parent.siblings("#order-detail-"+$parent.data("id"));
        if($detail.length) {
            $detail.toggle();
        }
        else {
            $.post("/admin/order/detail", {item: $parent.data("id")}, function(response){
                if(response.success) {
                    $parent.after($.parseHTML(response.data.html));
                }
            }, "json");            
        }
    }
    
    /**
	 * Изменение статуса заказа "Обработан"
	 */
	_this.onChangeCompleted=function(e) {
        e.preventDefault();
		var $t=$(this);
        $.ajax({
			type: "POST",
			url: "/admin/order/changeCompleted",
			data: {item: parent(e).data("id")},
			dataType: "json",
			success: function(json) {
				if(json.success) {
					if(!json.data.status) {
						$t.removeClass('unmarked');
					} else {
						$t.addClass('unmarked');
					}
					$('.dorder-order-button-widget-count').text(json.data.count);
				}
			}
        });
        return false;
	};
    
    /**
	 * Сохранение комментария
	 */
	_this.onCommentBlur=function(e) {
        e.preventDefault();
        var $comment=$(e.target);
        $.post(
            "/admin/order/updateComment", 
            {item: parent(e).data("id"), comment: $comment.val()}, 
            function(response) {
                if(!response.success) { 
                    $comment.addClass("bg-danger");
                    setTimeout(function(){alert("Изменения в комментарии не сохранены")}, 200); 
                }
                else {
                    $comment.addClass("bg-success");
                }
                setTimeout(function(){$comment.removeClass("bg-danger bg-success");}, 1000);
            }, 
            "json"
        );
        return false;
	};
    
    _this.onChangePaid=function(e) {
        e.preventDefault();
        var id=parent(e).data("id");
        if(confirm("Подтвердите изменение статуса платежа заказа #" + id)) {
            $.post("/admin/order/changePaid", {item: id}, function(response){
                if(response.success) {
                    var $o=$("[data-js='order-paid-"+id+"']");
                    $o.removeClass(response.data.paid ? "label-danger" : "label-success");
                    $o.addClass(response.data.paid ? "label-success" : "label-danger");
                    $o.text(response.data.paid ? "Да" : "Нет");
                }
            }, "json");
        }
        return false;
    };
    
    return _this;
})();
