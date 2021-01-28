
<hr>

<div class="row">
	<label>
		<div class="btn btn-success addItem" >Добавить</div>
	</label>
</div>

<hr>

<div id="contentInput">
	<?php 

		if(isset($model->items))
			foreach($model->items(['order'=>'id DESC']) as $item):
				$this->renderPartial('_item_part', ['model'=>$item]);
	?>
		
	<?endforeach;

	?>
</div>
<br>



<?php
