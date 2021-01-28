<?php
/**
 * Настройки магазина
 * 
 */
use common\components\helpers\HArray as A;

class ShopSettings extends \settings\components\base\SettingsModel
{
	/**
	 * SEO
	 */
	public $meta_title;
	public $meta_desc;
	public $meta_key;
	public $meta_h1;
	
	/**
	 * @var string текст на главной странице каталога
	 */
	public $main_text;
    
    /**
	 * @var string второй текст на главной странице каталога
	 */
	public $main_text2;
    
    /**
	 * @var string показывать список категорий на главной странице каталога
	 */
	public $show_categories_on_shop_page=1;
    
    /**
	 * @var string показывать список категорий на страницах категорий каталога
	 */
	public $show_categories_on_category_page=1;
    
    /**
	 * @var string показывать список категорий на страницах категорий каталога (по умолчанию)
	 */
	public $show_categories_on_category_page_default=1;
	
	/**
	 * @var boolean для совместимости со старым виджетом
	 * редактора admin.widget.EditWidget.TinyMCE
	 */
	public $isNewRecord=false;
	
	/**
	 * Для совместимости со старым виджетом
	 * редактора admin.widget.EditWidget.TinyMCE
	 */
	public function tableName()
	{
		return 'shop_settings';
	}
	
	/**
	 * (non-PHPdoc)
	 * @see \common\components\base\FormModel::behaviors()
	 */
	public function behaviors()
	{
		return A::m(parent::behaviors(), [
		]);
	}

	/**
	 * (non-PHPdoc)
	 * @see \settings\components\base\SettingsModel::rules()
	 */
	public function rules()
	{
		return $this->getRules([
			['meta_h1, meta_title, meta_key, meta_desc, main_text, main_text2', 'safe'],
			['show_categories_on_shop_page, show_categories_on_category_page, show_categories_on_category_page_default', 'safe']
		]);
	}
	
	/**
	 * (non-PHPdoc)
	 * @see \settings\components\base\SettingsModel::attributeLabels()
	 */
	public function attributeLabels()
	{
		return $this->getAttributeLabels([
			'meta_h1'=>'H1',
			'meta_title' => 'Meta Title',
			'meta_key' => 'Meta Keywords',
			'meta_desc' => 'Meta Description',
			'main_text'=>'Верхний текст на главной странице каталога',
			'main_text2'=>'Нижний текст на главной странице каталога',
			'show_categories_on_shop_page'=>'Показывать список категорий на главной странице каталога',
			'show_categories_on_category_page'=>'Показывать список категорий на страницах категорий каталога',
			'show_categories_on_category_page_default'=>'Показывать список категорий на страницах категорий каталога (по умолчанию)',
		]);
	}
}
