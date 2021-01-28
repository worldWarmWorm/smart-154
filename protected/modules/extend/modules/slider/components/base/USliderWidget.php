<?php
namespace extend\modules\slider\components\base;

use common\components\helpers\HYii as Y;
use common\components\helpers\HArray as A;
use common\components\helpers\HFile;
use common\components\helpers\HCache;
use common\components\helpers\HTools;
use common\components\helpers\HHash;
use extend\modules\slider\models\Slider;
use common\ext\file\components\helpers\HImage;

class USliderWidget extends \common\components\base\Widget
{
    /**
     * Символьный идентфикатор слайдера.
     * Если не указан будет сгенерирован случаным образом.
     * @var string|null
     */
    public $code=null;
    
    /**
     * Слайды
     * Каждый слайд может быть передан, как строкой (URL изображения),
     * либо массивом [
     *  'src'=>URL_ОРИГИНАЛЬНОГО_ИЗОБРАЖЕНИЯ, 
     *  'url'=>ССЫЛКА_СЛАЙДА,
     *  и дополнительные параметры слайда, аналогично, вида key=>value
     * ]
     * Может быть передана callable функция вида function(&$widget){}, 
     * которая возвращает данные слайдов.
     * Массив слайда будет дополнен элементом 
     * USliderWidget::$dataKey=>ОРИГИНАЛЬНЫЕ_ДАННЫЕ_СЛАЙДА 
     * @var array|callable
     */
    public $slides=[];
    
    /**
     * Имя атрибута URL изображения слайда, 
     * если слайд передан объектом или массивом.
     * По умолчанию "src".
     * @var string
     */
    public $srcAttribute='src';
    
    /**
     * Имя атрибута ссылки слайда
     * если слайд передан объектом или массивом.
     * По умолчанию "url".
     * @var string
     */
    public $urlAttribute='url';
    
    /**
     * Имя ключа в нормализованном массиве, в котором
     * будет доступны входные данные слайда
     * По умолчанию "data".
     * @var string
     */
    public $dataKey='data';
    
    /**
     * Ширина слайда
     * @var integer|null
     */
    public $width=null;
    
    /**
     * Высота слайда
     * @var integer|null
     */
    public $height=null;
    
    /**
     * @var string имя контейнера слайдера. Если не задано, будет сгенерирован.
     * По умолчанию (NULL) - не задано.
     */
    public $container=null;
    
    /**
     * @var string|null имя файла конфигурации настроек виджета.
     * Формат файла конфигурации:
     * array(
     *     'view'=>шаблон отображения,
     *     'options'=>(array) массив параметров инициализации плагина слайдера
     *     ... все доступные public свойства класса виджета
     * )
     */
    public $config=null;
    
    /**
     * (non-PHPdoc)
     * @see \common\components\base\Widget::$tagOptions
     */
    public $tagOptions=['class'=>'slider'];
    
    /**
     * @var string имя тэга списка элементов. По умолчанию "ul".
     */
    public $itemsTagName='ul';
    
    /**
     * @var array дополнительные HTML-атрибуты для тэга списка элементов.
     * По умолчанию пустой массив.
     */
    public $itemsOptions=[];
    
    /**
     * @var string имя тэга элемента списка. По умолчанию "li".
     */
    public $itemTagName='li';
    
    /**
     * @var array атрибуты для тэга элемента списка. По умолчанию пустой массив.
     */
    public $itemOptions=[];
    
    /**
     * @var array дополнительные HTML-атрибуты для ссылки
     */
    public $linkOptions=[];
    
    /**
     * @var array дополнительные HTML-атрибуты для изображения
     */
    public $imageOptions=[];
    
    /**
     * Пропорциональное изменение размера оригинального изображения 
     * @var string
     */
    public $proportional=true;
    
    /**
     * @var bool|null изображение адаптивное
     */
    public $adaptive=true;
    
    /**
     * @var boolean подключать js библиотеки слайдера.
     * По умолчанию (TRUE) - подключать.
     */
    public $jsLoad=true;
    
    /**
     * @var boolean инициализировать слайдер.
     * По умолчанию (TRUE) - инициализировать.
     */
    public $jsInit=true;
    
    /**
     * @var string|boolean файлы скриптов. Может быть передано (FALSE), либо пустое значение,
     * в этом случае, скрипты подключены не будут.
     */
    public $js=false;
    
