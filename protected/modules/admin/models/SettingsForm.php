<?php
/**
 * Created by JetBrains PhpStorm.
 * User: Alexok
 * Date: 01.06.11
 * Time: 15:27
 */
use common\components\helpers\HYii as Y;
use AttributeHelper as A;
/**
 * Class SettingsForm
 *
 */
class SettingsForm extends \CFormModel
{
    const DEV_SCENARIO='dev';
    
    public static $files = array();
    /**
     * @var array имена файлов. В формате array(attribute=>name),
     * где attribute - имя атрибута файла из SettingsForm::$files,
     * name - произвольное имя файла, без расширения.
     */
    public static $filesNames = array();
    
    public $slogan;
    public $address;
    public $sitename;
    public $phone;
    public $phone2;
    public $email;
    public $emailPublic;
    public $firm_name;
    public $counter;
    public $hide_news;
    public $menu_limit;
    public $cropImages;
    public $comments;
    public $meta_title;
    public $meta_key;
    public $meta_desc;
    public $watermark;
    public $blog_show_created;
    public $copyright_city;
    public $favicon;
    
    public $privacy_policy;
    public $privacy_policy_text;
    /**
     * @var CUploadedFile
     */
    public $faviconFile;
    
    // sitemap
    public $sitemap_priority;
    public $sitemap_changefreq;
    public $sitemap_auto;
    public $sitemap_auto_generate;

    // Slider
    public $slider_many;
    
    // Tree Menu
    public $treemenu_fixed_id;
    public $treemenu_show_id;
    public $treemenu_show_breadcrumbs;
	public $treemenu_depth;
    // Question (FAQ)
    public $question_collapsed;
    // Shop
    public $shop_title;
	public $shop_pos_description;
	public $shop_enable_attributes;
	public $shop_enable_reviews;
	public $shop_enable_carousel;
	public $shop_enable_hit_on_top;
	public $shop_category_descendants_level;
	public $shop_enable_brand;
    public $shop_enable_old_price;
    public $shop_product_page_size;
    public $shop_show_categories;
    public $shop_menu_enable;
    public $shop_menu_level;
        
    // Gallery
    public $gallery_title;
    // Events
    public $events_title;
    public $events_link_all_text;
    // Sale
    public $sale_title;
    public $sale_link_all_text;
    public $sale_preview_width;
    public $sale_preview_height;
    public $sale_meta_h1;
    public $sale_meta_title;
    public $sale_meta_key;
    public $sale_meta_desc;
    
    // TinyMCE
    public $tinymce_adaptivy;
    public $tinymce_full_toolbars;
    public $tinymce_allow_scripts;
    public $tinymce_allow_iframe;
    public $tinymce_allow_object;

	// SEO
	public $seo_yandex_verification;
	
	// system
	public $system_admins;
    
    /**
     * @var array $_defaults массив значений по умолчанию. array(attribute=>value)
     */ 
    private $_defaults=array(
    	'shop_title'=>'Kаталог',
    	'shop_enable_reviews'=>1,
    	'gallery_title'=>'Фотогалерея',
    	'events_title'=>'Новости',
    	'events_link_all_text'=>'Все новости',
    	'sale_title'=>'Акции',
    	'sale_link_all_text'=>'Все акции',
    	'sale_preview_width'=>320,
    	'sale_preview_height'=>240,
		'treemenu_depth'=>1,
		'copyright_city'=>'в Новосибирскe',
		'shop_category_descendants_level'=>99,
        'sitemap_priority'=>1,
        'sitemap_changefreq'=>'weekly',
        'sitemap_auto'=>'',
        'shop_product_page_size'=>12,
        'sitemap_auto_generate'=>0,
        'tinymce_allow_iframe'=>1,
        'tinymce_allow_object'=>1,
        'shop_menu_url'=>'/catalog',
        'shop_menu_level'=>1,
    );
    /**
     * @var array $_last массив предыдущих значений. array(attribute=>value). 
     * Необходим для корректного сохранения.
     */
    private $_last=array();

    public $sitemap;
    
    public function isDevMode()
    {
        return ($this->scenario == self::DEV_SCENARIO);
    }

