<?php
/**
 * Created by JetBrains PhpStorm.
 * User: Alexok
 * Date: 28.11.11
 * Time: 15:28
 */ 
class ShopSettingsForm extends CFormModel
{
    public $cropTop;
    
    public $meta_title;
    public $meta_desc;
    public $meta_key;
    public $meta_h1;

    public function rules()
    {
        return array(
            array('cropTop,meta_title,meta_key,meta_desc,meta_h1', 'safe')
        );
    }

    public function attributeLabels()
    {
        return array(
            'cropTop'=>'Позиция обрезки фото товара',
        	'meta_title' => 'Meta Title',
        	'meta_key' => 'Meta Keywords',
        	'meta_desc' => 'Meta Description',
        	'meta_h1'=>'H1',
        );
    }

    public function saveSettings()
    {
        Yii::app()->settings->set('shop_settings', $this->attributes);
    }

    public function loadSettings()
    {
        foreach($this->attributeNames() as $attr) {
            $this->$attr = Yii::app()->settings->get('shop_settings', $attr);
        }
    }
}
