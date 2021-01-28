<h1><?= D::cms('gallery_title', Yii::t('gallery', 'gallery_title')) ?></h1>
<div class="fotogallery_page">
    <ul class="fotogallery_list_box">
        <?php foreach ($albums as $album): ?>
            <li>
                <h2><?= $album->title; ?></h2>
                <div class="content">
                    <?= $album->description; ?>
                </div>
                <div class="fotogallery_page">
                    <ul class="fotogallery_inner_box">
                        <?php foreach ($album->photos as $photo): ?>
                            <li>
                                <div class="foto_wrap">
                                    <a rel="album_<?= $album->id ?>_images" href="<?= $photo->img ?>" class="image-full"
                                       title="<?= $photo->description ?>">
                                        <img src="<?= $photo->MainTmb ?>" alt="<?= $photo->description ?>">
                                    </a>
                                </div>
                            </li>
                        <?php endforeach; ?>
                    </ul>
                </div>
            </li>
        <?php endforeach; ?>
    </ul>
</div>