    public function rules()
    {
    	$rules =  array(
            array('email, emailPublic', 'email'),
            array('slogan, address, sitename, phone, phone2, email, emailPublic, firm_name, counter, hide_news, menu_limit', 'safe'),
            array('comments, meta_title, meta_key, meta_desc, cropImages, watermark, blog_show_created, sitemap', 'safe'),
            array('events_title, events_link_all_text, copyright_city, favicon, privacy_policy, privacy_policy_text', 'safe'),
            array('faviconFile', 'file', 'allowEmpty'=>true, 'types'=>'ico, png'),
            ['sitemap_priority, sitemap_changefreq, sitemap_auto, sitemap_auto_generate', 'safe'],
            ['tinymce_adaptivy, tinymce_allow_scripts, tinymce_allow_iframe, tinymce_allow_object, tinymce_full_toolbars', 'safe'],
			['seo_yandex_verification, system_admins', 'safe']
        );
    	if(D::yd()->isActive('gallery') && $this->isDevMode()) {
    		$rules = \CMap::mergeArray($rules, array(
            	array('gallery_title', 'safe')
    		));
    	}
    	if(D::yd()->isActive('slider') && $this->isDevMode()) {
        	$rules = \CMap::mergeArray($rules, [
            	['slider_many', 'boolean'],
        	]);
        }
        if(D::yd()->isActive('treemenu') && $this->isDevMode()) {
            $rules = \CMap::mergeArray($rules, array(
            	array('treemenu_fixed_id', 'match', 'pattern'=>'/^[0-9,]+$/', 'message'=>'Разрешены только цифры и запятая'),
            	array('treemenu_fixed_id, treemenu_show_id, treemenu_show_breadcrumbs, treemenu_depth', 'safe')
        	));
        }
        if(D::yd()->isActive('question') && $this->isDevMode()) {
            $rules = \CMap::mergeArray($rules, array(
            	array('question_collapsed', 'safe')
        	));
        }
        if(D::yd()->isActive('shop') && $this->isDevMode()) {
            $rules = \CMap::mergeArray($rules, array(
            	array('shop_title', 'required'),
            	array('shop_category_descendants_level', 'numerical', 'integerOnly'=>true),
            	array('shop_title, shop_enable_hit_on_top, shop_pos_description, shop_enable_attributes, shop_show_categories', 'safe'),
                array('shop_enable_old_price, shop_enable_reviews, shop_enable_carousel, shop_enable_brand, shop_product_page_size', 'safe'),
                array('shop_menu_enable, shop_menu_url, shop_menu_level', 'safe'),
        	));
        }
        if(D::yd()->isActive('sale') && $this->isDevMode()) {
            $rules = \CMap::mergeArray($rules, array(
            	array('sale_title, sale_link_all_text, sale_preview_width, sale_preview_height', 'safe'),
            	array('sale_meta_h1, sale_meta_title, sale_meta_key, sale_meta_desc', 'safe')
        	));
        }

        return $rules;
    }

    public function validateRequiredBy($attribute, $params)
    {
    	$attributeBy=$params['attribute'];
    	if($this->$attributeBy && !$this->$attribute) {
			$this->addError($attribute, 'Поле "'.$this->attributeLabels()[$attribute].'" является обязательным для заполнения');
			return false;
    	}
    	return true;
    }

