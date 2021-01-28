<?php Yii::app()->getClientScript()->registerCoreScript( 'jquery.ui' ); ?>

<script>
  $(function() {
    $(".page-accordion").each(function(){
         $(this).accordion({
  	       header: $(this).children().children(".accordion__item_title"),
             active: false,
             collapsible: true,
             heightStyle: "content"
         });
    });
  });
</script>

<div class="accordion-list page-accordion" id="">
	<? foreach($model as $accordion):?>

		<div class="accordion__item">
			<div class="accordion__item_title"><?=$accordion->title?></div>
			<div class="accordion__item_content content">
				<?=$accordion->description?>
			</div>
		</div>
	<?endforeach;?>
</div>
