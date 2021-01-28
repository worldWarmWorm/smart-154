<?
/** @var \widget\sale\SaleList $this */
/** @var array[\Sale] $models */ 
?>


<?=\CHtml::openTag($this->itemsTagName, $this->htmlOptions)?>
  <?foreach($models as $model):?>
	  <?=\CHtml::openTag($this->itemTagName)?>
			<div class="slide">
				<div class="slide-left-contetn">
					<?if(!empty($model->preview)):?>
						<div class="action_img">
							<a href="<?=\Yii::app()->createUrl('sale/view', array('id'=>$model->id))?>">
								<img src="<?=$model->imageBehavior->getSrc()?>" alt="<?=$model->title?>" title="<?=$model->title?>" class="img-responsive">
							</a>
						</div>
					<?endif?>
				</div>
				<div class="slide-right-contetn">
					<div class="has-fon">
						<p class="has-date"><?=$model->date?></p>
						<?=D::c($this->showSaleTitle, \CHtml::link($model->title, array('sale/view', 'id'=>$model->id), array('class'=>'action-head')))?>
						<p class="has-text"><?=$model->preview_text?></p>
					</div>
					<div class="btn-wrap">
						<?if($this->showLinkAll) 
							echo \CHtml::link(\D::cms('sale_link_all_text', \Yii::t('sale','link.all')), array('/sale'), $this->linkAllOptions)
						?>
					</div>
				</div>
			</div>
	  <?=\CHtml::closeTag($this->itemTagName)?>
  <?endforeach?>
<?=\CHtml::closeTag($this->itemsTagName)?>



