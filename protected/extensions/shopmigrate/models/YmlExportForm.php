<?php
/**
 * Модель формы миграции каталога
 */
namespace ext\shopmigrate\models;

use common\components\helpers\HArray as A;
use common\components\helpers\HRequest as R;
use common\components\helpers\HFile;

class YmlExportForm extends \common\components\base\FormModel
{
	/**
	 * @var string имя файла.
	 */
	public $filename;
	
	/**
	 * @var string короткое название магазина, должно содержать не более 20 символов. 
	 */
	public $shop_name=false;
	
	/**
	 * @var string полное наименование компании, владеющей магазином.
	 */
	public $shop_company=false;
	
	/**
	 * @var string URL главной страницы магазина.
	 */
	public $shop_url=false;
	
	/**
	 * @var string выгружать строгов формате YML.
	 */
	public $as_yml=false;
	
	/**
	 * @var boolean используется для новой версии CMS >= 2.5.4
	 */
	public $isNewVersion=true;
	
	/**
	 * @var array конфигурация
	 * "tmpdir" string путь к временной директории. По умолчанию "webroot.uploads.export".
	 */
	private $_config = [
		'tmpdir'=>'webroot.uploads.export'
	];
	
	private $_vars = [
		'tmpdir'=>false,
		'filename'=>false,
		'zipCategoryImages'=>false,
		'zipCategoryFiles'=>false,
		'zipProductImages'=>false,
		'zipProductFiles'=>false,
	];
	
	/**
	 * {@inheritDoc}
	 * @see CModel::rules()
	 */
	public function rules()
	{
		return $this->getRules([
			['shop_name, shop_company', 'required'],
			['shop_name', 'length', 'max'=>20],
			['filename, as_yml, shop_url', 'safe'],
		]);
	}
	
	/**
	 * {@inheritDoc}
	 * @see CModel::attributeLabels()
	 */
	public function attributeLabels()
	{
		return $this->getAttributeLabels([
			'shop_name'=>'Короткое название магазина',
			'shop_company'=>'Полное наименование компании, владеющей магазином',
			'shop_url'=>'URL главной страницы магазина',
			'filename'=>'Имя файла',
			'as_yml'=>'YML формат (сокращенный формат)',
		]);
	}
	
	/**
	 * Получить сгенерированное имя файла
	 * @return string
	 */
	public function generateFilename()
	{
		return date('Y_m_d_') . R::r()->serverName . '.xml';
	}
	
