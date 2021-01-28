<?php
use \DOrder\models\OrderCustomerFields;
/**
 * @var OrderCustomerFields $model
 * @var CActiveForm $form
 */
?>


<div class="form">
    <?php $form = $this->beginWidget('CActiveForm', array(
        'id' => 'field-form-modify',
        'enableClientValidation' => true,
        'clientOptions' => array(
            'validateOnSubmit' => true,
            'validateOnChange' => false
        ),
        'htmlOptions' => array('enctype' => 'multipart/form-data'),
    )); ?>
    <?= CHtml::hiddenField('id', $model->id) ?>

    <?= $form->errorSummary($model) ?>

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
        <?php echo $form->dropDownList($model, 'type', $model->getTypes(), [
			'class'=>'form-control',
            'onchange'=>'if(this.value=="' . OrderCustomerFields::TYPE_CHECKBOX_GROUP .'" || this.value=="' . OrderCustomerFields::TYPE_RADIOBUTTON . '" || this.value=="' . OrderCustomerFields::TYPE_SELECT . '") { $("#values-list").show(); } else { $("#values-list").hide(); $("#values-list textarea").value(""); }'
		]); ?>
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
	<div class="col-md-12" id="values-list"<?php if(!in_array($model->type, [OrderCustomerFields::TYPE_SELECT, OrderCustomerFields::TYPE_RADIOBUTTON, OrderCustomerFields::TYPE_CHECKBOX_GROUP])){ ?> style="display: none;"<?php } ?>>
        <?php echo $form->labelEx($model, 'values'); ?>
        <?php echo $form->textArea($model, 'values', array('class'=>'form-control', 'rows'=>10)); ?>
		<span><small><b>* каждое значение на новой строке</b></small></span>
        <?php echo $form->error($model, 'values'); ?>
	</div>

	<div class="clr">&nbsp;</div>
	<div class="col-md-12">
		<br>
        <?php
        echo CHtml::ajaxSubmitButton(
            'Сохранить',
            ['orderFieldModify'],
            [
				'dataType' => 'json',
                'success' => 'function (json){ 
                	if (json.result == "success") {
                		$("#order-field-modify").remove();
                		var r_selector = "#order-field-row-" + json.id
						$(r_selector).find(".data_column").remove();
                		$(r_selector).prepend(json.content);
                		$(r_selector).removeAttr("style");
                	} else {
                		$("#order-field-modify td").html(json.content);
                	}
                }'
            ],
            array('id' => uniqid(), 'class' => 'btn btn-primary'));
        ?>
	</div>
	<div class="clr"></div>

    <?php $this->endWidget(); ?>
</div><!-- form -->
