---------------------------
Использование модуля DOrder
---------------------------
Подключение оплаты:
1) (protected\config\urls.php) Добавить необходимые:
    'payment/robokassa/<hash>'=>'payment/rb',
    'pay/payment_success'=>'payment/rbSuccess',
    'pay/payment_fail'=>'payment/rbFail',

    'payment/ym/<hash>'=>'payment/ym',
    'pay/payment_success'=>'payment/ymSuccess',
    'pay/payment_fail'=>'payment/ymFail',

2)(protected\config\main.php)
     'params'=>[
        'payment'=>[
            'action'=>'/payment/ym',
            // 'action'=>'/payment/robokassa',
            'robokassa'=>[
                'login'=>'<login>',
                'password1'=>'<password1>',
                'password2'=>'<password1>',
                //'url'=>'http://test.robokassa.ru/Index.aspx',
                'url'=>'https://merchant.roboxchange.com/Index.aspx',
            ],
            'ym'=>[
                'shopId'=>'<shopId>',
                'scid'=>'<scid>',
            ],
            'types'=>[
                1=>'Наличными курьеру при получении (г. Новосибирск)',
                2=>'On-line оплата',
                3=>'Наложенным платежом, Почтой России'
            ],
            'online'=>[2]
        ]
    ]

В параметре payment.online=>array() перечиляются типы платежей, которые относятся к онлайн-оплате.
Например, для Яндекс.Кассы может быть задано:
'types'=>[
	1=>'Наличными курьеру при получении (г. Новосибирск)',
    'AC'=>'Оплатить банковской картой',
    'SB'=>'Оплата через сбербанк-онлайн',
    'PC'=>'Электронные деньги'
],
тогда значение параметра должно быть таким:
'online'=>['AC','PC','SB']

3) Расскомментрировать payment в CustomerForm
4) Скопировать DOrder\install\protected\controllers\PaymentController. Расскомментрировать необходимое.
5) Установить сценарий protected\modules\DOrder\widgets\actions\OrderWidget.php
$customerForm = new CustomerForm('payment'); 

------------------------ Раздел администрирования ------------------------ 
1. Виджет кнопки "Заказы" для перехода к списку заказов. 
<?php $this->widget('\DOrder\widgets\admin\OrderButtonWidget'); ?>

Можно добавить в (protected\modules\admin\views\layouts\main.php)

