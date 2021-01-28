<?php
/** @var \ecommerce\modules\order\modules\admin\controllers\DefaultController $this */
/** @var \CActiveDataProvider[\ecommerce\modules\order\models\Order] $dataProvider */
use common\components\helpers\HYii as Y;
use common\components\helpers\HHtml;

Y::js(false, 'window.ecommerce_modules_order_modules_admin_controllers_DefaultController.init({
    box: "#ecommerce-orders-grid",
    complete: ".mark",
    paid: ".btn-paid",
    order_title: ".order-title"
});', \CClientScript::POS_READY);

$t=Y::ct('\ecommerce\modules\order\modules\admin\AdminModule.controllers/default', 'ecommerce');

$dateRangeFilter=$this->widget('zii.widgets.jui.CJuiDatePicker', [
    'model'=>$dataProvider->model,
    'attribute'=>'filter_create_time_from',
    'language'=>'ru',
    'value'=>'',
    'options'=>[
        'showAnim'=>'fold',
        'dateFormat'=>'dd.mm.yy',
        'changeMonth' => 'true',
        'changeYear'=>'true',
        'constrainInput' => 'false',
    ],
    'htmlOptions'=>['style'=>'height:20px;width:80px;','placeholder'=>'от даты']
], true) 
. $this->widget('zii.widgets.jui.CJuiDatePicker', [
    'model'=>$dataProvider->model,
    'attribute'=>'filter_create_time_to',
    'language'=>'ru',
    'value'=>'',
    'options'=>[
        'showAnim'=>'fold',
        'dateFormat'=>'dd.mm.yy',
        'changeMonth' => 'true',
        'changeYear'=>'true',
        'constrainInput' => 'false',
    ],
    'htmlOptions'=>['style'=>'height:20px;width:80px;','placeholder'=>'до даты']
], true);

$orderHashFilter=false;
if((bool)Y::module('ecommerce.ykassa')) {
    $orderHashFilter=\CHtml::activeTextField($dataProvider->model, 'hash', ['placeholder'=>'Номер заказа в Яндекс.Кассе']);
}

$columns=[
    [
        'header'=>'№',
        'name'=>'id',
        'headerHtmlOptions'=>['style'=>'width:50px;text-align:center;', 'class'=>'bg-info'],
        'htmlOptions'=>['style'=>'text-align:center;cursor:pointer', 'class'=>'col-order-id'],
    ],
    [
        'type'=>'raw',
        'filter'=>$orderHashFilter,
        'header'=>'ФИО, контакты',
        'headerHtmlOptions'=>['class'=>'bg-info'],
        'name'=>'hash',
        'value'=>function($data, $index, $column) {
            $html.=\CHtml::link('Заказ №' . $data->id, 'javascript:;', ['class' => 'order-title']) . '<br/>';
            if((bool)Y::module('ecommerce.ykassa')) {
                $html.='<span style="text-decoration:underline">Номер заказа в Яндекс.Кассе: ' . $data->hash . '</span><br/>';
            }
            
            $customer=$data->getCustomerData();
            foreach($customer as $k=>$f) {
                if($k == 'delivery_type') {
                    $html.='<span><em>' . $f['label'] . ':</em> ';
                    $html.=$column->grid->controller->widget(
                        '\DOrder\widgets\delivery\DeliveryTypeView', 
                        ['value'=>$f['value'], 'orderId'=>$data->id],
                        true
                    );
                }
                elseif($f['value'] && !in_array($k, ['comment', 'privacy_policy'])) {
                    $html.='<span><em>' . $f['label'] . ':</em> ' . nl2br(\CHtml::encode($f['value'])) . '</span><br/>';
                }
            }
            return $html;
        }
    ],
    [
        'filter'=>false,
        'type'=>'raw',
        'header'=>'Сумма',
        'name'=>'id',
        'headerHtmlOptions'=>['style'=>'width:120px;text-align:center;', 'class'=>'bg-info'],
        'htmlOptions'=>['style'=>'text-align:center;vertical-align:middle;'],
        'value'=>function($data, $index, $column) {
            $html=HHtml::price($data->getTotalPrice()) . ' р.';
            $deliveryPrice=0;
            if($deliveryPrice) {
                $html.='<div style="font-weight:bold;margin-top:10px;font-size:0.8em">с доставкой:</div>';
                $html.=HHtml::price($data->getTotalPrice() + $deliveryPrice) . ' р.';
            }
            return $html;
        }
    ],
    [
        'filter'=>$dateRangeFilter,
        'type'=>'raw',
        'header'=>'Дата',
        'name'=>'create_time',
        'headerHtmlOptions'=>['style'=>'width:100px;text-align:center;', 'class'=>'bg-info'],
        'htmlOptions'=>['style'=>'text-align:center;vertical-align:middle;'],
        'value'=>'\common\components\helpers\HYii::formatDate($data->create_time)'
    ],
    [
        'filter'=>[0=>'В обработке', 1=>'Завершен'],
        'header'=>'Статус',
        'name'=>'completed',
        'headerHtmlOptions'=>['style'=>'width:30px;text-align:center;', 'class'=>'bg-info'],
        'htmlOptions'=>['style'=>'text-align:center;vertical-align:middle;'],
        'type'=>'raw',
        'value'=>'\CHtml::tag("div", ["class"=>"mark ".($data->completed ? "unmarked" : "marked")])'
    ]
];

