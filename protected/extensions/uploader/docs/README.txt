Добавить в модель атрибут filehash
public $filehash;
в rules() ['filehash', 'safe'],

В раздел администрирования в контроллер:
public function actions()
{
	return \CMap::mergeArray(parent::actions(), [
		'clearFiles'=>[
			'class'=>'\ext\uploader\actions\ClearAction',
			'models'=>['\Question'=>'filehash']
		]
	]);
}

public function filters()
{
	return \CMap::mergeArray(parent::actions(), [
		'ajaxOnly +clearFile'
	]);
}

в публичной части в контроллер
public function actions()
{
	return \CMap::mergeArray(parent::actions(), [
		'uploadFile'=>'\ext\uploader\actions\UploadFileAction',
		'deleteFile'=>'\ext\uploader\actions\DeleteFileAction',
	]);
}

public function filters()
{
	return \CMap::mergeArray(parent::actions(), [
		'ajaxOnly +uploadFile, deleteFile'
	]);
}

в форму
<?php $this->widget('\ext\uploader\widgets\UploadField', [
	'form'=>$form,
	'model'=>$model,
	'attribute'=>'filehash',
	'uploadUrl'=>'/mycontroller/uploadFile',
	'deleteUrl'=>'/mycontroller/deleteFile',
]); ?>

в раздел администрирования 
кнопка очистки временных файлов
<? $this->widget('\ext\uploader\widgets\ClearButton', [
	'clearUrl'=>'/cp/mycontroller/clearFiles', 
	'htmlOptions'=>['class'=>'btn btn-warning pull-right']
]); ?>

для отображения списка файлов
<? $this->widget('\ext\uploader\widgets\FileList', ['hash'=>$model->filehash]); ?>

=== Рецепты ===
--- для раздела администрирования ---
модель 
public function getFileHash()
{
	return md5(get_class($this) . "_{$this->id}");
}
контроллер
public function actions()
{
	return \CMap::mergeArray(parent::actions(), [
		'uploadFile'=>['class'=>'\ext\uploader\actions\UploadFileAction', 'extensions'=>'jpg,png,jpeg'], // разрешно заргружать только картинки
		'deleteFile'=>['class'=>'\ext\uploader\actions\DeleteFileAction', 'maxtime'=>315360000] // всегда разрешено удаление файлоа (10 лет)
	]);
}
шаблон
<?php $this->widget('\ext\uploader\widgets\UploadField', [
	'hash'=>$item->getFileHash(),
	'uploadUrl'=>'/cp/question/uploadFile',
	'deleteUrl'=>'/cp/question/deleteFile',
	'tagOptions'=>['class'=>'row', 'style'=>'margin:10px -20px;padding:0 40px;']
]); ?>
<? $this->widget('\ext\uploader\widgets\FileList', [
	'hash'=>$item->getFileHash(),
	'tagOptions'=>['class'=>'row', 'style'=>'margin:10px -20px;padding:0 40px;'],
	'deleteUrl'=>'/cp/question/deleteFile'
]); ?>


-- для формы обратной связи / раздел администрирования --
 public function actions()
    {
        $myFeedbackModel=\feedback\components\FeedbackFactory::factory('my_feedback_id')->getModelFactory()->getModel();
        return \CMap::mergeArray(parent::actions(), [
            'clearFiles'=>[
                'class'=>'\ext\uploader\actions\ClearAction',
                'models'=>[[$myFeedbackModel, 'filehash']]
            ],
            
            
            
прикрепление файлов к письму
 $model=$event->params['model'];
$attacheFiles=[];
if($model->filehash) {
    $attacheFiles=HUploader::getFiles(\Yii::getPathOfAlias('webroot.images.uploader'), true, $model->filehash);
}
HEmail::cmsAdminSend(true, [
    'factory'=>$event->params['factory'],
    'model'=>$event->params['model'],
], 'feedback.views._email.new_message_success', false, $attacheFiles);
* если требуется добавить возможность прикреплять файл в 
HEmail::cmsAdminSend(..., $attachfiles=[]) 
и HEmail::send(..., $attachfiles=[]);

	if(!empty($attachfiles)) {
        foreach($attachfiles as $attachfile) {
            $mail->addAttachment($attachfile);
        }
    }
    
    return $mail->send();


----------- пример для торговых предложений ---

Product.php
public $offer_photo_hash;
public function getOfferPhotoHash()
{
    return md5(get_class($this) . "_offer_{$this->id}");
}

/admin/controllers/ShopController.php
public function actions()
{
	...
	'uploadFile'=>['class'=>'\ext\uploader\actions\UploadFileAction', 'extensions'=>'jpg,png,jpeg', 'limit'=>50], // разрешно заргружать только картинки
	'deleteFile'=>['class'=>'\ext\uploader\actions\DeleteFileAction', 'maxtime'=>315360000] // всегда разрешено удаление файлоа (10 лет)
}
		    
/admin/views/shop/_form_product_offer.php
<?php if($model->isNewRecord): ?>
	<div class="alert alert-info">Разрешено загружать изображения для торговых предложений только после создания товара</div>
<?php else: ?>
    <div class="panel panel-default">
    	<div class="panel-heading">Изображения для торговых предложений</div>
    	<div class="panel-body">
        <?php $this->widget('\ext\uploader\widgets\UploadField', [
        	'hash'=>$model->getOfferPhotoHash(),
        	'uploadUrl'=>'/cp/shop/uploadFile',
        	'deleteUrl'=>'/cp/shop/deleteFile',
        	'tagOptions'=>['class'=>'row', 'style'=>'margin:10px -20px;padding:0 40px;']
        ]); ?>
        <? $this->widget('\ext\uploader\widgets\FileList', [
            'hash'=>$model->getOfferPhotoHash(),
        	'tagOptions'=>['class'=>'row', 'style'=>'margin:10px -20px;padding:0 40px;'],
        	'deleteUrl'=>'/cp/shop/deleteFile'
        ]); ?>
        </div>
    </div>
<?php endif; ?>
