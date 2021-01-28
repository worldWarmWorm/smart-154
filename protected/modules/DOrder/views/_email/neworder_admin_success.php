
<?php
$customer = $model->getCustomerData();
?>
<!DOCTYPE html PUBLIC "-//W3C//DTD HTML 4.01//EN" "http://www.w3.org/TR/1999/REC-html401-19991224/strict.dtd">
<html>
	<head>
		<meta http-equiv="Content-Type" content="text/html; charset=utf-8">
	</head>

	<body style="font-family: Arial, Helvetica, sans-serif; font-size: 12px; color: #000000;">
		<div style="width: 680px;">

			<p style="margin-top: 0px; margin-bottom: 20px;">
				<h2>Новый заказ с сайта "<?=\Yii::app()->name ?>"</h2>
			</p>

			<?php if ($model->id): ?>
			<p style="margin-top: 0px; margin-bottom: 20px;"><p>Заказ <?php echo '№'. $model->id; ?></p></p>
			<?endif;?>
			<?foreach($customer as $key=>$personal): if($key == 'paymentType') continue; if($key == 'privacy_policy') continue; ?>
				<table style="border-collapse: collapse; width: 100%; border-top: 1px solid #DDDDDD; border-left: 1px solid #DDDDDD; margin-bottom: 20px;">
					<thead>
						<tr>
							<td style="font-size: 12px; border-right: 1px solid #DDDDDD; border-bottom: 1px solid #DDDDDD; background-color: #EFEFEF; font-weight: bold; text-align: left; padding: 7px; color: #222222;"><?=$personal['label']?></td>
						</tr>
					</thead>
					<tbody>
						<tr>
							<td style="font-size: 12px;	border-right: 1px solid #DDDDDD; border-bottom: 1px solid #DDDDDD; text-align: left; padding: 7px;"><?=$personal['value']?></td>
						</tr>
					</tbody>
				</table>
			<?endforeach;?>

			<table style="border-collapse: collapse; width: 100%; border-top: 1px solid #DDDDDD; border-left: 1px solid #DDDDDD; margin-bottom: 20px;">
				<thead>
					<tr>
						<td style="font-size: 15px; border-right: 1px solid #DDDDDD; border-bottom: 1px solid #DDDDDD; background-color: #EFEFEF; font-weight: bold; text-align: center; padding: 10px; color: #222222;" colspan="12">Детали заказа</td>
					</tr>
				</thead>

				<thead>
					<tr>
						<td width="40%" style="font-size: 12px; border-right: 1px solid #DDDDDD; border-bottom: 1px solid #DDDDDD; background-color: #EFEFEF; font-weight: bold; text-align: left; padding: 7px; color: #222222;">Название</td>
						<td width="20%" style="font-size: 12px; border-right: 1px solid #DDDDDD; border-bottom: 1px solid #DDDDDD; background-color: #EFEFEF; font-weight: bold; text-align: right; padding: 7px; color: #222222;">Артикул</td>
						<td width="20%" style="font-size: 12px; border-right: 1px solid #DDDDDD; border-bottom: 1px solid #DDDDDD; background-color: #EFEFEF; font-weight: bold; text-align: right; padding: 7px; color: #222222;">Количество, шт.</td>
						<td style="font-size: 12px; border-right: 1px solid #DDDDDD; border-bottom: 1px solid #DDDDDD; background-color: #EFEFEF; font-weight: bold; text-align: right; padding: 7px; color: #222222;">Цена, руб</td>
						<td style="font-size: 12px; border-right: 1px solid #DDDDDD; border-bottom: 1px solid #DDDDDD; background-color: #EFEFEF; font-weight: bold; text-align: right; padding: 7px; color: #222222;">Итого, руб</td>
					</tr>
				</thead>

				<tbody>
				<?
				$total = 0;
					foreach($model->getOrderData() as $hash=>$attributes):
					$price = $attributes['price']['value'];
					$count = $attributes['count']['value'];
					$code = empty($attributes['code']['value']) ? '-' : $attributes['code']['value'];
					$total += $price*$count;
				?>
					<tr>
						<td width="40%" style="font-size: 12px; border-right: 1px solid #DDDDDD; border-bottom: 1px solid #DDDDDD; background-color: #EFEFEF; font-weight: bold; text-align: left; padding: 7px; color: #222222;"><?
							echo $attributes['title']['value'];
							if($eavAttributes) {
								$attrListContent='';
								foreach(\YiiHelper::arraySort($attributes, $eavAttributes) as $attribute=>$data) {
									if(!empty($data['label']) && (!empty($data['value']) || is_numeric($data['value']))) {
										$attrListContent.=\CHtml::tag(
											'li', 
											['style'=>'font-size: 9px;color: #222222;font-weight:normal;'], 
											'<span style="font-weight: bold;">'.$data['label'].':</span> '.$data['value']
										);
									}
								}
								if(!empty($attrListContent)) {
									echo \CHtml::tag('ul', ['style'=>'list-style:none;padding:0;margin:0;'], $attrListContent);
								}
							}
						?></td>
						<td width="10%" style="font-size: 12px; border-right: 1px solid #DDDDDD; border-bottom: 1px solid #DDDDDD; background-color: #EFEFEF; font-weight: bold; text-align: right; padding: 7px; color: #222222;"><?=$code?></td>
						<td width="10%" style="font-size: 12px; border-right: 1px solid #DDDDDD; border-bottom: 1px solid #DDDDDD; background-color: #EFEFEF; font-weight: bold; text-align: right; padding: 7px; color: #222222;"><?=$count?></td>
						<td style="font-size: 12px; border-right: 1px solid #DDDDDD; border-bottom: 1px solid #DDDDDD; background-color: #EFEFEF; font-weight: bold; text-align: right; padding: 7px; color: #222222;"><?=$price?></td>
						<td style="font-size: 12px; border-right: 1px solid #DDDDDD; border-bottom: 1px solid #DDDDDD; background-color: #EFEFEF; font-weight: bold; text-align: right; padding: 7px; color: #222222;"><?=$price*$count?></td>
					</tr>
					<?endforeach;?>
				</tbody>

				<tfoot>
					<tr>
						<td style="font-size: 12px;	border-right: 1px solid #DDDDDD; border-bottom: 1px solid #DDDDDD; text-align: right; padding: 7px;" colspan="4"><b>Итого:</b></td>
						<td style="font-size: 12px;	border-right: 1px solid #DDDDDD; border-bottom: 1px solid #DDDDDD; text-align: right; padding: 7px;"><?=$total?> руб.</td>
					</tr>
				</tfoot>
			</table>
		</div>
	</body>
</html>