if((bool)Y::module('ecommerce.ykassa')) {
    $columns[]=[
        'filter'=>[0=>'Нет', 1=>'Да'],
        'header'=>'Оплачен',
        'name'=>'paid',
        'type'=>'raw',
        'headerHtmlOptions'=>['style'=>'width:30px;text-align:center;', 'class'=>'bg-info'],
        'htmlOptions'=>['style'=>'text-align:center;vertical-align:middle;'],
        'value'=>function($data, $index, $column) {
            if($data->in_paid) {
                return \CHtml::tag("span", ["data-js"=>"order-paid-".$data->id, "class"=>"label label-warning"], "в процессе");
            }
            else {
                return \CHtml::tag("span", 
                    ["data-js"=>"order-paid-".$data->id, "class"=>"label label-".($data->paid ? "success" : "danger")], 
                    ($data->paid ? "Да" : "Нет")
                ) . "<br/><br/>" . \CHtml::button("изменить", ["class"=>"btn-paid btn btn-xs btn-default"]);
            }
        }
    ];
}

$columns[]=[
    'class'=>'\CButtonColumn',
    'template'=>'{delete}',
    'updateButtonImageUrl'=>false,
    'deleteButtonImageUrl'=>false,
    'buttons'=>[
        'delete'=>[
            'label'=>'Удалить',
            'url'=>'"/admin/order/delete/{$data->id}"',
            'options'=>['class'=>'btn btn-danger btn-xs']
        ]
    ],
    'htmlOptions'=>['style'=>'text-align:center;vertical-align:middle;'],
    'headerHtmlOptions'=>['class'=>'bg-info'],
];

$this->widget('zii.widgets.grid.CGridView', [
	'id'=>'ecommerce-orders-grid',
	'dataProvider'=>$dataProvider,
    'itemsCssClass'=>'table table-striped table-bordered table-hover items_sorter',
	'filter'=>$dataProvider->model,
    'pagerCssClass'=>'pagination',
    'rowHtmlOptionsExpression'=>'["data-id"=>$data->id]',
    'summaryText'=>'Заказы {start}—{end} из {count}.',    
    'afterAjaxUpdate'=>'function() {
        $("#ecommerce_modules_order_models_Order_filter_create_time_from").datepicker($.extend(
            {showMonthAfterYear:false}, 
            $.datepicker.regional["ru"], 
            {"showAnim":"fold","dateFormat":"dd.mm.yy","changeMonth":"true","showButtonPanel":"true","changeYear":"true","constrainInput":"false"}
        ));
        $("#ecommerce_modules_order_models_Order_filter_create_time_to").datepicker($.extend(
            {showMonthAfterYear:false}, 
            $.datepicker.regional["ru"], 
            {"showAnim":"fold","dateFormat":"dd.mm.yy","changeMonth":"true","showButtonPanel":"true","changeYear":"true","constrainInput":"false"}
        ));
    }',
	'columns'=>$columns,
]); 
?>
