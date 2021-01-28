<?php
/**
 * The following variables are available in this template:
 * - $this: the CrudCode object
 */
?>
<?php echo "<?php\n"; ?>
/* @var $this <?php echo $this->getControllerClass(); ?> */
/* @var $model <?php echo $this->getModelClass(); ?> */
/* @var $form CActiveForm */
?>

<div class="form">

<?php echo "<?php \$form=\$this->beginWidget('CActiveForm', array(
	'id'=>'".$this->class2id($this->modelClass)."-form',
	'enableAjaxValidation'=>false,
)); ?>\n"; ?>


	<?php echo "<?php echo \$form->errorSummary(\$model); ?>\n"; ?>

<?php
foreach($this->tableSchema->columns as $column)
{
	if($column->autoIncrement)
		continue;
?>
	<div class="row">
		<?php echo "<?php echo ".$this->generateActiveLabel($this->modelClass,$column)."; ?>\n"; ?>
		<?php echo "<?php echo \$form->textField(\$model,'".$column->name."',array('class'=>'form-control')); ?>\n"; ?>
		<?php echo "<?php echo \$form->error(\$model,'{$column->name}'); ?>\n"; ?>
	</div>

<?php
}
?>
	<div class="row buttons">
		<div class="left">
			<?php echo "<?php echo CHtml::submitButton(\$model->isNewRecord ? 'Создать' : 'Сохранить', array('class'=>'btn btn-primary')); ?>\n"; ?>
		</div>

		<?php echo "<?php if (!\$model->isNewRecord): ?>\n" ?>
		<div class='left'>
		<?php echo "<a class='btn btn-danger delete-b' href=\"<?=\$this->createUrl('/cp/".$this->controllerId."/delete', array(\"id\"=>\$model->id))?>\"\n"; ?>
		<?php echo "onclick=\"return confirm('Вы действительно хотите удалить запись?');\">\n" ?>
			<span>Удалить</span></a>
		</div>
		<?php echo "<?php endif; ?>\n"; ?>
		<div class="clr"></div>

	</div>

<?php echo "<?php \$this->endWidget(); ?>\n"; ?>

</div><!-- form -->