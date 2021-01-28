<?php
/**
 * Created by JetBrains PhpStorm.
 * User: Rick
 * Date: 30.09.11
 * Time: 19:11
 * To change this template use File | Settings | File Templates.
 */
 
class CmsHtml
{
	/**
	 * Путь относительно webroot к директории для откомпелированных less файлов
	 * @var string
	 */
	const LESS_COMPILE_DIR = '/assets/css';

    static $_state = array();

    private function jcrop_admin_plugins(){
        Yii::app()->clientScript->registerScriptFile('/js/jquery/jquery.jcrop.min.js');
        Yii::app()->clientScript->registerCssFile('/css/jcrop/jquery.jcrop.css');
    }

    public static function editPricePlugin()
    {
    }

    public static function head()
    {

        if(Yii::app()->user->getState('role')=='admin'){
            self::jcrop_admin_plugins(); 
        }
        self::jquery();
        // self::css();
        self::less();
        self::metaTags();
        self::noskype();
        self::fancybox();
    }

    public static function css($files = array())
    {
        if (!$files)
            $files = array('editor.css', 'form.css', 'question.css', 'review.css', 'template.css', 'style.css');

        $cs = Yii::app()->clientScript;

        if(is_string($files)) $files = array($files);

        foreach($files as $file) {
            if ($path = self::getCssPath($file)) {
                $cs->registerCssFile($path.'/'.$file);
            }
        }
    }

    public static function less($files=array()) 
    {
    	if (!$files)
    		$files = array('client.less', 'template.less');
		
		if(!is_dir(\Yii::getPathOfAlias('webroot') . self::LESS_COMPILE_DIR)) 
			mkdir(\Yii::getPathOfAlias('webroot') . self::LESS_COMPILE_DIR);
        
        $cs = Yii::app()->clientScript;

		foreach($files as $file) {
        	if ($path = self::getCssPath($file)) {
		 		$cssFile = \Yii::app()->assetManager->lessCompile(\Yii::getPathOfAlias('webroot') . $path . '/' . $file);
				$destCssFile = preg_replace('/^(.*)([^\/\\\\]+)(.less)$/U', '\\1\\2.css', $file);
				copy($cssFile, \Yii::getPathOfAlias('webroot') . $path . '/' . $destCssFile);
 				$cs->registerCssFile($path . '/' . $destCssFile);
			}
		}
	}

    private static function getCssPath($file_name)
    {
        if (is_file(Yii::getPathOfAlias('webroot.css').DS.$file_name)) {
            return '/css';
        }

        $theme = Yii::app()->theme;
        if (is_file(Yii::getPathOfAlias('webroot.themes.'.$theme->name.'.css') .DS. $file_name)) {
            return $theme->baseUrl.'/css';
        }

        return false;
    }

    /**
     * Find all allowed css files
     *
     * @static
     * @return array
     */
    private static function findAllCss()
    {
        $paths = array(
            'css',
            'themes.'.Yii::app()->theme->name.'.css'
        );

        $exclude = array('.', '..');

        $result = array();

        foreach($paths as $path) {
            $files = scandir(Yii::getPathOfAlias('webroot.'.$path));
            foreach($files as $file) {
                if (in_array($file, $exclude))
                    continue;
                $result[] = $file;
            }
        }
        return $result;
    }

    public static function jquery()
    {
        Yii::app()->clientscript->registerCoreScript('jquery');
    }

    public static function js($src = '', $jquery = true)
    {
        if (empty($src))
            throw new CException('Не указан js-файл');

        if (is_array($src)) {
            foreach($src as $link)
                Yii::app()->clientScript->registerScriptFile($link);
        } else
            Yii::app()->clientScript->registerScriptFile($src);

    }

    public static function noskype()
    {
        Yii::app()->clientScript->registerMetaTag('SKYPE_TOOLBAR_PARSER_COMPATIBLE', 'SKYPE_TOOLBAR');
    }

    public static function fancybox()
    {
        if (isset(self::$_state['fancybox']))
            return;

        $cs = Yii::app()->clientScript;

        $cs->registerCoreScript('jquery');
        $cs->registerScriptFile('/js/fancybox/jquery.fancybox.min.js');
        $cs->registerCssFile('/js/fancybox/jquery.fancybox.min.css');
        $cs->registerScript('fancybox-settings', '$.fancybox.defaults.hash = false;');

        self::$_state['fancybox'] = true;
    }

    public static function metaTags()
    {
        echo "<title>". CHtml::encode(Yii::app()->controller->pageTitle)."</title>\n";

        $cs = Yii::app()->clientScript;

        $cs->registerMetaTag(null, null, null, array('charset'=>'utf-8'));
        $cs->registerMetaTag('index, follow', 'robots');

        self::addFavicon();
        self::seoTags();
    }

    public static function seoTags($metadata = array())
    {
        $meta_key  = Yii::app()->controller->meta_key;
        $meta_desc = Yii::app()->controller->meta_desc;

        $cs = Yii::app()->clientScript;

        if (!empty($meta_key))
            $cs->registerMetaTag($meta_key, 'keywords');

        if (!empty($meta_desc))
            $cs->registerMetaTag($meta_desc, 'description');
    }

    private static function getFaviconType($icon) {
        if (strpos($icon, '.png') !== false) {
            return 'image/png';    
        }
        return 'image/x-icon';
    }

    private static function addFavicon()
    {
        /** @var EClientScript $cs */
        $cs   = Yii::app()->clientScript;
        $icon =  \Yii::app()->settings->getCurrentFavicon();
        $cs->registerLinkTag('shortcut icon', static::getFaviconType($icon) , $icon);

    }
    
}
