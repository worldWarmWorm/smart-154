<h1><?=D::cms('gallery_title',Yii::t('gallery','gallery_title'))?></h1>
<div class="fotogallery_page">
	<ul class="fotogallery_preview_box">
		<?foreach($albums as $album):?>
			<li>
				<div class="foto_wrap">
					<a href="<?=$this->createUrl('gallery/album', array('id'=>$album->id))?>">
						<img src="<?=ResizeHelper::resize($album->getAlbumPreview(), 400, 250); ?>" alt="">
						<div class="fotogallery_title">
							<span><?=$album->title?></span>
							<i><?=$album->getImageCount()?></i>
						</div>
					</a>
				</div>
			</li>
		<?endforeach;?>
	</ul>
</div>