	/**
	 * Экспорт каталога
	 * @param array конфигурация. Подробнее YmlExportForm::$_config.
	 */
	public function process($config=[])
	{
		$this->_config=A::m($this->_config, $config);
		
		if(!$this->filename) {
			$this->filename=$this->generateFilename();
		}
		
		$this->_vars['tmpdir']=HFile::path([
			\Yii::getPathOfAlias(A::get($this->_config, 'tmpdir')),
			'xml'.date('_Y_m_d_H_i_s')
		], true, 0755);
		$this->_vars['filename']=$this->filename;
		
		$doc=new \DOMDocument('1.0', 'UTF-8');
		$rootElement=$doc->createElement('yml_catalog');
		$rootElement=$doc->appendChild($rootElement);
		$rootElement->setAttribute('date', date('Y-m-d H:i'));
		$xml=simplexml_import_dom($rootElement);
		$shop=$xml->addChild('shop');
		
		// описание магазина
		$shop->addChild('name', $this->shop_name ?: \D::cms('sitename'));
		$shop->addChild('company', $this->shop_company ?: \D::cms('firm_name'));
		$shop->addChild('url', $this->shop_url ?: \Yii::app()->createAbsoluteUrl('/'));
		
		// выгрузка валют
		$currencies=$shop->addChild('currencies');
		// @todo на данный момент только RUR
		$currency=$currencies->addChild('currency');
		$currency->addAttribute('id', 'RUR');
		$currency->addAttribute('rate', 1);
		
		// выгрузка категорий
		$this->ymlAddCategoriesNode($shop);
		
		if(!$this->as_yml) {
			// выгрузка дополнительных данных категорий
			$this->xmlAddCategoriesDataNode($doc, $shop);
		}
		
		// выгрузка товаров
		$this->ymlAddOffersNode($doc, $shop);
		
		if(!$this->as_yml) {
			// выгрузка дополнительных данных товаров
			$this->xmlAddOffersDataNode($doc, $shop);
		}
		
		if(!$this->as_yml) {
			// связи товаров с доп.категориями
			$this->xmlCategoryRelatedNodes($shop);
		}
		
		// сохранение xml файла выгрузки
		$xmlFilename=HFile::path([$this->_vars['tmpdir'], $this->_vars['filename']], true, 0755);
		
		$doc->preserveWhiteSpace = false;
		$doc->formatOutput = true;		
		$doc->save($xmlFilename);		
		
		// финальный архив
		$finalZipFilename=HFile::path([
			\Yii::getPathOfAlias(A::get($this->_config, 'tmpdir')), 
			pathinfo($this->_vars['filename'], PATHINFO_FILENAME).'.zip'
		]);
		
		if(is_file($xmlFilename)) {
			if(is_file($finalZipFilename)) {
				unlink($finalZipFilename);
			}
			
			$zip=HFile::zipOpen($finalZipFilename, \ZipArchive::CREATE);
			
			$zip->addFile($xmlFilename, basename($xmlFilename));
			$hZip=function($dirname, $entry, $params) use (&$hZip) {
				$filename=HFile::path([$dirname, $entry]);
				if(is_dir($filename)) {
					HFile::readDir($filename, false, $hZip, [
						'zip'=>$params['zip'], 
						'path'=>A::m(A::get($params, 'path', []), [basename($filename)])
					]);
				}
				elseif(is_file($filename)) {
					$localname=HFile::path(A::m(A::get($params, 'path', []), [basename($filename)]));
					$params['zip']->addFile($filename, $localname);
				}
			};
			HFile::readDir($this->_vars['tmpdir'], false, $hZip, ['zip'=>&$zip]);
			
			$zip->close();
		}
		
		// удаление файлов
		HFile::rm($this->_vars['tmpdir'], true);
		
		if(is_file($finalZipFilename)) {
			// отправка файла
			// HFile::download($finalZipFilename);
		}
		
		return true;
	}
	
	/**
	 * Добавить категории в формате .
	 * @param &\SimpleXMLElement $shop корневой XML-элемент каталога. 
	 */
	protected function ymlAddCategoriesNode(&$shop)
	{	
		$categories=$shop->addChild('categories');
		
		$this->ymlAddCategoryNode($categories);
	}
	
	/**
	 * 
	 * @param unknown $categories
	 * @param string $models
	 * @param string $parent
	 */
	protected function ymlAddCategoryNode(&$categories, $models=false, $parent=false)
	{
		$criteria=['select'=>'`id`,`title`,`level`,`root`,`lft`,`rgt`', 'order'=>'`root`,`lft`'];
		
		if(!$models) {
			$models=\Category::model()->roots()->findAll($criteria);
		}
		
		if($models) {
			foreach($models as $model) {
				$category=$categories->addChild('category', trim($model->title));
				$category->addAttribute('id', $model->id);
				if($parent) {
					$category->addAttribute('parentId', $parent->id);
				}
				
				if($childrens=$model->children()->findAll($criteria)) {
					$this->ymlAddCategoryNode($categories, $childrens, $model);
				}
			}		
		}
	}
	
