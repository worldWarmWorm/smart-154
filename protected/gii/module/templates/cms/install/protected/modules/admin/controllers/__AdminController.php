<?php echo "<?php\n"; ?>
Yii::setPathOfAlias('<?=$this->moduleID?>', Yii::getPathOfAlias('application.modules.<?=$this->moduleID?>'));
Yii::import('<?=$this->moduleID?>.<?=$this->moduleClass?>');

class <?=ucfirst($this->moduleID)?>Controller extends \<?=$this->moduleID?>\modules\admin\controllers\DefaultController
{	
}