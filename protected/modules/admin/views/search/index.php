<h1>Результаты поиска товаров<span style="font-size:0.7em;display:inline-block;margin-left:5px;">(<?=CHtml::link('вернуться в каталог', '/cp/shop/index')?>)</span></h1>
<div class="clearfix panel">
<? $this->renderPartial('/shop/_search'); ?>
</div>
<?php
if(!empty($productDataProvider) && ($productDataProvider->getTotalItemCount() > 0)):
    $this->renderPartial('/shop/_category_controls', ['model'=>$category]);
	$this->renderPartial('/shop/_products', ['productDataProvider'=>$productDataProvider, 'modeRelatedHidden'=>true/*, 'category'=>$category*/]);
else:
    if(empty($productDataProvider)) echo '<br /><i>Слишком короткий запрос</i>';
	else echo '<br /><i>Не найдено</i>'; 
endif; ?>