	/**
	 * Добавить категории.
	 * @param \DOMDocument &$doc объект XML документа.
	 * @param \SimpleXMLElement &$shop корневой XML-элемент каталога. 
	 */
	protected function xmlAddCategoriesDataNode(&$doc, &$shop)
	{
		$categoriesData=$shop->addChild('categories-data');
		if($models=\Category::model()->findAll(['order'=>'`root`,`lft`'])) {
			foreach($models as $model) {
				$category=$categoriesData->addChild('category');
				$category->addAttribute('id', $model->id);
				// ЧПУ
				if($model->alias) {
					$category->addChild('sef', $model->alias);
				}
				// сео
				if($meta=$model->getRelated('meta')) {
					$nodeMeta=$category->addChild('meta');
					$this->xmlAddNodes(
						$doc,
						$nodeMeta, 
						$meta, 
						[['meta_title'], ['meta_key'], ['meta_desc'], ['meta_h1'], ['a_title'], 'priority', 'lastmod', 'changefreq']
					);
				}
				
				if($this->isNewVersion) {
					// основное изображение
					if($categoryPictureFilename=$model->mainImageBehavior->getSrc()) {
						$pictureFullFilename=HFile::path([\Yii::getPathOfAlias('webroot'), $categoryPictureFilename]);
						$pictureFilename=basename($pictureFullFilename);
						$pictureNode=$category->addChild('picture', $pictureFilename);
						$this->compress([$this->_vars['tmpdir'], 'categories-images'], [[$pictureFullFilename, $pictureFilename]]);
					}
				}
				
				// изображения
				$this->addFilesNode(
					$doc, 
					$category, 
					'images', 
					'image', 
					$model->getRelated('images'), 
					['filename', ['description'], 'ordering'], 
					'categories-images', 
					function($model) {
						return [HFile::path([$model->getPath(), $model->filename]), $model->filename];
					}
				);
				
				// файлы
				$files=\File::model()->findAll([
					'condition'=>'`item_id`=:id AND `model`=:model',
					'params'=>[':id'=>$model->id, ':model' => strtolower(get_class($model))],
				]);
				$this->addFilesNode(
					$doc,
					$category,
					'files',
					'file',
					$files,
					['filename', ['description']],
					'categories-files',
					function($model) {
						return [
							HFile::path([\Yii::getPathOfAlias('webroot'), 'files', $model->model, $model->filename]),
							$model->filename
						];
					}
				);
				
				// описание
				if($model->description) {
					$this->xmlAddCData($doc, $category, 'description', $model->description);
				}
				
				// схема базы данных
				$dbSchemaNode=$category->addChild('dbschema');
				$columnsNode=$dbSchemaNode->addChild('columns');
				
				$columnNames=\Category::model()->getTableSchema()->getColumnNames();
				foreach($columnNames as $name) {
					$columnNode=$this->xmlAddCData($doc, $columnsNode, 'column', $model->$name);
					$columnNode->addAttribute('name', $name);
				}
			}
		}
	}
	
	/**
	 * Добавление узла offers
	 * @param \DOMDocument &$doc объект XML документа.
	 * @param \SimpleXMLElement &$shop
	 */
	protected function ymlAddOffersNode(&$doc, &$shop)
	{
		$offers=$shop->addChild('offers');
		
		$this->ymlAddOfferNode($doc, $offers);
	}
	
	/**
	 * @todo на данный момент Упрощенный тип выгрузки товара.
	 * @param \DOMDocument &$doc объект XML документа.
	 * @param \SimpleXMLElement $offersNode
	 * @param string $models
	 * @param string $parent
	 */
	protected function ymlAddOfferNode(&$doc, &$offersNode, $models=false)
	{
		if($this->isNewVersion) {
			$criteria=['select'=>'`id`,`title`,`price`,`old_price`,`description`,`category_id`, `main_image`', 'order'=>'`id`'];
		}
		else {
			$criteria=['select'=>'`id`,`title`,`price`,`description`,`category_id`', 'order'=>'`id`'];
		}
		
		if(!$models) {
			$models=\Product::model()->findAll($criteria);
		}
		
		if($models) {
			foreach($models as $model) {
				$offerNode=$offersNode->addChild('offer');
				$offerNode->addAttribute('id', $model->id);
				$offerNode->addAttribute('available', $model->notexist?'false':'true');
				
				$offerNode->addChild('name', trim($model->title));
				$offerNode->addChild('url', \Yii::app()->createAbsoluteUrl('/shop/product', ['id'=>$model->id]));
				$offerNode->addChild('price', $model->price);
				if($this->isNewVersion) {
					$offerNode->addChild('oldprice', $model->old_price);
				}
				$offerNode->addChild('currencyId', 'RUR');
				$offerNode->addChild('categoryId', $model->category_id);
				
				if($this->isNewVersion) {
					$offerPicture=$model->mainImageBehavior->getSrc();
				}
				else {
					$offerPicture=$model->getFullImg(false, false);
				}
				if($offerPicture) {
					$offerPicture=\Yii::app()->createAbsoluteUrl('/images/product/'.$offerPicture);
				}
				$offerNode->addChild('picture', $offerPicture);
				$this->xmlAddCData($doc, $offerNode, 'description', $model->description);
			}
		}
	}
	
