<?php
/**
 * Обратный звонок
*
* 1 Имя
* 3 Контактный телефон
*/
return array(
	'callback' => array(
		'title' => 'Обратный звонок',
		'short_title' => 'Обратный звонок',
		// Options
		'options' => array(
			'useCaptcha' => false,
			'sendMail' => true,
			'emptyMessage' => 'Заявок нет',
		),
		// Form attributes
		'attributes' => array(
			'name' => array(
				'label' => 'Имя',
				'type' => 'String', // String, Phone, Text, Checkbox, List
				'placeholder' => 'Ваше имя',
				'rules' => array(
					array('name', 'required')
				),
			),
			'phone' => array(
				'label' => 'Контактный телефон',
				'type' => 'Phone',
				'rules' => array(
					array('phone', 'required')
				),
			),
			'privacy_policy' => array(
				'label' => 'Нажимая на кнопку "Отправить", я даю согласие на ' . \CHtml::link('обработку персональных данных', '/privacy-policy', ['target'=>'_blank']),
				'type' => 'Checkbox',
				'rules' => array(
					array('privacy_policy', 'required')
				),
				'htmlOptions'=>['class'=>'inpt inpt-privacy_policy']
			),
		),
		// Control buttons
		'controls' => array(
			'send' => array(
				'title' => 'Отправить'
			),
		),
	),
);