    /**
     * @var string|boolean файл стилей. Может быть передано (FALSE), либо пустое значение,
     * в этом случае, стили подключены не будут.
     */
    public $css=false;
    
    /**
     * @var string|boolean файл LESS-стилей. Может быть передано (FALSE), либо пустое значение,
     * в этом случае, стили подключены не будут.
     */
    public $less=false;
    
    /**
     * @var array параметры для инициализации плагина слайдера.
     */
    public $options=[];
    
    /**
     * @var string|callable|null дополнительный код слайда.
     * Может быть передана callable значение function($slide, $widget){}.
     */
    public $content=null;
    
    /**
     * @var string|null тэг обертки блока дополнительного контента слайда
     */
    public $contentTag=null;
    
    /**
     * @var array атрибуты для тэга обертки блока дополнительного контента слайда.
     */
    public $contentOptions=[];
    
    /**
     * @var boolean использовать кэширование.
     */
    public $cache=true;
    
    /**
     * @var integer время кэширования в секундах.
     * По умолчанию 60 секунд.
     */
    public $cacheTime=HCache::MINUTE;
    
    /**
     * @var string имя шаблона представления по умолчанию.
     */
    public $view='udefault';
    
    /**
     * @var string|array путь к ресурсам виджета для публикации
     */
    public $assetsPath=null;
    
    /**
     * Входные данные слайдов нормализованы?
     * @var string
     */
    protected $normalized=false;
    
    /**
     * (non-PHPdoc)
     * @see \CWidget::init()
     */
    public function init()
    {
        if(!$this->code) {
            $this->code=HHash::u('slider');
        }
        
        $this->normalizeHtmlOptions();
        
        $this->loadConfig();
        
        if(!$this->container) {
            $this->container='slider_'.$this->code;
        }
        
        if(!A::exists('id', $this->htmlOptions)) {
            $this->htmlOptions['id']=$this->container;
        }
        
        $refCalledClass=new \ReflectionClass(get_called_class());
        $this->setAssetsBasePath(dirname($refCalledClass->getFileName()));
        
        $this->publishAssets();
    }
    
    /**
     * (non-PHPdoc)
     * @see \common\components\base\Widget::run()
     */
    public function run()
    {
        if($this->cache) {
            if(!($content=Y::cache()->get($this->getCacheId()))) {
                $content=$this->render($this->view, $this->params, true);
                Y::cache()->set($this->getCacheId(), $content, $this->cacheTime);
            }
            echo $content;
        }
        else {
            $this->render($this->view, $this->params);
        }
    }
    
    /**
     * Получить слайды
     */
    public function getSlides()
    {
        if(!$this->normalized) {
            if(!is_array($this->slides) && is_callable($this->slides)) {
                $this->slides=call_user_func_array($this->slides, [&$this]);
            }
            
            $slides=[];
            foreach($this->slides as $slide) {
                $_slide=[];
                if(is_string($slide)) {
                    $_slide=['src'=>$slide, 'url'=>null];
                }
                elseif(is_object($slide)) {
                    $_slide=['src'=>$slide->{$this->srcAttribute}, 'url'=>$slide->{$this->urlAttribute}];
                }
                elseif(is_array($slide)) {
                    $_slide=['src'=>A::get($slide, $this->srcAttribute), 'url'=>$slide->{$this->urlAttribute}];
                }
                
                if(!empty($_slide)) {
                    $_slide[$this->dataKey]=$slide;
                    $slides[]=$_slide;
                }
            }
            
            $this->slides=$slides;
            $this->normalized=true;
        }
        
        return $this->slides;
    }
    
    /**
     * Регистрация javascript кода.
     * @param string $code код скрипта.
     * @param boolean $forcy принудительно зарегистрировать скрипт.
     * По умолчанию (FALSE) будет опубликован, если SliderWidget::$jsInit
     * установлено в TRUE.
     * @param string|null $id идентификатор кода.
     * По умолчанию (NULL) будет взят из параметра SliderWidget::$container
     */
    public function registerScript($code, $forcy=false, $id=null)
    {
        if($forcy || $this->jsInit) {
            if(!$id) $id=$this->container;
            Y::js($id, $code, \CClientScript::POS_READY);
        }
    }
    
    /**
     * Получить jQuery выражение выборки DOM-элемента списка слайдов.
     * @return string
     */
    public function getJQueryItemsSelector()
    {
        if(A::exists('class', $this->itemsOptions)) {
            $selector='.' . preg_replace('/\s+/', '.', $this->itemsOptions['class']);
        }
        else {
            if(!A::exists('id', $this->itemsOptions)) $this->itemsOptions['id']=HHash::u('id');
            $selector='#' . $this->itemsOptions['id'];
        }
        
        return $selector;
    }
    
