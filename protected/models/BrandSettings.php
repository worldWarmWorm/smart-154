<?php
/**
 * Created by JetBrains PhpStorm.
 * User: Alexok
 * Date: 28.11.11
 * Time: 15:28
 */ 

use common\components\helpers\HArray as A;

class BrandSettings extends \CFormModel
{
	use \common\traits\Singleton;
	
    public $meta_h1;
    public $meta_title;
    public $meta_key;
    public $meta_desc;
    public $index_page_content='';
    public $index_page_content_pos_footer=0;
    
    public $id=1;
    public $isNewRecord=false;
    
    /**
     * Get static model
     */
    public static function model()
    {
		return self::getInstance();   	
    }

    public function tableName()
    {
    	return 'brand_settings';
    }
    
    /**
     * (non-PHPdoc)
     * @see \CModel::behaviors()
     */
    public function behaviors()
    {
    	return [
			'settingsFormBehavior'=>[
				'class'=>'\common\behaviors\SettingsBehavior',
				'category'=>'brands_settings'
			]
   		];
    }
    
    /**
     * (non-PHPdoc)
     * @see \CModel::rules()
     */
    public function rules()
    {
        return [
            ['meta_h1, meta_title, meta_key, meta_desc, index_page_content, index_page_content_pos_footer', 'safe']
        ];
    }

    /**
     * (non-PHPdoc)
     * @see \CModel::attributeLabels()
     */
    public function attributeLabels()
    {
        return array(
            'meta_h1'=>'H1',
            'meta_title'=>'Meta: title',
            'meta_key'=>'Meta: keywords',
            'meta_desc'=>'Meta: description',
        	'index_page_content'=>'Текст на странице списка брендов',
        	'index_page_content_pos_footer'=>'Расположить текст в конце списка'
        );
    }
}
