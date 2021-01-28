<?php
use iblock\models\InfoBlockProp as IBP;
use common\components\helpers\HArray as A;

/* @var $this iblock\controllers\AdminElementsController */
/* @var $form CActiveForm */
/* @var $model iblock\models\InfoBlockElement */
/* @var $iblock iblock\models\InfoBlock */
?>

<div class="form">

    <?php $form = $this->beginWidget('CActiveForm', array(
        'id' => 'info-block-element-form',
        'enableAjaxValidation' => false,
        'htmlOptions' => ['enctype' => 'multipart/form-data'],
    )); ?>
    <?php echo $form->errorSummary($model); ?>

    <?php
    $items = [
        'Основые данные' => ['content' => $this->renderPartial('iblock.views.admin-elements.__base_form', compact('model', 'form', 'iblock'), true), 'id' => 'tab-main'],
    ];

    if ($iblock->infoBlockProps) {
        $items['Свойства'] = ['content' => $this->renderPartial('iblock.views.admin-elements.__properties_form', compact('model', 'form', 'iblock'), true), 'id' => 'tab-properties'];
    }

    $this->widget('zii.widgets.jui.CJuiTabs', [
        'tabs' => $items,
        'options' => []
    ]);
    ?>

	<div class="row buttons">
		<div class="left">
            <?php echo CHtml::submitButton($model->isNewRecord ? 'Создать' : 'Сохранить', array('class' => 'btn btn-primary')); ?>
		</div>

        <?php if (!$model->isNewRecord): ?>
			<div class='left'>
				<a class='btn btn-danger delete-b'
				   href="<?= $this->createUrl('/cp/iblockElements/delete', array("id" => $model->id)) ?>"
				   onclick="return confirm('Вы действительно хотите удалить запись?');">
					<span>Удалить</span></a>
			</div>
        <?php endif; ?>
		<div class="clr"></div>
	</div>

    <?php $this->endWidget(); ?>

</div><!-- form -->
