<?php
use \DOrder\models\OrderCustomerFields;
/**
 * @var OrderCustomerFields[] $fields
 * @var OrderCustomerFields $model
 * @var CActiveForm $form
 */
$breadcrumbs = array();

$breadcrumbs[D::cms('shop_title', 'Каталог')] = array('shop/index');
$breadcrumbs[] = 'Форма заказа';
$this->breadcrumbs = $breadcrumbs;
?>

<div id="order-fields-list">
    <h1>Поля формы оформления заказа</h1>
    <table id="order-fields-table" class="adminList table table-striped table-hover">
        <thead>
            <tr>
                <th><?=$model->getAttributeLabel('name')?></th>
                <th><?=$model->getAttributeLabel('label')?></th>
                <th><?=$model->getAttributeLabel('type')?></th>
                <th><?=$model->getAttributeLabel('required')?></th>
                <th><?=$model->getAttributeLabel('sort')?></th>
                <!--<th><?/*=$model->getAttributeLabel('default_value')*/?></th>
                <th><?/*=$model->getAttributeLabel('values')*/?></th>-->
                <th></th>
                <th></th>
            </tr>
        </thead>
        <tbody>
            <?php foreach ($fields as $i=>$f) { ?>
                <tr id="order-field-row-<?php echo $f->id; ?>" class="row<?php echo $i % 2 ? 0 : 1; ?>" valign="top">
                    <td class="data_column"><?=$f->name?></td>
                    <td class="data_column"><?=$f->label?></td>
                    <td class="data_column"><?=$f->type?></td>
                    <td class="data_column"><?=($f->required ? 'да' : 'нет')?></td>
                    <td class="data_column"><?=$f->sort?></td>
					<!--<td class="data_column"><?/*=$f->default_value*/?></td>
                    <td class="data_column"><?/*=$f->values*/?></td>-->
                    <td>
                        <?php echo CHtml::link(
                            'Удалить',
                            'javascript:;',
                            array(
                                'class'=>'order-field-btn-remove btn btn-danger',
                                'data-item'=>$f->id
                            )
                        ); ?>
                    </td>
                    <td>
                        <?php echo CHtml::link(
                            'Изменить',
                            'javascript:;',
                            array(
                                'class'=>'order-field-btn-modify btn btn-success',
                                'data-item'=>$f->id,
                                'rel' => "modify" . $f->id,
                            )
                        ); ?>
                    </td>
                </tr>
            <?php } ?>
        </tbody>
    </table>
</div>
<?php Yii::app()->clientscript->registerScriptFile($this->module->assetsUrl.'/js/customer_fields_admin.js'); ?>
<script type="text/javascript">
    $(function() {
        CustomerFieldsAdmin.init();
    });
</script>


<h1>Новое поле для формы заказа</h1>
<div class="form">
    <?php $form = $this->beginWidget('CActiveForm', array(
        'id'=>'field-form',
        'enableClientValidation'=>true,
        'clientOptions'=>array(
            'validateOnSubmit'=>true,
            'validateOnChange'=>false
        ),
        'htmlOptions' => array('enctype'=>'multipart/form-data'),
    )); ?>

    <?=$form->errorSummary($model)?>

    <div class="col-md-4">
        <?php echo $form->labelEx($model, 'name'); ?>
        <?php echo $form->textField($model, 'name', array('class'=>'form-control')); ?>
        <?php echo $form->error($model, 'name'); ?>
    </div>
    <div class="col-md-4">
        <?php echo $form->labelEx($model, 'label'); ?>
        <?php echo $form->textField($model, 'label', array('class'=>'form-control')); ?>
        <?php echo $form->error($model, 'label'); ?>
    </div>
    <div class="col-md-4">
        <?php echo $form->labelEx($model, 'type'); ?>
        <?php echo $form->dropDownList($model, 'type', $model->getTypes(), array(
        		'class'=>'form-control',
				'onchange'=>'if(this.value=="' . OrderCustomerFields::TYPE_CHECKBOX_GROUP .'" || this.value=="' . OrderCustomerFields::TYPE_RADIOBUTTON . '" || this.value=="' . OrderCustomerFields::TYPE_SELECT . '") { $("#values-list").show(); } else { $("#values-list").hide(); $("#values-list textarea").value(""); }'
		)); ?>
        <?php echo $form->error($model, 'type'); ?>
    </div>
	<div class="clr">&nbsp;</div>
    <div class="col-md-4">
        <?php echo $form->labelEx($model, 'required'); ?>
        <?php echo $form->dropDownList($model, 'required', ['нет', 'да'], array('class'=>'form-control')); ?>
        <?php echo $form->error($model, 'required'); ?>
    </div>
    <div class="col-md-4">
        <?php echo $form->labelEx($model, 'sort'); ?>
        <?php echo $form->textField($model, 'sort', array('class'=>'form-control')); ?>
        <?php echo $form->error($model, 'sort'); ?>
    </div>
    <div class="col-md-4">
        <?php echo $form->labelEx($model, 'default_value'); ?>
        <?php echo $form->textField($model, 'default_value', array('class'=>'form-control')); ?>
        <?php echo $form->error($model, 'default_value'); ?>
    </div>
    <div class="clr">&nbsp;</div>
    <div class="col-md-12" id="values-list" style="display: none;">
        <?php echo $form->labelEx($model, 'values'); ?>
        <?php echo $form->textArea($model, 'values', array('class'=>'form-control', 'rows'=>10)); ?>
		<span><small><b>* каждое значение на новой строке</b></small></span>
        <?php echo $form->error($model, 'values'); ?>
    </div>

	<div class="clr">&nbsp;</div>
    <div class="col-md-12">
        <br>
        <?php echo CHtml::submitButton('Добавить поле', array('class'=>'btn btn-primary')); ?>
    </div>
    <div class="clr"></div>

    <?php $this->endWidget(); ?>
</div><!-- form -->
