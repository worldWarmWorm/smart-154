<?php
use common\components\helpers\HArray as A;
?>
<div class="row">
	<? $this->widget('\common\widgets\form\AliasField', A::m(compact('form', 'model'), ['btnUpdate'=>false])); ?>
</div>

<? $this->widget('\common\widgets\form\TextField', A::m(compact('form', 'model'), ['attribute'=>'meta_h1'])); ?>
<? $this->widget('\common\widgets\form\TextField', A::m(compact('form', 'model'), ['attribute'=>'meta_title'])); ?>
<? $this->widget('\common\widgets\form\TextAreaField', A::m(compact('form', 'model'), ['attribute'=>'meta_key'])); ?>
<? $this->widget('\common\widgets\form\TextAreaField', A::m(compact('form', 'model'), ['attribute'=>'meta_desc'])); ?>