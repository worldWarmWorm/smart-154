<h1><?= $album->title ?></h1>
<div class="fotogallery_page">
    <ul class="fotogallery_inner_box">
        <?php foreach ($album->photos as $photo): ?>
            <li>
                <div class="foto_wrap">
                    <a href="<?= $photo->img ?>" title="<?= $photo->description ?>" data-caption="<?= $photo->description ?>" data-fancybox="album">
                        <img src="<?= ResizeHelper::resize($photo->img, 250, 250) ?>" alt="<?= $photo->description ?>">
                    </a>
                </div>
            </li>
        <?php endforeach; ?>
    </ul>
</div>
<div class="content">
    <?= $album->description ?>
</div>
