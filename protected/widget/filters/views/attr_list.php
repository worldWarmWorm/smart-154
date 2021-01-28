


<?php $form=$this->beginWidget('CActiveForm', array(
    'id'=>'page-form',
)); ?>

<?php echo CHtml::textField('price_from'); ?>
<?php echo CHtml::textField('price_to'); ?>

<div class="filter">
	<div id="slider-range"></div>
</div>
<?php $this->endWidget(); ?>
<?php 
	$current_price_min = $min_price;
	$current_price_max = $max_price;
/*if (isset(Yii::app()->request->cookies['price_from'])) $current_price_min = Yii::app()->request->cookies['price_from'];
else{
	$current_price_min = $min_price;
}
if (isset(Yii::app()->request->cookies['price_to'])) $current_price_max = Yii::app()->request->cookies['price_to'];
else{
	$current_price_max = $max_price;
}*/
?>

<script>
	$(document).ready(function(){
		$('.reset-filter').on('click', function(){
			$("select").each(function() { $(this).val('none'); });
			$('#price_from').val('<?php echo $current_price_min; ?>');
			$('#price_to').val('<?php echo $current_price_max; ?>');
			$( "#slider-range" ).slider( "values", 1, <?php echo $current_price_max; ?>, $( "#slider-range" ).slider( "values", 0, 0 ));
			
			$('#form-filter').submit();
		});
	});


	var x;
	var y;
	$('.right-side').mousemove(function(e) {
	          x = e.pageX;
	          y = e.pageY;
	});
	if(parseInt(<?php echo $current_price_max; ?>)==0){
		$('.filter').hide();
	}
	$("#slider-range").slider({
	    range: true,
	    min: <?php echo $min_price; ?>,
	    max: <?php echo $max_price; ?>,
	    values: [<?php echo $current_price_min; ?>,<?php echo $current_price_max; ?>],
	    animate: true,
	    click: function(){
	    	$('input.shop-button').hide();	
	    },
	    change: function(){
	    	//$('#page-form').submit();
	    	//$('.filter_this').css('top', y)
	    	
	    	$('.filter_this').css('left', x);
	    	$('input.shop-button').show();
	    },
	    slide: function( event, ui ) {
	       if(ui.values[1] - ui.values[0] < 1 ) return false;
		       $('#price_from').val(ui.values[0]);
		       $('#price_to').val(ui.values[1]);
	        }
	    });
	 $( "#price_from" ).val( $( "#slider-range" ).slider( "values", 0 ));
	 $( "#price_to" ).val( $( "#slider-range" ).slider( "values", 1 ));
</script>

<?php

echo CHtml::beginForm('', '', array('id'=>'form-filter')); 

foreach ($attributes as $key => $attr):  ?>
	<div>
		<?php echo $attr['name']; ?>
	</div>
	<div>
		<?php
		$attr['values']['none'] = 'Без фильтра';
		echo CHtml::dropDownList($attr['id'], 'none', $attr['values'], array('data-id'=>$key, 'selected'=>'none'));  ?>
	</div>
<?php endforeach; ?>


<?php echo CHtml::button('Сбросить', array('class'=>'reset-filter')); ?>

<?php echo CHtml::submitButton('фильтровать', array('class'=>'filter-button')); ?>
<?php echo CHtml::endForm(); ?>
