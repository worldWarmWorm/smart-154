<?php
use common\components\helpers\HArray as A;

$titleAttribute = A::rget($config, 'item.titleAttribute', 'title');
if(is_callable($titleAttribute)) {
    $title = call_user_func($titleAttribute, $data);
}
else {
    $title = $data->$titleAttribute;
}
$url = A::rget($config, 'item.url');
if(is_callable($url)) {
    $url = call_user_func($url, $data);
}

?>
<div class="col-md-4">
	<?php echo CHtml::link($title, $url); ?>
</div>