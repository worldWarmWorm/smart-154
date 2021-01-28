<?php
use common\components\helpers\HArray as A;
?>
<? $this->widget('\common\widgets\form\CheckboxField', A::m(compact('form', 'model'), ['attribute'=>'index_page_content_pos_footer'])); ?>
<? $this->widget('\common\widgets\form\TinyMceField', A::m(compact('form', 'model'), ['attribute'=>'index_page_content'])); ?>