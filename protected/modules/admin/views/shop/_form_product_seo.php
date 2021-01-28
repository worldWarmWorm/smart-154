<div class="row">
    <?php echo $form->label($model, 'meta_h1'); ?>
    <?php echo $form->textField($model, 'meta_h1', array('class'=>'form-control')); ?>
    <?php echo $form->error($model,'meta_h1'); ?>
</div>

<div class="row">
    <?php echo $form->labelEx($model, 'link_title'); ?>
    <?php echo $form->textField($model, 'link_title', array('class'=>'form-control')); ?>
	<?php echo $form->error($model, 'link_title'); ?>
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

<div class="row" style="margin: 0 -15px;">
    <div class="col-md-4">
        <?php echo $form->label($model, 'priority'); ?>
        <?php echo $form->textField($model, 'priority', array('class'=>'form-control')); ?>
        <?php echo $form->error($model,'priority'); ?>
    </div>
    <div class="col-md-4">
        <?php echo $form->label($model, 'changefreq'); ?>
        <?php echo $form->dropDownList($model, 'changefreq', array(
            'always'=>'always',
            'hourly'=>'hourly',
            'daily'=>'daily',
            'weekly'=>'weekly',
            'monthly'=>'monthly',
            'yearly'=>'yearly',
            'never'=>'never',
        ), array('class'=>'form-control')); ?>
        <?php echo $form->error($model,'changefreq'); ?>
    </div>
    <div class="col-md-4">
        <p style="margin: 0"><b>Настройки для - sitemap.xml</b></p>
        <br>
        <?php $this->widget('admin.widget.Seo.generateSitemap'); ?>
    </div>
</div>

<div class="row">
</div>