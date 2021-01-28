<?php
/**
 * Created by JetBrains PhpStorm.
 * User: Alexok
 * Date: 28.11.11
 * Time: 15:28
 */ 
namespace reviews\models;

use common\components\helpers\HArray as A;

class Settings extends \CFormModel
{
	use \common\traits\Singleton;
	
    public $meta_h1;
    public $meta_title;
    public $meta_key;
    public $meta_desc;
    
    public $auto_generate_preview_text=true;
    public $preview_text_length=300;
    public $tmb_width=320;
    public $tmb_height=240;
    public $index_page_content='';
    
    public $isNewRecord=false;
    
    /**
     * Get static model
     */
    public static function model()
    {
		return self::getInstance();   	
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
				'category'=>'reviews_settings'
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
        	['auto_generate_preview_text', 'boolean'],
        	['preview_text_length, tmb_width, tmb_height', 'numerical', 'integerOnly'=>true],
            ['meta_h1, meta_title, meta_key, meta_desc, index_page_content', 'safe']
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
            'tmb_width'=>'Ширина изображения для анонса',
            'tmb_height'=>'Высота изображения для анонса',
        	'index_page_content'=>'Текст на странице списка отзывов',
        	'auto_generate_preview_text'=>'Автоматически генерировать текст анонса',
        	'preview_text_length'=>'Кол-во символов в анонсе при автоматической генерации'
        );
    }
}
