<?
    $this->breadcrumbs = array('Вопрос-ответ'=>array('question/index'));
?>
<h1>Вопрос-ответ</h1>

<?php if (count($list)): ?>
<script type="text/javascript">
var DQuestion = { updateQAAjaxSuccess: function(data) {
	var $flash=$("#faq-item-flash-"+data.id);
	if(data.success) $flash.html("Изменения успешно сохранены.");
	else $flash.html("Произошла ошибка на сервер. Изменения не были сохранены.");

	var $mark=$('#question-'+data.id+' .mark');
	if(data.mark) $mark.addClass('unmarked');
	else $mark.removeClass('unmarked');
		
	$flash.fadeIn();
	$('.question-count, .notification-question-count').text(data.count);
	
	setTimeout(function() { $flash.fadeOut(); }, 5000);
}};

$(function(){
	
    $('#faq').on('click', '.user', function() {
       $("table#faq").find(".details[data-item='" + $(this).data('item') + "']").toggle();
    });
    $("#faq").on('click', '.mark', function() {
        t = $(this);
        $.post(
            "<?=Yii::app()->createUrl("/admin/question/ajax")?>", 
            {item: $(this).data('item'), action: "publish"},
            function(data) {
            	if(data.error) alert(data.error);
            	else {
					if(!data.status) $(t).removeClass('unmarked');
    	            else $(t).addClass('unmarked');
        	        $('.question-count, .notification-question-count').text(data.count);
        	    }
            },
            'json'
        );
	});
});
</script>
<table id="faq" class="faq-list table table-striped table-hover">
    <tr class="head">
        <td class="number">№</td>
        <td class="date">Дата</td>
        <td>ФИО</td>
        <td>Обработан</td>
        <td style="width:50px;">Действия</td>
    </tr>
    <?php foreach($list as $item): ?>
    <tr id="question-<?php echo $item->id; ?>" class="row<?php echo $item->id % 2 ? 0 : 1; ?>">
        <td><?php echo $item->id; ?></td>
        <td><?php echo date("d.m.Y, H:i", strtotime($item->created)); ?></td>
        <td class="title"><?php echo CHtml::link($item->username, "javascript:;", array('class' => 'user', 'data-item' => $item->id)); ?></td>
        <td><div class="mark <?php echo !$item->published ? 'marked' : 'unmarked'; ?>" data-item="<?php echo $item->id; ?>" title="Опубликовать"></div></td>
        <td class="actions"><?php echo CHtml::ajaxLink('Удалить', $this->createUrl('question/delete', array('id'=>$item->id)),
            array(
                'type'=>'post',
                'data'=>array('ajax'=>1),
                'beforeSend'=>'function() { return confirm(\'Подтвердите удаление\'); }',
                'success'=>'function() {$("#question-'. $item->id .'").remove(); $("#faq").find(".details[data-item=\''. $item->id  . '\']").remove();}'
            ),
            array('class'=>'btn btn-danger')
            ); ?>
        </td>
    </tr>
    <tr class="details" data-item="<?=$item->id?>">
        <td colspan="5">
        	<?$form=$this->beginWidget('CActiveForm');?>
        	<?=$form->hiddenField($item, 'id')?>
        	<?=CHtml::hiddenField('action', 'qa_save')?>
            <label>
                <span>Вопрос</span><br>
                <?=$form->textArea($item,'question',array('class'=>'question form-control'))?>
            </label>
            <div class="clr"></div>
            <label>
              <span>Ответ</span><br>
              <?=$form->textArea($item,'answer',array('class'=>'answer form-control'))?>
            </label>
            <br>
            <?=CHtml::ajaxSubmitButton('Сохранить', '/admin/question/ajax', array(
            	'success'=>'DQuestion.updateQAAjaxSuccess',
 				'dataType'=>'json'
            ), array('class'=>'btn btn-primary'))?><span id="faq-item-flash-<?=$item->id?>" class="flash success"></span>
            <?$this->endWidget()?>
        </td>
    </tr>
    <?php endforeach; ?>
</table>
<?php else: ?>
<p>Нет вопросов</p>
<?php endif; ?>


