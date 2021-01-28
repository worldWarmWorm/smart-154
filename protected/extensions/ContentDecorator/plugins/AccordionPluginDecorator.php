<?php
/**
 * Created by JetBrains PhpStorm.
 * User: Rick
 * Date: 11.11.11
 * Time: 9:29
 * To change this template use File | Settings | File Templates.
 */ 
class AccordionPluginDecorator extends PluginDecorator
{   
    public function processModel($model, $attribute = 'text') 
    {
        Yii::app()->getClientScript()->registerCoreScript( 'jquery.ui' );
        $accordion_list = Accordion::model()->findAll(['select'=>'id']);
        if(!empty($accordion_list)) {
            foreach($accordion_list as $acc){
                $this->point = '{accordion_' . $acc->id . '}';
                if(empty($acc) || empty($acc->items)) {
                    continue;
                }
                
                $items=$acc->items;
                foreach($items as &$item) 
                	ContentDecorator::decorate($item, 'description', ['accordion']);

                $acc->items=$items;
                $accordion_content = Yii::app()->getController()->renderPartial('//plugins/accordion', array('model'=>$acc->items), true);
                $model->$attribute = $this->replace($model->$attribute, $accordion_content);
            }
        }
		$model->$attribute=preg_replace('/\{accordion_\d+\}/i', '', $model->$attribute);
    }

}
