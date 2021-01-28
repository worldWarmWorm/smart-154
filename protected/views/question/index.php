<?php
use common\components\helpers\HYii as Y;

CmsHtml::fancybox();

Y::js(false, '$(function(){$("#question-list .question").click(function(){$(this).next().toggleClass("show");});});', \CClientScript::POS_READY);
?>

<h1><?= Yii::t('app', 'FAQ') ?></h1>

<span id="add-question" class="add-question">
    <a class="add-question__btn btn" data-src="#question-form-div"><?= Yii::t('app', 'Ask a question') ?></a>
</span>

<div id="question-list" class="question-list">
    <?php foreach($list as $item): ?>
        <?php if (empty($item->answer)) continue; ?>
        <?$collapsed=D::cms('question_collapsed')?' collapsed':'';?>
        <div class="item">
            <span class="username"><?php echo $item->username; ?></span>
            <a class="question<?=$collapsed?>"><?php echo $item->question; ?></a>
            <div class="answer<?=$collapsed?>"><?php echo $item->answer ?></div>
        </div>
    <?php endforeach; ?>

    <?php if (!$list): ?>
    <p><?= Yii::t('app', 'No questions') ?></p>
    <?php endif; ?>
</div>

<div style="display: none">
    <?php $this->renderPartial('_form', compact('model')); ?>
</div>
