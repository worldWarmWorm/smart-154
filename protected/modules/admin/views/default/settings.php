<?php 
    $this->pageTitle = 'Настройки сайта - '. $this->appName; 
    $this->breadcrumbs=array(
        'Настройки'=>array('default/settings'),
        'Общие'=>'#swither',
    );
?>

<h1>
    Настройки
    <?=CHtml::link('Очистить кэш', ['default/clearCache'], ['class'=>'btn btn-warning pull-right', 'onclick'=>'return confirm(\'Подвердите очистку кэша.\')']);?>
</h1>

<div class="form">
    <?php $form = $this->beginWidget('CActiveForm', array(
        'id'=>'settings-form',
        'enableClientValidation'=>!D::role('sadmin'),
        'enableAjaxValidation'=>D::role('sadmin'),
        'clientOptions'=>array(
            'validateOnSubmit'=>true,
        ),
        'htmlOptions' => array('enctype'=>'multipart/form-data'),
    )); ?>

    <? if($model->isDevMode()) echo $form->errorSummary($model); ?>

    <?php 
    $tabs = array(
        'Общие'=>array('content'=>$this->renderPartial('settings_general', compact('model', 'form'), true), 'id'=>'tab-general'),
        'Блог' =>array('content'=>$this->renderPartial('settings_blog'   , compact('model', 'form'), true), 'id'=>'tab-blog'),
        'Seo'  =>array('content'=>$this->renderPartial('settings_seo'    , compact('model', 'form'), true), 'id'=>'tab-seo'),
		'Карта сайта'  =>array('content'=>$this->renderPartial('settings_sitemap'    , compact('model', 'form'), true), 'id'=>'tab-sitemap'),
    );
    if(SettingsForm::$files) {
        $tabs['Файлы'] = array('content'=>$this->renderPartial('settings_files', compact('model', 'form'), true), 'id'=>'tab-files');
    }
    if(D::yd()->isActive('slider') && $model->isDevMode()) {
    	$tabs['Слайдер'] = array('content'=>$this->renderPartial('settings_slider', compact('model', 'form'), true), 'id'=>'tab-slider');
    }
    if(D::yd()->isActive('treemenu') && $model->isDevMode()) {
    	$tabs['TreeMenu'] = array('content'=>$this->renderPartial('settings_treemenu', compact('model', 'form'), true), 'id'=>'tab-treemenu');
    }
    if(D::yd()->isActive('question') && $model->isDevMode()) {
    	$tabs['Вопрос-ответ'] = array('content'=>$this->renderPartial('settings_question', compact('model', 'form'), true), 'id'=>'tab-question');
    }
    if(D::yd()->isActive('shop') && $model->isDevMode()) {
    	$tabs['Магазин'] = array('content'=>$this->renderPartial('settings_shop', compact('model', 'form'), true), 'id'=>'tab-shop');
    }
    if(D::role('sadmin')) {
		$tabs['Новости'] = array('content'=>$this->renderPartial('settings_events', compact('model', 'form'), true), 'id'=>'tab-events');
	}
	if(D::yd()->isActive('gallery') && $model->isDevMode()) {
		$tabs['Фотогалерея'] = array('content'=>$this->renderPartial('settings_gallery', compact('model', 'form'), true), 'id'=>'tab-gallery');
	}
    if(D::yd()->isActive('sale')) {
		$tabs['Акции'] = array('content'=>$this->renderPartial('settings_sale', compact('model', 'form'), true), 'id'=>'tab-sale');
	}
    if(D::role('sadmin')) {
		$tabs['Редактор'] = array('content'=>$this->renderPartial('settings_tinymce', compact('model', 'form'), true), 'id'=>'tab-tinymce');
	}
	if($model->isDevMode()) {
		$tabs['Дополнительно'] = array('content'=>$this->renderPartial('settings_system', compact('model', 'form'), true), 'id'=>'tab-system');
	}

    $this->widget('zii.widgets.jui.CJuiTabs', array(
        'tabs'=>$tabs,
        'options'=>array(
            /*'collapsible'=>true,*/
        )
    )); 
    ?>

    <div class="row buttons">
        <?php echo CHtml::submitButton('Сохранить', array('class'=>'btn btn-primary')); ?>
        <?php echo CHtml::link('отмена', array('default/index'), array('class'=>'btn btn-default')); ?>
    </div>

    <?php $this->endWidget();  ?>
</div>

<script type="text/javascript">
    $(document).ready(function(){
        $(document).on('click', '.ui-tabs-nav li', function(){
            $('a[href="#swither"]').text($(this).text());
            return false;
        });
        $('body').on('click', 'a[href="#swither"]', function(){
            return false;
        });
    });
</script>
