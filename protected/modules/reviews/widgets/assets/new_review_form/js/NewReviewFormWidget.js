/**
 * Kontur.Reviews.NewReviewFormWidget javascript class.
 * Javascript for \reviews\widgets\NewReviewForm widget
 * 
 * @use js/kontur/common/classes/Kontur.js
 * @use js/kontur/reviews/classes/Reviews.js
 * 
 */
window.NewReviewFormWidget=(function() {
    /**
     * @var object this.
     */
    var _this=this;
    
    _this.form="#review-add-form";
    _this.tryCount=3;
    _this.options={};
    
    _this.submitAddForm=function(form, data, hasError) {
        if (!hasError) {
            if(_this.tryCount-- > 0) {
                $.post($(_this.form).attr('action'), $(_this.form).serialize(), function(data) {
                    if (data.success) {
                        $(_this.form).parent().html(_this.options["w_nrf_mgs_success"]);
                    }
                    else {
                        $(_this.form).find("[data-js='result-errors']").html(_this.options["w_nrf_mgs_error"]).show();
                    }
                }, "json");
            }
            else {
                $(_this.form).find("[data-js='buttons']").html(_this.options["w_nrf_mgs_error_max_try"]).show();
            }
        }
    };
    
    /**
     * Инициализация
     */
    _this.init=function(options) {
        _this.options=options;
        $("[data-js='add-review']").fancybox();
    };

    return _this;
})();
