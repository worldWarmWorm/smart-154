<?php
use iblock\models\InfoBlockProp as IBP;
use common\components\helpers\HArray as A;

/* @var $this iblock\controllers\AdminElementsController */
/* @var $form CActiveForm */
/* @var $model iblock\models\InfoBlockElement */
/* @var $iblock iblock\models\InfoBlock */
?>
<?php
    $fields = $model->getFields();
    foreach ($fields as $f) { ?>
        <div class="row">
            <?php switch ($f['type']) {
                case IBP::TYPE_STRING :
                case IBP::TYPE_NUMBER :
                    echo $form->labelEx($model, $f['name']);
                    echo $form->textField($model, $f['name'], ['class' => 'form-control']);
                    break;

                case IBP::TYPE_CHECKBOX :
                    echo $form->checkBox($model, $f['name']);
                    echo $form->labelEx($model, $f['name'], ['class' => 'inline']);
                    break;
                case IBP::TYPE_LIST :
                    echo $form->labelEx($model, $f['name']);
                    echo $form->dropDownList($model, $f['name'], $f['values'], ['class' => 'form-control']);
                    break;
                case IBP::TYPE_FILE :
                    $b = $f['name'].'PropertyBehavior';
                    $this->widget('\common\ext\file\widgets\UploadFile', [
                        'behavior' => $model->{$b},
                        'form' => $form,
                        'actionDelete' => $this->createAction($f['name'].'PropertyRemove'),
                        'view' => 'panel_upload_file'
                    ]);
                    break;
                case IBP::TYPE_IMAGE :
                    $b = $f['name'].'PropertyBehavior';
                    $this->widget('\common\ext\file\widgets\UploadFile', [
                        'behavior' => $model->{$b},
                        'form' => $form,
                        'actionDelete' => $this->createAction($f['name'].'PropertyRemove'),
                        'tmbWidth' => 200,
                        'tmbHeight' => 200,
                        'view' => 'panel_upload_image'
                    ]);
                    break;
                case IBP::TYPE_TEXT_AREA :
                    echo $form->labelEx($model, $f['name']);
                    echo $form->textArea($model, $f['name'], ['class' => 'form-control']);
                    break;
                case IBP::TYPE_TEXT :
                case IBP::TYPE_FULL_TEXT :
                    $this->widget(
                        '\common\widgets\form\TinyMceField',
                        A::m(
                            compact('form', 'model'),
                            [
                                'attribute' => $f['name'],
                                'uploadImages' => false,
                                'uploadFiles' => false,
                                'full' => $f['type'] == IBP::TYPE_FULL_TEXT,
                            ]
                        )
                    );
                    /*$this->widget('admin.widget.ajaxUploader.ajaxUploader', array(
                        'fieldName'=>'images',
                        'fieldLabel'=>'Загрузка фото',
                        'model'=>$model,
                        'tmb_height'=>100,
                        'tmb_width'=>100,
                        'fileType'=>'image'
                    ));
                    $this->widget('admin.widget.ajaxUploader.ajaxUploader', array(
                        'fieldName'=>'files',
                        'fieldLabel'=>'Загрузка файлов',
                        'model'=>$model,
                    ));*/

                    break;
            }
            ?>
            <?php echo $form->error($model, $f['name']); ?>
        </div>
<?php } ?>
