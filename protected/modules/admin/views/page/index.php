<?php
use AttributeHelper as A;

$this->pageTitle = 'Страницы - '. $this->appName; 
$this->breadcrumbs=array(
    'Страницы'=>array('page/index'),
);
?>

<h1>Страницы</h1>
        
<div>
  <a class="btn btn-primary" href="<?php echo $this->createUrl('page/create');?>"><span>Создать</span></a>
  <div class="pull-right page_item-menu_count">
  	<label for="SettingsForm_menu_limit">Кол-во пунктов меню</label>
  	<?=CHtml::textField('SettingsForm_menu_limit', D::cms('menu_limit', A::get(\Yii::app()->params,'menu_limit')), array('class'=>'form-control'))?>
  </div>
</div>
<br>
<?php 
	$id=uniqid('cmsmenu');
	$this->widget('admin.widget.Nestable.CmsMenuWidget', array(
		'id'=>$id, 
		'mode'=>D::c((D::cms('treemenu_depth') == '-'), 'tree', 'basic'),
		'showId'=>D::yd()->isActive('treemenu') && (D::cms('treemenu_show_id')==1)
	));
?>
<script type="text/javascript">
$(function() {
	$('#SettingsForm_menu_limit').on('change', function(e) {
		var $target=$(e.target), $parent=$target.parent();
		$parent.removeClass('has-error has-success');
		if(isNaN(+$target.val())) {
			$parent.addClass('has-error');
			return false;
		}
		$.post("<?=$this->createUrl('default/saveMenuLimit')?>", {limit: $(e.target).val()}, function(responceData) {
			if(responceData.success) $parent.addClass('has-success');
			else $parent.addClass('has-error');
		}, "json");
	});
	$(".disabled_edit_page_button").on("click", function() { return false; });
	$(".delete-ajax").on("click", function(e){ 
		e.preventDefault();
		if(!confirm("Вы действительно хотите удалить страницу?")) return false;
		$.get($(e.target).closest("a").attr("href"), {}, function(data, textStatus, jqXHR) {
			window.location.reload();
			// $(e.target).closest('li').remove();
		}, "json"); 
	});
	$("#<?=$id?>").on("change", function(e) { 
		$.post("<?=$this->createUrl('menu/changeSort')?>", {data:JSON.stringify(NestableWidget.getSerialize("<?=$id?>"))}, function(){}, "json"); 
	});
});
</script>
