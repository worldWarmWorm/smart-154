<?php
use common\components\helpers\HYii as Y;
use common\components\helpers\HArray as A;

Y::jsCore('cookie');

$categoriesListData=\Category::model()->getCategories();
//$canRemoveCategory=($model->isLeaf() && !$model->getProductsCount());

?>
<? $this->widget('\common\widgets\ui\flash\Yii', [
    'id'=>'SHOP_PRODUCT_MASSIVE_CONTROL_SUCCESS', 
    'htmlOptions'=>['class'=>'alert alert-success']
]); ?>
<div class="panel panel-default" id="category_controls">
    <div class="panel-heading">
        Панель управления товарами <a data-toggle="collapse" href="#category_controls_collapse">открыть</a>
        <small class="pull-right text-muted">
            <?= \CHtml::checkBox('disable_tooltips', A::get($_COOKIE, 'disable_tooltips', 0), ['style'=>'width:10px;height:10px']); ?>
            &nbsp;<label for="disable_tooltips" style="font-weight:normal;maring:0 0 0 0">не отображать подсказки</label>
        </small>
    </div>
    <div id="category_controls_collapse" class="panel-collapse collapse">
        <div class="panel-body" data-loading-text="Подождите, идет выполнение операции...">
            <div class="row">
                <div class="col-md-8">
                    <a data-js="check-all" href="javascript:;" class="btn btn-default">отметить все</a>
                    &nbsp;
                    <a data-js="uncheck-all" href="javascript:;" class="btn btn-default">снять все</a>
                    &nbsp;
                    <div class="checkbox inline">
                        <label for="forcy_all">
                            <input data-js="forcy-all" id="forcy_all" name="forcy_all" type="checkbox" value="1" />
                            Применить ко всем товарам
                        </label>
                    </div>
                </div>
                <div class="col-md-4">
                    <a data-js="btn-remove" href="javascript:;" data-toggle="tooltip" class="btn btn-danger pull-right">удалить отмеченные</a>
                </div>
            </div>
            <div class="controls__copy" style="margin-top:20px">
                <div class="row">
                    <div class="col-md-3">
                        Выберите категорию:
                    </div>
                    <div class="col-md-9">
                        <?= CHtml::dropDownList('category_id', $model->id, $categoriesListData, ['class'=>'form-control w50 inline', 'empty'=>'-- Корневая категория --']); ?>
                    </div>
                </div>
                <div class="row" style="margin-top:10px">
                    <div class="col-md-3">
                        Название подкатегории:
                    </div>
                    <div class="col-md-9">
                        <?= CHtml::textField('category_name', '', [
                            'class'=>'form-control w100 inline', 
                            'placeholder'=>'Введите название подкатегории',
                            'data-toggle'=>'tooltip',                            
                        ]); ?>
                    </div>
                </div>
                <div class="row" style="margin-top:10px">
                    <div class="col-md-9">
                        <a href="javascript:;" data-toggle="tooltip" data-js="btn-move" class="btn btn-primary inline">Перенести</a>
                        &nbsp;
                        <a href="javascript:;" data-toggle="tooltip" data-js="btn-copy" class="btn btn-primary inline">Скопировать</a>
                        &nbsp;
                        <a href="javascript:;" data-toggle="tooltip" data-js="btn-rel" class="btn btn-primary inline">Привязать</a>
                        &nbsp;
                        <a href="javascript:;" data-toggle="tooltip" data-js="btn-unrel" class="btn btn-danger inline">Отвязать</a>
                    </div>
                    <? /* <div class="col-md-3">
                        <?= \CHtml::link('Удалить категорию', 'javascript:;', [
                            'data-js'=>'btn-remove-category',
                            'data-toggle'=>'tooltip',
                            'class'=>'btn btn-danger pull-right'
                        ]); ?>
                    </div> */ ?>                    
                </div>
            </div>
        </div>
    </div>
