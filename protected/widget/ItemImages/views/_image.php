<li>
    <div class="foto_wrap">
        <a href="<?= $data->url ?>" data-fancybox="gallery" title="<?php echo $data->description; ?>">
            <img src="<?= ResizeHelper::resize($data->url, 400, 300) ?>" alt="<?php echo $data->description; ?>"/>
        </a>
    </div>
</li>
