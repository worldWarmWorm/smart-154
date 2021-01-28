<?
/* @var \DOrder\models\DOrder $model */
use common\components\helpers\HYii as Y;
use common\components\helpers\HArray as A;

$formId=uniqid('ym');
$shopId=Y::param('payment.ym.shopId');
$scid=Y::param('payment.ym.scid');
$totalPrice=$model->getTotalPrice();
$customerData=$model->getCustomerData();
$orderData=$model->getOrderData();
?>
<meta http-equiv="Content-Type" content="text/html; charset=utf-8">
<br><br>
<form id="<?=$formId?>" method="POST" action="https://money.yandex.ru/eshop.xml">
	<input type="hidden" name="shopId" value="<?=$shopId?>"/>
	<input type="hidden" name="scid" value="<?=$scid?>"/>
	<input value="Заказ #<?php echo $model->id; ?>" type="hidden" name="customerNumber" size="64"/>
	<input value="<?=preg_replace('/[^\d+]/', '', A::rget($customerData, 'phone.value'))?>" type="hidden" name="cps_phone" size="64"/>
	<input value="<?=$totalPrice?>" type="hidden" name="sum" size="64"/>
	<input name="paymentType" value="<?=A::rget($customerData, 'paymentType.value')?>" type="hidden"/>
	<input value="<?=A::rget($customerData, 'name.value')?>" type="hidden" name="custName" size="43"/>
	<input value="<?=A::rget($customerData, 'address.value')?>" type="hidden" name="custAddr" size="43"/> 
	<input type="hidden" name="custEmail" value="<?=A::rget($customerData, 'email.value')?>" size="43"/>
	<div style="display: none">
	<textarea rows="10" name="orderDetails" cols="34"><? 
        foreach($orderData as $item) {
        	$count=(int)A::rget($item, 'count.value', 0);
            echo '<p>'.A::rget($item, 'title.value').' x '.$count .' = '.A::rget($item, 'price.value')*$count.' РУБ.</p>\n';
        }
    ?></textarea>
	</div>
	<p>Сейчас вы будете перенаправлены на сайт сервиса <b>Яндекс.Касса</b> для продолжения платежа. Если этого не произошло нажмите "Оплатить".</p>
	<input type=submit value="Оплатить" class="payment-ym-button"><br> 
</form>
<script>setTimeout(function() { $("#<?=$formId?>").submit(); }, 5000);</script>
