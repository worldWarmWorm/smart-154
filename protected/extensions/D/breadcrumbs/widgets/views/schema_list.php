<?php
$position=1;
echo \CHtml::openTag('div', $this->htmlOptions);
if($this->homeTitle):?>
<div itemscope itemtype="http://schema.org/BreadcrumbList">
    <div itemprop="itemListElement" itemscope itemtype="http://schema.org/ListItem" style="display:inline-block">
        <a itemprop="item" href="<?=$this->homeUrl?>"><span itemprop="name"><?=$this->homeTitle?></span></a>
        <meta itemprop="position" content="<?=$position++?>" />
    </div>
</div>
<?endif?>

<?foreach($this->breadcrumbs as $breadcrumb):?>
    <?php if($breadcrumb === end($this->breadcrumbs)):?>
    	<?
		$link=\Yii::app()->createAbsoluteUrl($_SERVER['REQUEST_URI']);
	    $params=(array)$breadcrumb['url'];
	    if(!empty($params)) {
			$link=\Yii::app()->createAbsoluteUrl(array_shift($params), $params);
		}
		?>
        <div itemscope itemtype="http://schema.org/BreadcrumbList">
            <div itemprop="itemListElement" itemscope itemtype="http://schema.org/ListItem" style="display:inline-block">
                <span itemprop="name"><?=$breadcrumb['title']?></span>
                <meta itemprop="position" content="<?=$position++?>" />
                <meta itemprop="item" content="<?=$link?>" />
            </div>
        </div>
    <?php else: ?>
		<?
		$link=null;
	    $params=(array)$breadcrumb['url'];
	    if(!empty($params)) {
			$link=\Yii::app()->createUrl(array_shift($params), $params);
		}
		?>
        <div itemscope itemtype="http://schema.org/BreadcrumbList">
            <div itemprop="itemListElement" itemscope itemtype="http://schema.org/ListItem" style="display:inline-block">
				<? if($link): ?><a itemprop="item" href="<?=$link?>"><span itemprop="name"><?=$breadcrumb['title']?></span></a><? endif; ?>
				<? if(!$link): ?><span itemprop="name"><?=$breadcrumb['title']?></span><meta itemprop="item" content="" /><? endif; ?>
                <meta itemprop="position" content="<?=$position++?>" />
            </div>
        </div>
    <?php endif;?>
<?endforeach?>
	
<?=\CHtml::closeTag('div')?>
