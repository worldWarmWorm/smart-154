<div class="row">
    <?php echo $form->label($model, 'meta_h1'); ?>
    <?php echo $form->textField($model, 'meta_h1', array('class'=>'inline form-control')); ?>
    <?php echo $form->error($model,'meta_h1'); ?>
</div>

<div class="row">
    <?php echo $form->label($model, 'a_title'); ?>
    <?php echo $form->textField($model, 'a_title', array('class'=>'inline form-control')); ?>
    <?php echo $form->error($model,'a_title'); ?>
</div>

<div class="row">
    <?php echo $form->label($model, 'meta_title'); ?>
    <?php echo $form->textField($model, 'meta_title', array('class'=>'inline form-control')); ?>
    <?php echo $form->error($model,'meta_title'); ?>
</div>

<div class="row">
    <?php echo $form->label($model, 'meta_key'); ?>
    <?php echo $form->textArea($model, 'meta_key', array('class'=>'inline form-control')); ?>
    <?php echo $form->error($model,'meta_key'); ?>
</div>

<div class="row">
    <?php echo $form->label($model, 'meta_desc'); ?>
    <?php echo $form->textArea($model, 'meta_desc', array('class'=>'inline form-control')); ?>
    <?php echo $form->error($model,'meta_desc'); ?>
</div>

<hr>
<h2>Настройки для - sitemap.xml</h2>
<?php $this->widget('admin.widget.Seo.generateSitemap'); ?>
<hr>

<div class="row">
    <div class="col-md-5">
        <?php echo $form->label($model, 'priority'); ?>
        <?php echo $form->textField($model, 'priority', array('class'=>'form-control')); ?>
        <?php echo $form->error($model,'priority'); ?>
    </div>
</div>

<div class="row">
    <div class="col-md-5">
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
</div>