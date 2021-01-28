<?php
/** @var \DOrder\widgets\CustomerFormWidget $this */
use \DOrder\models\OrderCustomerFields as OCF;
?>
<div class="form" style="width: 60%">
    <?php $form = $this->beginWidget('CActiveForm', array(
        'id'=>'dorder-customer-form',
        'enableClientValidation'=>true,
        'clientOptions'=>array(
            'validateOnSubmit'=>true,
            'validateOnChange'=>false
        ),
    )); /* @var CActiveForm $form */

    $fields = $this->model->getFields();
    foreach ($fields as $f) { ?>
		<div class="row">
			<?php echo $form->labelEx($this->model, $f['name']); ?>
			<?php switch ($f['type']) {
				case OCF::TYPE_TEXT :
					echo $form->textField($this->model, $f['name']);
					break;
				case OCF::TYPE_PHONE :
					$this->widget('\common\widgets\form\Phone2Field', [
					    'form'=>$form,
					    'model'=>$this->model, 
					    'attribute'=>$f['name'], 
					    'mask'=>'+7 ( 999 ) 999 - 99 - 99',
					    'htmlOptions' => array('placeholder'=>'+7 ( ___ ) ___ - __ - __'),
					    'tag'=>false,
					    'hideLabel'=>true,
					    'hideError'=>true
					]);
					break;
				case OCF::TYPE_EMAIL :
                    echo $form->textField($this->model, $f['name']);
					break;
				case OCF::TYPE_TEXTAREA :
                    echo $form->textArea($this->model, $f['name']);
					break;
				case OCF::TYPE_SELECT :
                    echo $form->dropDownList($this->model, $f['name'], $f['values']);
					break;
				case OCF::TYPE_CHECKBOX :
                    echo $form->checkBox($this->model, $f['name']);
					break;
				case OCF::TYPE_CHECKBOX_GROUP :
                    echo $form->checkBoxList($this->model, $f['name'], $f['values']);
					break;
				case OCF::TYPE_RADIOBUTTON :
					if(count($f['values']) === 1) { $this->model->{$f['name']}=reset($f['values']); }
                    echo $form->radioButtonList($this->model, $f['name'], $f['values']);
					break;
			}
			?>
			<?php echo $form->error($this->model, $f['name']); ?>
		</div>

	<?php } ?>
	
	<div class="row">
		<?php echo $form->checkBox($this->model,'privacy_policy',array('class'=>'inpt inpt-privacy_policy')); ?>
        <?php echo $form->labelEx($this->model,'privacy_policy'); ?>
        <?php echo $form->error($this->model,'privacy_policy'); ?>
	</div>

    <div class="row buttons">
        <?php echo CHtml::submitButton($this->submitTitle, ['class' => 'btn']); ?>
    </div>

    <?php $this->endWidget(); ?>
</div>
