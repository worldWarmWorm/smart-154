
<?php $this->owner->widget('zii.widgets.CMenu', array(
    'items'=>$items,
    'htmlOptions'=>array('class'=>$this->listClass),
    'activateParents' => true,
    'encodeLabel' => false,
)); ?>
