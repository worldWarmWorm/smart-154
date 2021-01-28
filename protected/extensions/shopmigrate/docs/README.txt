Расширение ипорта/экспорта каталога
(БЕЗ РЕГИОНОВ)

------------------------------
ПОДКЛЮЧЕНИЕ
------------------------------
Требования: PHP 5.4
1) Модуль common (без подмодулей) версии от 2.5.2 (при необходимости обновить)
Расширение работает при следующих настройках подключении модуля common
'common'=>[
    'registerJsClasses'=>false,
    'registerFancybox'=>false
]

2) Добавить действие в ShopController
public function actions()
{
    return \CMap::mergeArray(parent::actions(), [
        'migrate'=>[
            'class'=>'\ext\shopmigrate\actions\YmlMigrateAction'
        ]
    ]);
}

3) Добавить пункт меню
(в новой версии) config/menu.php
['label'=>'Импорт/экспорт каталога','url'=>['/cp/shop/migrate']],

(в старых версиях) view/layouts/main.php
$shopMenuItems[]=['label'=>'', 'itemOptions'=>['class'=>'divider'], 'visible'=>'divider'];
$shopMenuItems[]=['label'=>'Импорт/экспорт каталога','url'=>['/cp/shop/migrate']];

