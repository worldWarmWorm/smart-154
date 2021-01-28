<?php
/** @var \feedback\controllers\AjaxController $this */
/** @var \feedback\components\FeedbackFactory $factory */
/** @var \feedback\models\FeedbackModel $model */
?>
<!DOCTYPE html PUBLIC "-//W3C//DTD HTML 4.01//EN" "http://www.w3.org/TR/1999/REC-html401-19991224/strict.dtd">
<html>
	<head>
		<meta http-equiv="Content-Type" content="text/html; charset=utf-8">
	</head>

	<body style="font-family: Arial, Helvetica, sans-serif; font-size: 12px; color: #000000;">
		<div style="width: 680px;">

			<p style="margin-top: 0px; margin-bottom: 20px;">
				<h1>Новый вопрос с сайта <a href="http://<?php echo \Yii::app()->request->serverName; ?>" target="_blank"><?php \ModuleHelper::getParam('sitename'); ?></a></h1>
			</p>
			
			<div>
				<p>Со страницы: <?= \CHtml::link($_SERVER['HTTP_REFERER'], $_SERVER['HTTP_REFERER'], ['target'=>'_blank']); ?></p>
			</div>

			<?foreach(['username', 'question'] as $name): ?>
				<table style="border-collapse: collapse; width: 100%; border-top: 1px solid #DDDDDD; border-left: 1px solid #DDDDDD; margin-bottom: 20px;">
					<thead>
						<tr>
							<td style="font-size: 12px; border-right: 1px solid #DDDDDD; border-bottom: 1px solid #DDDDDD; background-color: #EFEFEF; font-weight: bold; text-align: left; padding: 7px; color: #222222;"><?=$model->getAttributeLabel($name)?></td>
						</tr>
					</thead>
					<tbody>
						<tr>
							<td style="font-size: 12px;	border-right: 1px solid #DDDDDD; border-bottom: 1px solid #DDDDDD; text-align: left; padding: 7px;"><?= \CHtml::encode($model->$name); ?></td>
						</tr>
					</tbody>
				</table>
			<?endforeach;?>
		</div>
	</body>
</html>