</div>
<script>
    $(document).ready(function(){
        var $controls=$("#category_controls");
        $controls.on("click", "[data-js$='check-all']", onCheckAllClick);
        $controls.on("click", "[data-js^='btn']", onControlButtonClick);
        $controls.on("click", "[data-js='forcy-all']", onControlButtonForcyClick);
        $(document).on("click", "#disable_tooltips", onDisableTooltipsClick);
        <? if(!A::get($_COOKIE, 'disable_tooltips', 0)): ?>
        enableTooltips();
        <? endif; ?>
        function enableTooltips() {
            $controls.find("[name^='category_name']").tooltip({
                "trigger": "hover",
                "placement": "bottom",
                "title": 'Если название категории будет задано, действия будут выполнены для данной новой подкатегории',
                "container": "body"
            });
            $controls.find("[data-js='btn-move']").tooltip({
                "trigger": "hover",
                "placement": "left",
                "title": 'Переместить отмеченные товары в выбранную категорию',
                "container": "body"
            });
            $controls.find("[data-js='btn-copy']").tooltip({
                "trigger": "hover",
                "placement": "bottom",
                "title": 'Скопировать отмеченные товары в выбранную категорию',
                "container": "body"
            });
            $controls.find("[data-js='btn-rel']").tooltip({
                "trigger": "hover",
                "placement": "bottom",
                "title": 'Привязать выбранную категорию, как дополнительную, к отмеченным товарам',
                "container": "body"
            });
            $controls.find("[data-js='btn-unrel']").tooltip({
                "trigger": "hover",
                "placement": "right",
                "title": 'Удалить из дополнительных категорий, для отмеченных товаров, выбранную категорию',
                "container": "body"
            });
            $controls.find("[data-js='btn-remove']").tooltip({
                "trigger": "hover",
                "placement": "bottom",
                "title": 'Удалить отмеченные товары',
                "container": "body"
            });
        }
        <? /* if(!$canRemoveCategory): ?>
        $(document).on("mouseover", "#category_controls [data-js='btn-remove-category']", function(e){$(e.target).tooltip('show');});
        $(document).on("mouseout", "#category_controls [data-js='btn-remove-category']", function(e){$(e.target).tooltip('hide');});
        $controls.find("[data-js='btn-remove-category']").tooltip({
            "trigger": "hover",
            "placement": "bottom",
            "title": 'Удалить категорию можно только, если она является последней вложенной и в ней нет товаров.',
            "container": "body"
        });
        <? endif; */ ?>
        function onControlButtonForcyClick(e) {
            if($(e.target).closest(":checkbox").prop("checked")) {
                return confirm("Подтвердите выполнение массового действия над всеми товарами в данной категории");
            }
            return true;
        }
        function disableTooltips() {
            $controls.find("[data-toggle='tooltip']").each(function(){$(this).tooltip("destroy");});
        }
        function onDisableTooltipsClick(e) {
            var checked=$(e.target).closest(":checkbox").is(":checked") ? 1 : 0;
            $.cookie('disable_tooltips', checked, {path:"/"});
            if(checked) disableTooltips();
            else enableTooltips();
        }
        function onCheckAllClick(e) {
            $("#product-list .product .checkbox :checkbox").prop("checked", $(e.target).data("js")=="check-all");
        }
        function isForcy() {
            return $controls.find("[data-js='forcy-all']").prop("checked") ? 1 : 0;
        }
        function onControlButtonClick(e) {
            var $panel=$controls.find(".panel-body");
            var action=$(e.target).data("js").replace(/^[^-]+-/, '');
            var products=[];
            $("#product-list .product .checkbox :checkbox:checked").each(function(){
                products.push($(this).val());
            });
            e.preventDefault();
            if(isForcy() || (products.length > 0)) {
				if(confirm("Подтвердите выполнение массового действия над товарами")){
	                $panel.button("loading");
    	            $.post("<?=$this->createUrl('/cp/shop/productMassiveControl')?>", {
        	            action: action,
            	        from_category_id: <?= ($id=$model->id) ? $id : 'false'; ?>,
                	    category_id: $controls.find("[name='category_id']").val(),
                    	category_name: $controls.find("[name='category_name']").val(),
                        forcy: isForcy(),
	                    products: products
    	            }, function(response) {
        	            if(response.success) {
            	            window.location.reload();
                	    }
                    	else {
	                        $panel.button("reset");
    	                }
        	        }, "json");
				}
            }
            else {
                alert("Необходимо выбрать хотя бы один товар");
            }
        }
    });
</script>