	/**
	 * Добавить узел дополнительных данных товара.
	 * @param \DOMDocument &$doc объект XML документа.
	 * @param \SimpleXMLElement &$shopNode корневой XML-элемент каталога.
	 */
	protected function xmlAddOffersDataNode(&$doc, &$shopNode)
	{
		$offersDataNode=$shopNode->addChild('offers-data');
		if($models=\Product::model()->findAll()) {
			foreach($models as $model) {
				$offerNode=$offersDataNode->addChild('offer');
				$offerNode->addAttribute('id', $model->id);
				
				// ЧПУ
				if($model->alias) {
					$offerNode->addChild('sef', $model->alias);
				}
				
				// сео
				if($meta=$model->getRelated('meta')) {
					$metaNode=$offerNode->addChild('meta');
					$this->xmlAddNodes(
						$doc,
						$metaNode,
						$meta,
						[['meta_title'], ['meta_key'], ['meta_desc'], ['meta_h1'], ['a_title'], 'priority', 'lastmod', 'changefreq']
					);
				}
				
				// основное изображение
				if($this->isNewVersion) {
					if($offerPictureFilename=$model->mainImageBehavior->getSrc()) {
						$pictureFullFilename=HFile::path([\Yii::getPathOfAlias('webroot'), $offerPictureFilename]);
					}
				}
				else {
					if($offerPictureFilename=$model->getFullImg(false, false)) {
						$pictureFullFilename=HFile::path([\Yii::getPathOfAlias('webroot.images.product'), $offerPictureFilename]);
					}
				}
				if($offerPictureFilename) {
					$pictureFilename=basename($pictureFullFilename);
					$pictureNode=$offerNode->addChild('picture', $pictureFilename);
					$this->compress([$this->_vars['tmpdir'], 'offers-images'], [[$pictureFullFilename, $pictureFilename]]);
				}
				
				// изображения
				$this->addFilesNode(
					$doc,
					$offerNode,
					'images',
					'image',
					$model->getMoreImages(),
					['filename', ['description'], 'ordering'],
					'offers-images',
					function($model) {
						return [HFile::path([$model->getPath(), $model->filename]), $model->filename];
					}
				);
				
				// файлы
				$files=\File::model()->findAll([
					'condition'=>'`item_id`=:id AND `model`=:model',
					'params'=>[':id'=>$model->id, ':model' => strtolower(get_class($model))],
				]);
				$this->addFilesNode(
					$doc,
					$offerNode,
					'files',
					'file',
					$files,
					['filename', ['description']],
					'offers-files',
					function($model) {
						return [
							HFile::path([\Yii::getPathOfAlias('webroot'), 'files', $model->model, $model->filename]),
							$model->filename
						];
					}
				);
				
				// описание
				if($model->description) {
					$this->xmlAddCData($doc, $offerNode, 'description', $model->description);
				}
				
				// схема базы данных
				$dbSchemaNode=$offerNode->addChild('dbschema');
				$columnsNode=$dbSchemaNode->addChild('columns');
				
				$columnNames=\Product::model()->getTableSchema()->getColumnNames();
				foreach($columnNames as $name) {
					$columnNode=$this->xmlAddCData($doc, $columnsNode, 'column', $model->$name);
					$columnNode->addAttribute('name', $name);
				}
			}
		}
	}
	
	/**
	 * Добавление узла файлов.
	 * @param \DOMDocument &$doc
	 * @param \SimpleXMLElement $parentNode
	 * @param string $filesNodeName
	 * @param string $fileNodeName
	 * @param array[\CModel] $models
	 * @param array $attributes
	 * @param string $archiveFilename
	 * @param callable $hGetArchiveFilename
	 */
	protected function addFilesNode(&$doc, $parentNode, $filesNodeName, $fileNodeName, $models, $attributes, $archiveFilename, $hGetArchiveFilename)
	{
		if($models) {
			$nodeFiles=$parentNode->addChild($filesNodeName);
			$files=[];
			foreach($models as $model) {
				$nodeFile=$nodeFiles->addChild($fileNodeName);
				$this->xmlAddNodes($doc, $nodeFile, $model, $attributes);
				$files[]=call_user_func_array($hGetArchiveFilename, [$model]);
			}
			// архивирование файлов
			$this->compress([$this->_vars['tmpdir'], $archiveFilename], $files);
		}
		
	}
	
