<?php
/**
 * Created by JetBrains PhpStorm.
 * User: Rick
 * Date: 28.08.12
 * Time: 13:25
 * To change this template use File | Settings | File Templates.
 */
class imgCroper extends CWidget
{
    public $params;
    
    public function run()
    {   
    	$assetPrefix = Yii::app()->assetManager->publish(dirname(__FILE__).'/assets/js', true, 0, defined('YII_DEBUG'));
    	Yii::app()->getClientScript()->registerScriptFile( $assetPrefix . '/fancy.js');
    	Yii::app()->getClientScript()->registerScriptFile('/js/jquery.jcrop.min.js');
    	Yii::app()->getClientScript()->registerCssFile('/css/jcrop/jquery.jcrop.css');

    	if($this->params['img']){
	        $this->render('default', compact('params'));
        }
    }
}
