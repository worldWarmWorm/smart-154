<?php
/* @var $this iblock\controllers\AdminController */
/* @var $model iblock\models\InfoBlock */
/* @var $prop_model iblock\models\InfoBlockProp */
/* @var $form CActiveForm */

use iblock\models\InfoBlockProp;
?>
<!--todo: вынести в файл стилей в тему -->
<style>
	#iblock-properties-table tbody tr td:nth-child(1) input,
	#iblock-properties-table tbody tr td:nth-child(2) input,
	#iblock-properties-table tbody tr td:nth-child(8) input,
	#iblock-properties-table tbody tr td:nth-child(3) select { width: 140px; }
	#iblock-properties-table tbody tr td:nth-child(7) input { width: 60px; }
</style>

<table id="iblock-properties-table" class="adminList table table-striped table-hover">
    <thead>
        <tr>
            <th><?=$prop_model->getAttributeLabel('code')?>
            <th><?=$prop_model->getAttributeLabel('title')?>
            <th><?=$prop_model->getAttributeLabel('type')?>
            <th title='<?=$prop_model->getAttributeLabel('active')?>'>А</th>
            <th title='<?=$prop_model->getAttributeLabel('multiple')?>'>М</th>
            <th title='<?=$prop_model->getAttributeLabel('required')?>'>О</th>
            <th><?=$prop_model->getAttributeLabel('sort')?>
            <th><?=$prop_model->getAttributeLabel('default')?>
            <th>Удалить</th>
        </tr>
    </thead>
    <tbody>
        <?php
        $i = 0;
        $property_types = InfoBlockProp::getTypesList();
        if ($properties = $model->infoBlockProps) {
            foreach ($properties as $i => $p) { ?>
                <tr id="iblock-old-prop-row-<?php echo $p->id; ?>" class="old_prop row<?php echo $i % 2 ? 0 : 1; ?>"
                    valign="top">
                    <td class="data_column"><?= CHtml::textField('properties[' . $p->id . '][code]', $p->code) ?></td>
                    <td class="data_column"><?= CHtml::textField('properties[' . $p->id . '][title]', $p->title) ?></td>
                    <td class="data_column"><?= CHtml::dropDownList('properties[' . $p->id . '][type]', $p->type, $property_types, array('prompt' => '')) ?></td>
                    <td class="data_column"><?= CHtml::checkBox('properties[' . $p->id . '][active]', (bool)$p->active) ?></td>
                    <td class="data_column"><?= CHtml::checkBox('properties[' . $p->id . '][multiple]', (bool)$p->multiple) ?></td>
                    <td class="data_column"><?= CHtml::checkBox('properties[' . $p->id . '][required]', (bool)$p->required) ?></td>
                    <td class="data_column"><?= CHtml::textField('properties[' . $p->id . '][sort]', $p->sort) ?></td>
                    <td class="data_column"><?= CHtml::textField('properties[' . $p->id . '][default]', $p->default) ?></td>
                    <td class="data_column"><?= CHtml::checkBox('properties[' . $p->id . '][delete]', false) ?></td>
                </tr>
            <?php }
        } ?>
        <tr id="iblock-new-prop-row-0" class="new_prop row<?php echo $i % 2 ? 0 : 1; ?>" valign="top">
            <td class="data_column"><?=CHtml::textField('new_properties[0][code]', '')?></td>
            <td class="data_column"><?=CHtml::textField('new_properties[0][title]', '')?></td>
            <td class="data_column"><?=CHtml::dropDownList('new_properties[0][type]', '', $property_types, array('prompt'=>''))?></td>
            <td class="data_column"><?=CHtml::checkBox('new_properties[0][active]', true)?></td>
			<td class="data_column"><?=CHtml::checkBox('new_properties[0][multiple]', false)?></td>
			<td class="data_column"><?=CHtml::checkBox('new_properties[0][required]', false)?></td>
            <td class="data_column"><?=CHtml::textField('new_properties[0][sort]', InfoBlockProp::DEFAULT_SORT)?></td>
            <td class="data_column"><?=CHtml::textField('new_properties[0][default]', '')?></td>
            <td class="data_column"></td>
        </tr>
	</tbody>
	<thead>
		<tr>
			<td colspan="9">
				<button id="add-iblock-property" title="Добавить свойство" class="btn btn-success">+</button>
			</td>
		</tr>
	</thead>
</table>
<script type="text/javascript">
    $(function() {
        IblockAdmin.init();
    });
</script>