	/**
	 * Создание архива файлов.
	 * @param string $filename имя файла архива.
	 * @param array $files массив файлов.
	 * - элемент может быть передан строкой (полный путь до архивируемого файла)
	 * - элемент может быть передан как массив array(полный путь до архивируемого файла, локальное имя файла)
	 * @param string $method метод сжатия. Поддерживается "zip", "tar" и "copy". По умолчанию "copy".
	 * Простое копирование.
	 */
	protected function compress($filename, $files, $method='copy')
	{
		$archiver=$this->getArchiver($filename, $method);
				
		foreach($files as $params) {
			$localname=null;
			if(is_array($params)) {
				$filename=array_shift($params);
				if(!empty($params)) {
					$localname=array_shift($params);
				}
			}
			else {
				$filename=$params;
			}
			
			$this->addToArchive($archiver, $filename, $localname);
		}
		
		$this->closeArchiver($archiver);
	}
	
	/**
	 * Получения объекта-архиватора
	 * @param string $filename имя файла архива.
	 * @param string $method метод сжатия. Поддерживается "zip", "tar". По умолчанию "tar".
	 * @return mixed объект архиватора
	 */
	protected function getArchiver($filename, $method='copy')
	{
		$archiver=false;
		
		if(is_array($filename)) {
			$filename=HFile::path($filename, true, 0755);
		}
		
		if($method == 'zip') {
			$archiver=HFile::zipOpen($filename, \ZipArchive::CREATE);
		}
		elseif($method == 'tar') {
			$archiver=new \PharData($filename);
		}
		elseif($method == 'copy') {
			HFile::mkDir($filename, 0755, true);
			$archiver=$filename;
		}
		
		return $archiver;
	}
	
	/**
	 * Закрытие архива
	 * @param mixed &$archiver объект архиватора.
	 */
	protected function closeArchiver(&$archiver)
	{
		if($archiver instanceof \ZipArchive) {
			$archiver->close();
		}
	}
	
	/**
	 * Добавление файла в архив
	 * @param mixed &$archiver объект архиватора.
	 * @param string $filename имя файла архива.
	 * @param string $localname имя файла внутри ZIP-архива. Если указано, то переопределит $filename.
	 */
	protected function addToArchive(&$archiver, $filename, $localname)
	{
		if(!is_file($filename)) {
			return false;
		}
		
		if($archiver instanceof \ZipArchive) {
			return HFile::zipAddFile($archiver, $filename, $localname);
		}
		elseif($archiver instanceof \PharData) {
			if($localname) {
				$localname=iconv('utf-8', 'cp866', $localname);
			}
			return $archiver->addFile($filename, $localname);
		}
		elseif(is_string($archiver)) { // copy
			copy($filename, HFile::path([$archiver, $localname?:basename($filename)]));
		}
		
		return false;
	}
	
	/**
	 * Добавление узлов связи товаров с доп.категориями.
	 * @param \SimpleXMLElement &$shopNode
	 */
	protected function xmlCategoryRelatedNodes(&$shopNode)
	{
		if($models=\RelatedCategory::model()->findAll(['order'=>'category_id'])) {
			$categoryRelatedNode=$shopNode->addChild('category-related');
			foreach($models as $model) {
				$relationNode=$categoryRelatedNode->addChild('relation');
				$relationNode->addAttribute('categoryId', $model->category_id);
				$relationNode->addAttribute('offerId', $model->product_id);
			}
		}
	}
	
	/**
	 * Добавление узлов
	 * @param \DOMDocument &$doc
	 * @param \SimpleXMLElement &$node
	 * @param \CModel $model
	 * @param array $attributes array(name, [name])
	 * Если элемент передан в виде массива, то это означает, что значение атрибута 
	 * необходимо поместить в контейнер CDATA. 
	 * @param string $allowEmpty По умолчанию false.
	 */
	protected function xmlAddNodes(&$doc, &$node, $model, $attributes, $allowEmpty=false)
	{
		foreach($attributes as $attribute) {
			$cdata=false;
			if(is_array($attribute)) {
				$attribute=array_shift($attribute);
				$cdata=true;
			}
			if($model->$attribute) {
				if($cdata) {
					$this->xmlAddCData($doc, $node, $attribute, $model->$attribute);
				}
				else {
					$node->addChild($attribute, $model->$attribute);
				}
			}
		}
	}
	
	/**
	 * Добавление элемента CDATA
	 * @param unknown $doc
	 * @param unknown $node
	 * @param unknown $name
	 * @param unknown $value
	 */
	protected function xmlAddCData(&$doc, $node, $name, $value)
	{
		$child=$node->addChild($name);
		$dom=dom_import_simplexml($child);
		$dom->appendChild($doc->createCDATASection($value));
		
		return $child;
	}
}