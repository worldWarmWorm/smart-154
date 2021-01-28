<?php

$baseUrl = $this->module->assetsUrl;

?>

<script>

function toggleCheckBox(self)  {
	$('.m_default:checkbox').attr('checked', false);
    $(self).attr('checked', true);
}

function toggleHidden(self) {
    $.ajax({
        url: "<?php echo Yii::app()->createUrl('devadmin/menu/toggleHidden'); ?>",
        type: 'get',
        data: {id: $(self).parents('tr').data('menuid')},
        success: function(data) {
            $(self).attr('checked', (data=='1'));
        }
    });
}

function toggleDefault(self) {
    $.ajax({
        url: "<?php echo Yii::app()->createUrl('devadmin/menu/toggleDefault'); ?>",
        type: 'get',
        data: {id: $(self).parents('tr').data('menuid')},
        success: function(data) {
			$('.m_default:checkbox:not([data-item='+$(self).data('item')+'])').attr('checked', false);
			$(self).attr('checked', (data=='1'));
        }
    });
}

function changeName(self) {
    var value = $(self).text();
    $(self).html("<input class='title' type='text' value='" + value + "' />");
    $(self).find(".title").focus();
    $(self).attr("onClick", '');
    $(self).find("input").attr("onBlur", "saveName(this, '" + value + "');");
}

function saveName(self, value) {
    $.ajax({
            url: "<?php echo Yii::app()->createUrl('devadmin/menu/changeName'); ?>",
            type: 'get',
            data: {id: $(self).parents("tr").data('menuid'), newname: $(self).val()},
            success: function(data) {
                $(self).parent().text($(self).val()).attr('onClick', 'changeName(this);');
                $(self).remove();
            },
            error: function(data) {
                $(self).parent().text(value).attr('onClick', 'changeName(this);');
                $(self).remove(); 
            }
        });
}

function saveSeoTitle(id, btn) 
{
	$.post('<?=$this->createUrl('menu/saveSeoATitle')?>', {
			id: id,
			seo_a_title: $(btn).siblings('[name="seo_a_title"]').val()
		}, function(responceData) { 
			if(responceData.length) alert(responceData);
		});
}

</script>

<style>
.table-menu td {
	border-right: 1px solid #000;
	padding: 5px;
}
.table-menu td:last-child {
	border: 0;
}
</style>

<table cellpadding="0" cellspacing="0" class="table-menu">
<tr>
	<th width="45%">Заголовок</th>
	<th width="1%"><img title="Скрыть"src="<?php echo $baseUrl ?>/images/visible.png"></th>
	<th width="1%"><img title="Пункт по умолчанию" src="<?php echo $baseUrl ?>/images/default.png"?></th>
	<th width="45%">SEO &lt;A title="" ...&gt;</th>
</tr>

<?foreach ($model as $item):?>
<tr data-menuid="<?=$item->id?>" class="menuitems">
<td><span class="title" onClick="changeName(this);"><?=$item->title?></span></td>
<td><?=CHtml::checkBox('hidden', $item->hidden, array('onClick'=>'toggleHidden(this);'))?></td>
<td><?=CHtml::checkBox('default', $item->default, array('onClick'=>'toggleDefault(this);', 'class' => 'm_default', 'data-item'=>$item->id)); ?></td>
<td>
	<?=CHtml::textField('seo_a_title', $item->seo_a_title)?>
	<?=CHtml::button('Сохранить', array('onClick'=>"saveSeoTitle({$item->id}, this)"))?>
</td>
</tr>
<?endforeach?>
</table>
<br>
<div class="row"><i>*Клик по пункту, чтобы переименовать.</i></div>
<div class="row"><i>"Скрыть" и "Пункт по умолчанию" сохраняются автоматически.</i></div>
