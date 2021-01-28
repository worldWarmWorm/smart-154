<?
/** @var AdminController $this */
use common\components\helpers\HYii as Y;
use common\components\helpers\HArray as A;
use admin\components\helpers\HAdmin;
use common\components\helpers\HFile;

$baseUrl=$this->module->assetsUrl;
?>
<!DOCTYPE html>
<html>
<head>
  	<meta charset="utf-8" />
  	<title><?=CHtml::encode($this->pageTitle)?></title>
  	<link rel="shortcut icon" href="<?=$baseUrl?>/images/favicon.png" />
	<!--   <link rel="stylesheet" type="text/css" href="<?=$baseUrl?>/css/bootstrap-theme.css" /> -->
	<? Y::cs()->registerCssFile($baseUrl.'/css/bootstrap.css'); ?>
	<? Y::cs()->registerCssFile($baseUrl.'/css/elements.css'); ?>
	<? Y::cs()->registerCssFile($baseUrl.'/css/modules.css'); ?>
	<? Y::cs()->registerCssFile($baseUrl.'/css/style.css'); ?>
	<? Y::cs()->registerCoreScript('jquery.ui'); ?>
	<? Y::cs()->registerScriptFile($baseUrl.'/js/admin_main.js'); ?>
	<? Y::cs()->registerScriptFile('/js/jquery/jquery-migrate-1.2.1.min.js'); ?>
	<? Y::cs()->registerScriptFile($baseUrl.'/js/bootstrap.min.js'); ?>
	<!--[if lt IE 9]>
	<script src="http://html5shiv.googlecode.com/svn/trunk/html5.js" type="text/javascript"></script>
	<![endif]-->
