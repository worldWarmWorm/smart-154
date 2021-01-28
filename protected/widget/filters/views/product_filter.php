<? use common\components\helpers\HArray as A; ?>
<? if($this->category || $this->data): ?>
	<?= CHtml::beginForm('', '', array('id'=>'form-filter')); ?>
	<div class="col-lg-12">
		<div class="col-lg-9">
<? 
if($this->category):
	$tree=Category::getTree($this->category);
	if(!empty($tree)): ?>
	<div class="col-lg-4 filter-item">
		<p class="filter-name">Раздел каталога</p><?
			echo CHtml::dropDownList('category_id', '-', $tree, ['selected'=>'none', 'empty'=>['-'=>$this->category->title]]);  
		?>
	</div>
	<? endif; ?>
<? endif; ?>
<?
if($this->data):
$requestData=HFilter::getRequestData(); 
foreach ($this->data as $aId => $aData): 
	if(!trim($aData['name']) || !$aData['values']) continue;
	?><div class="col-lg-4 filter-item">
		<p class="filter-name"><?= $aData['name']; ?></p><?
			$aData['values']['none'] = 'Без фильтра';
			echo CHtml::dropDownList($aId, A::get($requestData, $aId, 'none'), $aData['values'], ['data-id'=>$aId, 'selected'=>'none']);  
		?>
	</div><?
endforeach; 
endif;
?>
		</div>
		<div class="col-lg-3">
			<button type="submit" class="green-button filter-button">Применить</button>
			<button type="reset" class="clear-button reset-filter">Сбросить</button>
		</div>
	</div>
<?= CHtml::endForm(); ?>    
<? 
endif; ?>
