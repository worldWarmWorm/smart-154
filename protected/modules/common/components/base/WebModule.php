<?php
/**
 * Базовый класс для модулей DishCMS 
 *
 */
namespace common\components\base;

use common\components\helpers\HYii as Y;
use common\components\helpers\HArray as A;
use common\components\helpers\HDb;

abstract class WebModule extends \CWebModule
{
	use \common\traits\Singleton;

	/**
	 * @var string алиас пути до файлов миграций.
	 */
	public $migrationAlias=null;
	
	/**
	 * @var boolean автоматически инициализировать.
	 */
	public $autoload=false;
	
	/**
 	 * @var string алиас модуля.
	 */
	protected $baseAlias=null;

	/**
	 * @var string url к опубликованным ресурсам модуля.
	 */
	protected $assetsBaseUrl=null;
	
	/**
	 * @var string путь к опубликованным ресурсам модуля.
	 */
	protected $assetsBasePath=null;
	
	/**
	 * Кэш функций перевода
	 * @var array
	 */
	protected static $t=[];

	/**
	 * Получение перевода или функции перевода
	 * @param string $message сообщение. Если не передано, будет возвращена 
	 * функция перевода. Подробнее в описании метода \common\components\helpers\HYii::ct().
	 * @param [] $params набор переменных для подстановки в текст перевода.
	 * @param string|null $category категория. Если передана будет использована она.
	 * Подробнее в описании метода \Yii::t().
	 * @param string $path путь к файлу перевода относительно основного 
	 * пути к переводам модуля. По умолчанию "main". 
	 * @param string|null $language язык перевода
	 * @return string|callable
	 */
	public function t($message=null, $params=[], $category=null, $path='main', $language=null)
	{
	    if($category === null) {
    	    $called=get_called_class();
    	    $category=Y::param($called::i()->getBaseAlias() . ".translate.{$path}", "{$called}.{$path}");
	    }
	    
	    $hash=$category . '@' . $path;
	    if(!isset(static::$t[$hash])) {
    	    static::$t[$hash]=Y::ct($category);
	    }
	    
	    if($message === null) {
	        return static::$t[$hash];
	    }
	    
	    return call_user_func(static::$t[$hash], $message, $params, $language);
	}

	/**
	 * (non-PHPdoc)
	 * @see \CModule::configure()
	 */
	public function configure($config)
	{
		$configFile=$this->getBasePath() . Y::DS . 'config' . Y::DS . 'main.php';
		$config=\CMap::mergeArray($config, Y::includeFile($configFile, []));
		
		foreach(A::get($config, 'aliases', []) as $alias=>$path) {
			\Yii::setPathOfAlias($alias, $path);
		}
			
		parent::configure($config);
				
		// подключение модулей
		$this->setModules($this->modules);
		
		// инициализация модулей
		foreach($this->getModules() as $id=>$config) {
			if(A::get($config, 'autoload') === true) {
				$module=$this->getModule($id);
			}
		}
	}
	
	/**
	 * Получить алиас пути до текущего модуля
	 * @return string алиас
	 */
	public function getBaseAlias()
	{ 
		return $this->baseAlias;
	}
	
	/**
	 * Получить алиас пути до миграций текущего модуля
	 * @return string алиас
	 */
	public function getMigrationAlias()
	{ 
		$parent=$this->migrationAlias;
	}

	/**
	 * Получить URL к опубликованным ресурсам модуля.
	 * @return string
	 */
	public function getAssetsBaseUrl()
	{
		return $this->assetsBaseUrl;
	}
	
	/**
	 * Получить путь к опубликованным ресурсам модуля.
	 * @return string
	 */
	public function getAssetsBasePath()
	{
		return $this->assetsBasePath;
	}
	
	/**
	 * Установить путь к публикуемым ресурсам модуля.
	 * @param string $path|TRUE путь к публикуемым ресурсам.
	 * Если передано строгое TRUE, путь будет сгенерирован.
	 * Если папка с путем не найдена, будет установлено значение NULL.
	 */
	public function setAssetsBasePath($path)
	{
		if($path === true) {
			$this->assetsBasePath=$this->getBasePath() . Y::DS . 'assets';
		}
		else {
			$this->assetsBasePath=$path;
		}
		
		if(!is_dir($this->assetsBasePath)) $this->assetsBasePath=null;
	}
	
	/**
	 * (non-PHPdoc)
	 * @see \CModule::preinit()
	 */
	protected function preinit()
	{
		// установка алиаса модуля
		$parent=$this->getParentModule();
		$this->baseAlias=($parent instanceof WebModule) ? $parent->getBaseAlias() : 'application';
		$this->baseAlias.='.modules.' . $this->getName();
		
		// \Yii::setPathOfAlias($this->baseAlias, $this->getBasePath());
		
		$this->setAssetsBasePath(true);		
		
		// выполнение миграции
		if(!$this->migrationAlias)
			$this->migrationAlias=$this->baseAlias . '.migrations';
		
		// @todo add cache
		if(HDb::getCountMigrations($this->migrationAlias)) {
			HDb::migrate($this->migrationAlias);
		}

		// инициализация статического класса
		static::$instance=$this;
		
		return parent::preinit();
	}

	/**
     * Опубликовать основные ресурсы модуля
	 * @return string|FALSE В случае успеха, возвращает 
     * an absolute URL to the published asset, иначе FALSE.
	 */
	public function publishAssets()
	{
	    $this->assetsBaseUrl=Y::publish($this->getAssetsBasePath());
	    
	    return $this->assetsBaseUrl;
	}
	
	/**
	 * Опубликовать javascript файл.  
	 * @param string|array $file имя файла или массив имен файлов.
	 * @return string|FALSE В случае успеха, возвращает 
	 * an absolute URL to the published asset, иначе FALSE.
	 */
	public function publishJs($file='scripts.js')
	{
		if($path=$this->getAssetsBasePath()) {
			return Y::publish(['path'=>$path, 'js'=>$file]);
		}
		
		return false;
	}
	
	/**
	 * Опубликовать LESS файл.  
	 * @param string|array $file имя файла или массив имен файлов.
	 * @return string|FALSE В случае успеха, возвращает 
	 * an absolute URL to the published asset, иначе FALSE.
	 */
	public function publishLess($file='styles.less')
	{
		if($path=$this->getAssetsBasePath()) {
			return Y::publish(['path'=>$path, 'less'=>$file]);
		}
		
		return false;
	}
	
	/**
	 * Опубликовать CSS файл.  
	 * @param string|array $file имя файла или массив имен файлов.
	 * @return string|FALSE В случае успеха, возвращает 
	 * an absolute URL to the published asset, иначе FALSE.
	 */
	public function publishCss($file='styles.css')
	{
		if($path=$this->getAssetsBasePath()) {
			return Y::publish(['path'=>$path, 'css'=>$file]);
		}
		
		return false;
	}
} 