</head>
  <body>
      <nav class="top_menu navbar-fixed-top navbar navbar-inverse" role="navigation">
      <div class="container">
        <div class="row">
          <div class="navbar-header">
            <button type="button" class="navbar-toggle" data-toggle="collapse" data-target="#bs-example-navbar-collapse-1">
              <span class="sr-only">Toggle navigation</span>
              <span class="icon-bar"></span>
              <span class="icon-bar"></span>
              <span class="icon-bar"></span>
            </button>
            <a class="logo navbar-brand" href="<?=$this->createUrl('default/index')?>" title="Перейти на главную страницу панели администрирования">
              <svg xmlns="http://www.w3.org/2000/svg" xml:space="preserve" version="1.0" style="shape-rendering:geometricPrecision; text-rendering:geometricPrecision; fill-rule:evenodd; clip-rule:evenodd"
              viewBox="0 0 104 104" xmlns:xlink="http://www.w3.org/1999/xlink">
                <g>
                  <path class="fil0" d="M0 65l0 -26 13 0 0 26 -13 0zm30 39l-30 0 0 -26 13 0 0 13 17 0 0 13zm35 0l-26 0 0 -13 26 0 0 13zm39 -26l0 26 -30 0 0 -13 17 0 0 -13 13 0zm0 -39l0 26 -13 0 0 -26 13 0zm-30 -38l30 0 0 26 -13 0 0 -13 -17 0 0 -13zm-35 0l26 0 0 13 -26 0 0 -13zm-39 0l30 0 0 13 -17 0 0 13 -13 0 0 -26zm39 25l9 0 0 22 10 -22 9 0 -11 24 11 28 -9 0 -10 -26 0 26 -9 0 0 -52z"/>
                </g>
              </svg>
            </a>
            <div class="sitename navbar-brand">
              <a href="/" target="_blank" title="Перейти на главную страницу сайта">На сайт <i class="glyphicon glyphicon-upload"></i></a>
            </div>
          </div>
          <div class="collapse navbar-collapse" id="bs-example-navbar-collapse-1">
            <? $this->widget('zii.widgets.CMenu', [
                'items'=>HAdmin::menuItemsMain(),
                'htmlOptions'=>['class'=>'nav navbar-nav'],
                'submenuHtmlOptions'=>['class'=>'dropdown-menu'],
            	'activateParents'=>true,
            ]); ?>
            
            <ul class="nav navbar-nav navbar-right">
              <?php if(D::isTopAdmin()): ?>
              <li onmouseover="$(this).find('.dropdown-menu').show()" onmouseout="$(this).find('.dropdown-menu').hide()">
                <?= CHtml::link('Настройки <i class="glyphicon glyphicon-cog"></i> <b class="caret"></b>', ['default/settings']); ?>
                <ul class="dropdown-menu" style="display: none;">
                    <li><a href="/cp/crud/index?cid=admins">Администраторы</a></li>
                </ul>
              </li>
              <?php else: ?>
              	<li><?= CHtml::link('Настройки <i class="glyphicon glyphicon-cog"></i>', ['default/settings']); ?></li>
              <?php endif; ?>
              <li><?= CHtml::link('Выход <i class="glyphicon glyphicon-off"></i>', ['default/logout']); ?></li>
            </ul>
            
            <? $this->widget('zii.widgets.CMenu', [
                'items'=>HAdmin::menuItemsNotifications(),
                'htmlOptions'=>['class'=>'nav navbar-nav navbar-right'],
                'submenuHtmlOptions'=>['class'=>'dropdown-menu'],
            	'activateParents'=>true,
            ]); ?>
          </div>  
        </div>
      </div>
    </nav>

    <div class="content_box">
      <div class="wrapper">
        <? $this->widget('zii.widgets.CBreadcrumbs', array(
            'links'=>$this->breadcrumbs,
            'homeLink'=>CHtml::link('Главная', Yii::app()->createUrl('cp')).' <span class="divider">/</span> ',
            'separator'=>'',
            'activeLinkTemplate'=>'<li><a href="{url}">{label}</a></li>',
            'inactiveLinkTemplate'=>'<li><span>{label}</span></li>',
            'tagName'=>'ul',
            'separator'=>false,
            'encodeLabel'=>false,
            'htmlOptions'=>array('class'=>'breadcrumb')
        )); ?>
        <div id="main">
            <?php echo $content; ?>
        </div>
        <div id="footer">
          <div class="left">
              &copy; <?= CHtml::link($this->skinParam('support_name'), $this->skinParam('support_url'), ['target'=>'_blank']); ?>
              &nbsp; <?= $this->skinParam('product_name'); ?> <?= HFile::includeByAlias('webroot.version', '', 0, '.txt'); ?>
          </div>
          <?php if(D::role('sadmin')): ?>
          <div class="center-block center inline">
          		<?php if(D::isDevMode()): ?>
              		<?= \CHtml::link('<i class="glyphicon glyphicon-lock"></i> DEVCP', '/devcp/', ['target'=>'_blank']); ?>
              		<?= \CHtml::link('<i class="glyphicon glyphicon-lock"></i> Инфо-блоки', '/cp/iblock'); ?>
              	<?php endif; ?>
          		<?php
          		    $devurl=preg_replace('/[?&]dev=1/i','',$_SERVER['REQUEST_URI']);
          		    if(D::isDevMode()) {
          		        echo \CHtml::link('<i class="glyphicon glyphicon-user"></i> Режим пользователя', $devurl . ((strpos($devurl,'?')===false)?'?':'&') . 'dev=0');
          		    }
          		    else {
          		        echo \CHtml::link('<i class="glyphicon glyphicon-wrench"></i> Режим разработки', $devurl . ((strpos($devurl,'?')===false)?'?':'&') . 'dev=1');
          		    }
          		?>
          </div>
          <?php endif; ?>
          <div class="right">Служба поддержки клиентов: (383)<noskype></noskype> <?= $this->skinParam('support_phone'); ?></div>
          <div class="clr"></div>
        </div>
      </div>
    </div>
  </body>
</html>


 