    public function attributeLabels()
    {
        return array(
            'slogan'=>'Слоган сайта',
            'address'=>'Контактные данные',
            'sitename'=>'Название сайта',
            'phone'=>'Телефон',
            'phone2'=>'Дополнительный телефон',
            'email'=>'Email администратора',
            'emailPublic'=>'Email на сайте',
            'firm_name'=>'Название организации',
            'counter'=>'Счетчики',
            'hide_news'=>'Скрыть новости',
            'menu_limit'=>'Кол-во пунктов меню',
            'cropImages'=>'Обрезка изображений',
            'comments'=>'Код комментариев',
            'meta_title'=>'SEO заголовок',
            'meta_key'=>'Ключевые слова',
            'meta_desc'=>'Описание',
            'watermark'=>'Водяной знак',
            'blog_show_created'=>'Показывать дату создания',
            'copyright_city' => 'Название города в копирайтах',
            'favicon' => 'Иконка сайта',
        	'privacy_policy'=>'Страница "Политика обработки данных"',
        	'privacy_policy_text'=>'Текст в подвале сайта о политике обработки данных',

            // sitemap
            'sitemap_priority'=>'Приоритетность URL относительно других URL на Вашем сайте по умолчанию',
            'sitemap_changefreq'=>'Вероятная частота изменения этой страницы по умолчанию',
            'sitemap_auto'=>'Время автоматического обновления карты сайта',
            'sitemap_auto_generate'=>'Автоматически обновлять карту сайта при создании, изменения или удаления Категории, Товара, Новости или Страницы',

            // slider
            'slider_many'=>'Разрешено использовать несколько слайдеров',
            
            // treemenu
            'treemenu_fixed_id'=>'Id фиксированных пунктов меню (через запятую)',
            'treemenu_show_id'=>'Показать id menu',
            'treemenu_show_breadcrumbs'=>'Показать "хлебные крошки"',
			'treemenu_depth'=>'Максимальная вложенность меню',
            // question
            'question_collapsed'=>'Свернуть ответы',
        	// shop
        	'shop_title'=>'Название магазина',
        	'shop_pos_description'=>'Позиция текста описания категории',
        	'shop_enable_attributes'=>'Активировать аттрибуты товара',
        	'shop_enable_reviews'=>'Отзывы на товар',
        	'shop_enable_carousel'=>'Активировать блок "Популярные товары"',
        	'shop_enable_hit_on_top'=>'Отображать "Акция", "Хит", "Новинка" в начале списка товаров',
        	'shop_category_descendants_level'=>'Уровень вложенности категорий, из которых будут получаться товары',
        	'shop_enable_brand'=>'Активировать "Бренды"',
            'shop_enable_old_price'=>'Активировать функционал "Старая цена"',
            'shop_product_page_size'=>'Кол-во товаров на странице',
            'shop_show_categories'=>'Показать список категорий',
            'shop_menu_enable'=>'Активировать выпадающий список категорий в меню', 
            'shop_menu_level'=>'Уровень вложенности категорий в меню',
        	// gallery
        	'gallery_title'=>'Название фотогалереи',
        	// events
        	'events_title'=>'Название модуля новостей',
        	'events_link_all_text'=>'Текст ссылки "Все новости"',
            // sitemap 
            'sitemap'=>'Дополнение к карте сайта',
            
        	// sale
        	'sale_title'=>'Название акций',
        	'sale_link_all_text'=>'Текст ссылки "Все акции"',
        	'sale_preview_width'=>'Ширина изображения анонса',
        	'sale_preview_height'=>'Высота изображения анонса',
        	'sale_meta_title'=>'META: Заголовок',
        	'sale_meta_key'=>'META: Ключевые слова',
        	'sale_meta_desc'=>'META: Описание',
        	'sale_meta_h1'=>'H1',
            
            // tinymce
            'tinymce_adaptivy'=>'Включить адаптивный режим', 
            'tinymce_full_toolbars'=>'Полная панель управления',
            'tinymce_allow_scripts'=>'Разрешить вставку скриптов',
            'tinymce_allow_iframe'=>'Разрешить вставку тэгов IFRAME', 
            'tinymce_allow_object'=>'Разрешить вставку тэгов OBJECT', 

			// seo
			'seo_yandex_verification'=>'Код подтверждения yandex-verification',
            
            // system
            'system_admins'=>'Включить функционал дополнитильных адинистраторов'
        );
    }

    public function saveSettings()
    {
        // save files
        $uploadPath = Yii::getPathOfAlias('webroot') . Yii::app()->params['uploadSettingsPath'];

        foreach($this::$files as $fileAttribute) {
            $oldFile = Yii::app()->request->getPost($fileAttribute . '_file');

            $this->{$fileAttribute} = CUploadedFile::getInstance($this, $fileAttribute);

            if(is_object($this->{$fileAttribute})) {
             	if(isset(self::$filesNames[$fileAttribute])) {
                    $filePreviewName=self::$filesNames[$fileAttribute];
                }
                else {
                    $filePreviewName=uniqid();
                }
                $previewName = $filePreviewName . '.' . $this->{$fileAttribute}->extensionName;

                $this->{$fileAttribute}->saveAs($uploadPath . $previewName);
                $this->{$fileAttribute} = $previewName;

                if(!empty($oldFile) && ($oldFile != $previewName)) {
                    $delete = $uploadPath . DS . $oldFile;
                    if(file_exists($delete)) unlink($delete);
                }
            }

            if(empty($this->{$fileAttribute}) && !empty($oldFile)) $this->{$fileAttribute} = $oldFile;
        }

    	// shop
    	if(D::yd()->isActive('shop') && D::role('admin')) {
    		if($this->shop_title != $this->_last['shop_title']) {
    			$menu = \Menu::model()->find('options=:options', array(':options'=>'{"model":"shop"}'));
    			if(!$menu) throw new Exception('Shop module install failed');
    			$menu->title = $this->shop_title;
    			$menu->save();
    		}
    	}
    	// gallery
    	if(D::yd()->isActive('gallery') && D::role('admin')) {
	    	if($this->gallery_title != $this->_last['gallery_title']) {
    			$menu = \Menu::model()->find('options=:options', array(':options'=>'{"model":"gallery"}'));
    			if($menu) {
		    		$menu->title = $this->gallery_title;
    				$menu->save();
    			}
    		}
    	}
    	// events
    	if(D::role('admin') && ($this->events_title != $this->_last['events_title'])) {
    		$menu = \Menu::model()->find('options=:options', array(':options'=>'{"model":"event"}'));
    		if($menu) {
	    		$menu->title = $this->events_title;
    			$menu->save();
    		}
    	}
    	// sale
    	if(D::yd()->isActive('sale') && D::role('admin')) {
	    	if($this->sale_title != $this->_last['sale_title']) {
	    		$menu = \Menu::model()->find('options=:options', array(':options'=>'{"model":"sale"}'));
	    		if($menu) {
		    		$menu->title = $this->sale_title;
	    			$menu->save();
	    		}
	    	}
    	}
        
        $this->faviconFile = CUploadedFile::getInstance($this, 'faviconFile');
        if ($this->faviconFile instanceof CUploadedFile) {
            $this->removeFavicon();
            $this->createFavicon();
        }

        $attributes = $this->attributes;
        unset($attributes['faviconFile']);

        Yii::app()->settings->set('cms_settings', $attributes);
    }

