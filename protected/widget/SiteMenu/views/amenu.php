<div class="amenu-widget-wrapper">
	<ul id="<?php echo $this->id; ?>" class="amenu-widget-list">
		<?php $this->block($menu, true); ?>
	</ul>
</div>

<script type="text/javascript">
$(document).ready(function(){ 
	$("#<?php echo $this->id; ?>").amenu(<?php echo CJSON::encode($this->options); ?>); 
});
</script>