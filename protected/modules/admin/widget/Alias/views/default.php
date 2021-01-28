<?php
/** @var $this AliasFieldWidget */

echo $this->form->labelEx($this->model, $this->attributeAlias); 
echo $this->form->textField($this->model, $this->attributeAlias, array(
	'size'=>160,
    'maxlength'=>255, 
    'class'=>'form-control inline',
));
if(!$this->model->isNewRecord) { 
	echo '&nbsp;'.CHtml::button(\Yii::t('AdminModule.admin', 'btn.reload'), array(
    	'class'=>'btn btn-default js-afw-btn-update'
  	));
}
echo $this->form->error($this->model, $this->attributeAlias); 
?>