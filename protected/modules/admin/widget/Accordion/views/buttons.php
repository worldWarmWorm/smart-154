<div class="accordion-buttins">
	<div><b>Вставить аккордеон:</b></div>
	<?php foreach($accordion_list as $accordion):?>
		<div data-id="{accordion_<?=$accordion->id?>}" class="elementToInsert btn btn-primary btn-small">
			<?=$accordion->title?>
		</div>
	<?endforeach;?>
	<hr>
</div>

<script type="text/javascript">
	$(document).ready(function(){
		$('.elementToInsert').on('click', function(){
			var data_id = $(this).data('id')
			var ed  = tinyMCE.activeEditor;
			ed.focus();
			ed.execCommand('mceInsertContent', false, data_id);
			ed.dom.setAttrib('__img_tmp', 'src', src);
			ed.dom.setAttrib('__img_tmp', 'alt', alt_title);
			ed.dom.setAttrib('__img_tmp', 'title', alt_title);
			ed.dom.setAttrib('__img_tmp', 'id', '');
		});
	});

</script>