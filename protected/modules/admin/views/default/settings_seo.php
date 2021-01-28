<div class="row">
	<?php echo $form->label($model, 'seo_yandex_verification'); ?>
    <?php echo $form->textField($model, 'seo_yandex_verification', array('class'=>'form-control')); ?>
    <?php echo $form->error($model,'seo_yandex_verification'); ?>
</div>

<div class="row">
    <?php echo $form->label($model, 'meta_title'); ?>
    <?php echo $form->textField($model, 'meta_title', array('class'=>'form-control')); ?>
    <?php echo $form->error($model,'meta_title'); ?>
</div>

<div class="row">
    <?php echo $form->label($model, 'meta_key'); ?>
    <?php echo $form->textArea($model, 'meta_key', array('class'=>'form-control')); ?>
    <?php echo $form->error($model,'meta_key'); ?>
</div>

<div class="row">
    <?php echo $form->label($model, 'meta_desc'); ?>
    <?php echo $form->textArea($model, 'meta_desc', array('class'=>'form-control')); ?>
    <?php echo $form->error($model,'meta_desc'); ?>
</div>

<hr>
<h2>Настройки для - sitemap.xml</h2>
<?php $this->widget('admin.widget.Seo.generateSitemap'); ?>
<hr>

<div class="row">
    <?php echo $form->label($model, 'sitemap'); ?>
    <?php 
        $this->widget('admin.widget.EditWidget.TinyMCE', array(
        	'editorSelector'=>'sitemapEditor',
            'model'=>$model,
            'attribute'=>'sitemap',
            'full'=>true,
            'htmlOptions'=>array('class'=>'big')
        )); 
    ?>
    <?php echo $form->error($model,'sitemap'); ?>
</div>