    public function removeFavicon(){
        $theme = Yii::app()->getTheme();
        $webroot = Yii::getPathOfAlias('webroot');
        unlink($theme->basePath . DS . 'favicon.png');
        unlink($theme->basePath . DS . 'favicon.ico');
        unlink($webroot . DS . 'favicon.png');
        unlink($webroot . DS . 'favicon.ico');
        $this->favicon = '';
    }

    private function createFavicon()
    {
        $webroot = Yii::getPathOfAlias('webroot');
        $ext = strtolower($this->faviconFile->extensionName);
        $name = 'favicon.' .$ext;
        $this->faviconFile->saveAs($webroot . DS . $name);
        $this->favicon = $ext . '?'. time();
        $this->faviconFile = null;
        // очистка всего кэша
        \Yii::app()->cache->flush();
    }
    
    public function loadSettings()
    {
    	$fSetDefault=function($attribute, $attributeBy, $expr=true) {
    		if($expr && ($attribute == $attributeBy)) {
    			if($this->$attribute===null) $this->$attribute = $this->_defaults[$attributeBy];
    			$this->_last[$attributeBy] = $this->$attribute;
    		}
    	};
        foreach($this->attributeNames() as $attr) {
            $this->$attr = Yii::app()->settings->get('cms_settings', $attr);
            $fSetDefault($attr, 'shop_title', (D::yd()->isActive('shop') && D::role('admin')));
            $fSetDefault($attr, 'shop_category_descendants_level', (D::yd()->isActive('shop') && D::role('admin')));
			$fSetDefault($attr, 'shop_product_page_size', (D::yd()->isActive('shop') && D::role('admin')));
            $fSetDefault($attr, 'gallery_title', (D::yd()->isActive('gallery') && D::role('admin')));
            $fSetDefault($attr, 'events_title', (D::role('admin')));
            $fSetDefault($attr, 'events_link_all_text', (D::role('admin')));
            $fSetDefault($attr, 'sale_title', (D::yd()->isActive('sale') && D::role('admin')));
            $fSetDefault($attr, 'sale_link_all_text', (D::yd()->isActive('sale') && D::role('admin')));
            $fSetDefault($attr, 'sale_preview_width', (D::yd()->isActive('sale') && D::role('admin')));
            $fSetDefault($attr, 'sale_preview_height', (D::yd()->isActive('sale') && D::role('admin')));
			$fSetDefault($attr, 'treemenu_depth', D::role('admin'));
			$fSetDefault($attr, 'copyright_city', D::role('admin'));
			$fSetDefault($attr, 'sitemap_priority', D::role('admin'));
			$fSetDefault($attr, 'sitemap_changefreq', D::role('admin'));
			$fSetDefault($attr, 'sitemap_auto', D::role('admin'));
			$fSetDefault($attr, 'sitemap_auto_generate', D::role('admin'));
			$fSetDefault($attr, 'tinymce_allow_iframe', D::role('admin'));
			$fSetDefault($attr, 'tinymce_allow_object', D::role('admin'));
			$fSetDefault($attr, 'shop_menu_level', $this->isDevMode());
        }
    }

}
 
