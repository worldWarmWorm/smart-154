/**
 * Feedback frontend script 
 */
var FeedbackWidget = {
	/**
	 * @var string error success
	 */
	msgSuccess: "Ваша заявка принята.",
	
	/**
	 * @var string error message
	 */
	msgError: "Произошла ошибка на сервере, повторите подачу заявки позже.",
	
	/**
	 * Initialization.
	 */
	init: function(formId) {
	},

	/**
	 * Clone
	 * @link http://www.askdev.ru/javascript/53/%D0%9A%D0%B0%D0%BA-%D0%B2-JavaScript-%D0%BA%D0%BB%D0%BE%D0%BD%D0%B8%D1%80%D0%BE%D0%B2%D0%B0%D1%82%D1%8C-%D0%BE%D0%B1%D1%8A%D0%B5%D0%BA%D1%82/
	 */
	clone: function(obj) {
		if(obj == null || typeof(obj) != 'object')
			return obj;
		var temp = new obj.constructor(); 
		for(var key in obj)
			temp[key] = this.clone(obj[key]);
		return temp;
	},
	
	/**
	 * After validate handler
	 * @see Yii \CActiveForm
	 */
	afterValidate: function (form, data, hasError) 
	{
		$(form).find(".feedback-body").removeClass("success");
	    if (hasError) {
	    	$(form).find(".inpt-error").removeClass("inpt-error");
	    	for(var key in data) {
	    		if(key.indexOf('feedback') === 0) {
	    			$(form).find("#"+key).addClass("inpt-error");
	    		}
	    	}
	    }
	    else {
	    	$(form).find(".feedback-submit-button").hide();
			$.post($(form).attr("action"), form.serialize(), function(json) {
				if(json.success){$(form).find(".feedback-body").addClass("successed");$(form).trigger('cms.feedback.sended');}
				(function(){
                  	$(form).find(".inpt-error").removeClass("inpt-error");
                    var $body=$(form).find(".feedback-body");
                  	var html=$body.html();
	        		$body.html((json.message != undefined) ? json.message : ((json.success == true) ? FeedbackWidget.msgSuccess : FeedbackWidget.msgError));
                  	setTimeout(function(){
                        $body.removeClass("successed");
                        $body.html($.parseHTML(html));
                        if($body.find("[class*='phone']").length) {
                            $body.find("[class*='phone']").mask('+7 ( 999 ) 999 - 99 - 99');
                        }
                        $body.find(".feedback-submit-button").show();
                    },7000);
                })();
		    }, "json");
	    }
	    
	    return false;
	}  
}
