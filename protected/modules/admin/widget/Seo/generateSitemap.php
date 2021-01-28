<?php
class generateSitemap extends CInputWidget {

    private function _registerJs()
    {
        $assetsUrl = Yii::app()->getAssetManager()->publish(Yii::getPathOfAlias('admin.widget.Seo.assets'));
        $cs        = Yii::app()->clientScript;

        $cs->registerScriptFile($assetsUrl . '/js/request.js', CClientScript::POS_BEGIN);

    }

    public function run()
    {	
    	$this->_registerJs();
		$this->render('generateButton');
    }
}