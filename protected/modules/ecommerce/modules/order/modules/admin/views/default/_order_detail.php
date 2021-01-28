<?php
/** @var \ecommerce\modules\order\modules\admin\controllers\DefaultController $this */
/** @var \ecommerce\modules\order\models\Order $order */
$customer=$order->getCustomerData();
?>
<tr class="order-detail" id="order-detail-<?=$order->id?>" data-id="<?=$order->id?>">
    <td colspan="7" style="padding:3px;color:#000;" class="bg-primary">
        <table class="table table-striped table-bordered" style="margin-bottom:0;">
            <thead>
                <tr class="bg-info" style="font-size:0.9em">
                    <th>&nbsp;</th>
                    <th>Наименование</th>
                    <th class="col-price">Цена</th>
                    <th class="col-count">Кол-во</th>
                    <th class="col-sum">Сумма</th>
                </tr>
            </thead>
            <tbody>
            <? 
            foreach ($order->getOrderData() as $hash=>$attributes): 
                $productId=$attributes['id']['value'];
                $itemLink=\Yii::app()->createUrl('/shop/product', array('id'=>$productId));
                $img=\CHtml::image($attributes['image']['value'] ?: 'http://placehold.it/48', '');
            ?>
            <tr>
                <td class="col-image">
                    <?= \CHtml::link($img, $itemLink, ['target'=>'_blank']); ?>
                </td>
                <td class="col-info">
                    <?=$attributes['title']['value']?><br />
                    <?$i=0; foreach($attributes as $attribute=>$data): 
                        if($data['value'] && !in_array($attribute, array('id', 'model', 'categoryId', 'price', 'count', 'title', 'privacy_policy', 'image'))):?>
                        <?=$i++?' / ':''?><small><b><?=$data['label']?>:</b> <?=$data['value']?></small>
                        <?endif; 
                    endforeach; ?>
                </td>
                <td class="col-price"><?=$attributes['price']['value']?> р.</td>
                <td class="col-count"><?=$attributes['count']['value']?></td>
                <td class="col-sum"><?=$attributes['count']['value'] * $attributes['price']['value']?> р.</td>
            </tr>
            <? endforeach; ?>
            </tbody>
            <tfoot>
                <tr>
                    <td colspan="5" class="col-comment">
                        <textarea name="comment"><?=$order->comment ?: @$customer['comment']['value']?></textarea>
                    </td> 
                </tr>
            </tfoot>
        </table>
    </td>
</tr>
