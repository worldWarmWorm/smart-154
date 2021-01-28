<?
use YiiHelper as Y;
use AttributeHelper as A;
?>
<div class="form">
<?if(Yii::app()->user->hasFlash('error')) echo CHtml::tag('span', array('class'=>'error'), Yii::app()->user->getFlash('error'));?>
<?if(Yii::app()->user->hasFlash('success')) echo CHtml::tag('span', array('class'=>'success'), Yii::app()->user->getFlash('success'));?>
<form method="post">
<table class="system-settings" cellpadding="0" cellspacing="0" border="0">
	<thead>
		<tr>
			<th>Название модуля</th>
			<th>Активировать</th>
		</tr>
	</thead>
	<tbody>
		<?php 
		$modules = array(
			'feedback'=>'Обратная связь', 
			'question'=>'Вопрос-ответ', 
			'shop'=>'Магазин', 
			'slider'=>'Слайдер',
			'gallery'=>'Фотогалерея',
			'sale'=>'Акции',
			'reviews'=>'Отзывы'
		); 
		foreach($modules as $name=>$title):?>
			<tr>
				<td><?=$title?></td>
				<td align=center><?=\CHtml::checkBox("modules[{$name}]", D::yd()->isActive($name),array('onclick'=>D::yd()->isActive($name)?'return false':'return true'))?></td>
			</tr>
		<?endforeach?>
	</tbody>
</table>
<?/*
<br />
<h2>Домен</h2>
<table class="system-settings" cellpadding="0" cellspacing="0" border="0">
	<tbody>
		<tr>
			<td><?=CHtml::textField('domain', Y::request()->getPost('domain',Yii::app()->request->serverName))?></td>
		</tr>
	</tbody>
</table>
<br />
<table class="system-settings" cellpadding="0" cellspacing="0" border="0">
	<tbody>
		<tr>
			<td>Логин</td>
			<td><?=CHtml::textField('l', Y::request()->getPost('l'))?></td>
		</tr>
		<tr>
			<td>Пароль</td>
			<td><?=CHtml::passwordField('p');?></td>
		</tr>
	</tbody>
</table>
*/?>
<br />
<?=\Chtml::submitButton('Применить', array('class'=>'default-button'))?>
</form>
</div>