    /**
     * Получить имя конфигурации слайдера.
     * @return number
     */
    public function getConfig()
    {
        return $this->config;
    }
    
    /**
     * Получить ширину изображения слайдера.
     * @return number
     */
    public function getWidth()
    {
        if($this->width) {
            return $this->width;
        }
        
        return Slider::WIDTH;
    }
    
    /**
     * Получить высоту изображения слайдера.
     * @return number
     */
    public function getHeight()
    {
        if($this->height) {
            return $this->height;
        }
        
        return Slider::HEIGHT;
    }
    
    /**
     * Получить значение пропорционального преобразования изображения слайдера.
     * @return boolean
     */
    public function isProportional()
    {
        return $this->proportional;
    }
    
    /**
     * Получить значение параметра использования адаптивного изображения слайдера.
     * @return boolean
     */
    public function isAdaptive()
    {
        return $this->adaptive;
    }
    
    /**
     * Для совместимости с шаблонами слайдера Slick
     * @return boolean
     */
    public function isLink()
    {
        return true;
    }
    
    /**
     * Для совместимости с шаблонами слайдера Slick
     * @return boolean
     */
    public function isDescription()
    {
        return true;
    }
    
    /**
     * Получить тэг <img> для слайда
     * @param string|array $slide оригинальное изображение слайда, 
     * либо элемент слайда
     * @return string
     */
    public function getSlideImage($slide, $htmlOptions=[])
    {
        return HImage::img(($this->getSlideSrc($slide) ?: A::get($slide, 'src')), A::m($this->imageOptions, $htmlOptions));
    }
    
    /**
     * Получить url изображения слайда
     * @param string|array $slide оригинальное изображение слайда, 
     * либо элемент слайда
     * @return string
     */
    public function getSlideSrc($slide)
    {
        return HImage::tmb(A::get($slide, 'src'), $this->getWidth(), $this->getHeight(), [
            'proportional'=>$this->isProportional(),
            'adaptive'=>$this->isAdaptive()
        ]);
    }
    
    /**
     * Загрузка конфигурации плагина
     */
    protected function loadConfig()
    {
        $config=$this->getConfig();
        if($config) {
            if(strpos($config, '.') !== false) {
                $config=HFile::includeByAlias($config, []);
            }
            else {
                $folder=HTools::getShortClassName(get_called_class(), true);
                $config=HFile::includeByAlias(
                    "extend.modules.slider.widgets.configs.{$folder}.{$config}",
                    [],
                    ['widget'=>$this]
                );
            }
            
            if(!is_string($config) && is_callable($config)) {
                $config=call_user_func($config, $this);
            }
            
            if(is_array($config)) {
                foreach($config as $property=>$value) {
                    if(property_exists($this, $property)) {
                        if(($property == 'options') && is_array($this->options)) {
                            $this->$property=A::m($value, $this->options);
                        }
                        else {
                            $this->$property=$value;
                        }
                    }
                }
            }
            
            $this->normalizeHtmlOptions();
        }
    }
    
    /**
     * Получить идентификатор кэша.
     */
    protected function getCacheId()
    {
        return $this->container . $this->code;
    }
    
    /**
     * Публикация ресурсов виджета
     * @param string|array $path путь к ресурсам виджета относительно папки assets
     */
    protected function publishAssets($path=true)
    {
        $js=false;
        if($this->jsLoad) $js=$this->js;
        
        return $this->publish($js, A::m(A::toa($this->css), A::toa($this->less)), $path);
    }
    
    /**
     * Нормализовать значения параметров htmlOptions
     * @param array $names массив имен параметров.
     * Если пуст будет использован массив основных имен параметров:
     * ['htmlOptions', 'tagOptions', 'itemsOptions', 'itemOptions', 'linkOptions', 'imageOptions']
     */
    protected function normalizeHtmlOptions($names=[])
    {
        if(empty($names)) {
            $names=['htmlOptions', 'tagOptions', 'itemsOptions', 'itemOptions', 'linkOptions', 'imageOptions'];
        }
        
        foreach($names as $property) {
            if(is_string($this->$property)) {
                $this->$property=['class'=>$this->$property];
            }
        }
    }
}
