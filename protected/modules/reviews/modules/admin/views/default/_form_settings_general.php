<?php
use common\components\helpers\HArray as A;
?>
<? $this->widget('\common\widgets\form\CheckboxField', A::m(compact('form', 'model'), [
	'attribute'=>'auto_generate_preview_text'
])); ?>
<? $this->widget('\common\widgets\form\TextField', A::m(compact('form', 'model'), [
	'attribute'=>'preview_text_length', 'htmlOptions'=>['class'=>'w10 inline form-control']
])); ?>

<? $this->widget('\common\widgets\form\TextField', A::m(compact('form', 'model'), [
	'attribute'=>'tmb_width', 'htmlOptions'=>['class'=>'w10 inline form-control']
])); ?>
<? $this->widget('\common\widgets\form\TextField', A::m(compact('form', 'model'), [
	'attribute'=>'tmb_height', 'htmlOptions'=>['class'=>'w10 inline form-control']
])); ?>

<? $this->widget('\common\widgets\form\TinyMceField', A::m(compact('form', 'model'), [
	'attribute'=>'index_page_content', 'full'=>false
])); ?>