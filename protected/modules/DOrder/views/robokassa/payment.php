<?php
/* @var \DOrder\models\DOrder $model */
use common\components\helpers\HYii as Y;
use \DOrder\ext\Robokassa\Robokassa;

$formId=uniqid('rb');
$url=Y::param('payment.robokassa.url');
$login=Y::param('payment.robokassa.login');
$pwd1=Y::param('payment.robokassa.password1');
$totalPrice=$model->getTotalPrice();
?>
<h1>Оплата</h1>

<div class="form">
    <p>Сейчас вы будете перенаправлены на сайт сервиса <a target="_blank" href="http://robokassa.ru">Робокасса</a> для выбора способа оплаты. Если этого не произошло нажмите "Перейти к оплате".</p>
    <form id="<?=$formId?>" action="<?=$url?>" method="post">
        <input type="hidden" name="Encoding" value="utf-8" />
        <input type="hidden" name="Culture" value="ru" />
        <input type="hidden" name="MrchLogin" value="<?=$login?>" />
        <input type="hidden" name="OutSum" value="<?=$totalPrice?>" />
        <input type="hidden" name="InvId" value="<?=$model->id?>" />
        <input type="hidden" name="Desc" value="Оплата заказа #<?=$model->id?>" />
        <input type="hidden" name="SignatureValue" value="<?=Robokassa::generateSignature($totalPrice, $model->id, $login, $pwd1)?>" />
        <div class="row buttons1">
            <p>Сумма к оплате: <span style="font-weight: bold"><?= $totalPrice; ?> руб</span></p>
            <input type="submit" value="Перейти к оплате" class="payment-button" />
        </div>
    </form>
</div>
<script>setTimeout(function() { $("#<?=$formId?>").submit(); }, 5000);</script>
