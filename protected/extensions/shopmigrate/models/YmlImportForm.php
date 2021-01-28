<?php
/**
 * Модель формы миграции каталога
 */
namespace ext\shopmigrate\models;

use common\components\helpers\HYii as Y;
use common\components\helpers\HArray as A;
use common\components\helpers\HRequest as R;
use common\components\helpers\HFile;
use common\components\helpers\HDb;

class YmlImportForm extends \common\components\base\FormModel
{
	/**
	 * @var string имя файла.
	 */
	public $filename;
	
	/**
	 * @var string имя локального файла.
	 */
	public $local_filename=false;
	
	/**
	 * @var string коэффициент изменения цены. 
	 */
	public $price_coefficient=1;
	
	/**
	 * @var boolean перезаписать каталог товарами из выгрузки.
	 */
	public $replace=false;
	
	/**
	 * @var boolean ошибки при импорте
	 */
	public $importErrors=false;
	
	/**
	 * @var boolean используется для новой версии CMS >= 2.5.4
	 */
	public $isNewVersion=true;
	
	/**
	 * @var array конфигурация
	 * "tmpdir" string путь к временной директории. По умолчанию "webroot.uploads.export".
	 */
	private $_config = [
		'tmpdir'=>'webroot.uploads.import'
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
		$maxSize=max([
			HFile::getSizeBytes(ini_get('post_max_size')),
			HFile::getSizeBytes(ini_get('upload_max_filesize'))
		]);
		
		return $this->getRules([
			['filename', 'file', 
				'types'=>'xml,zip', 
				'allowEmpty'=>true,
				'maxSize'=>$maxSize, 
				'tooLarge'=>'Размер файла слишком большой. Проверьте значения переменных PHP-конфигурации "post_max_size" и "upload_max_filesize"'
			],
			['replace', 'boolean'],
			['price_coefficient', 'numerical'],
			['importErrors, local_filename', 'safe']
		]);
	}
	
	/**
	 * {@inheritDoc}
	 * @see CModel::attributeLabels()
	 */
	public function attributeLabels()
	{
		return $this->getAttributeLabels([
			'price_coefficient'=>'Коэффициент изменения цены',
			'filename'=>'Имя файла (*.xml, *.zip)',
			'replace'=>'Перезаписать каталог',
			'importErrors'=>'Ошибки'
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
	 * Полная очистка каталога от категорий и товаров.
	 * Дополнительно удаляются все файлы и изображения.
	 * @return boolean
	 */
	public static function clearCatalog()
	{
		$query='DELETE FROM `category`;'
			. 'DELETE FROM `product`;'
			. 'DELETE FROM `related_category`;'
			. 'DELETE FROM `image` WHERE `model`=\'category\' OR `model`=\'product\';'
			. 'DELETE FROM `file` WHERE `model`=\'category\' OR `model`=\'product\';'
			. 'DELETE FROM `metadata` WHERE `owner_name`=\'category\' OR `owner_name`=\'product\';'
			. 'ALTER TABLE `category` AUTO_INCREMENT = 1;'
			. 'ALTER TABLE `product` AUTO_INCREMENT = 1;';
		HDb::execute($query);
			
		// нормализация AUTO_INCREMENT
		// 		$query='SET @maxId=(SELECT MAX(`id`) FROM `image`) + 1;'
		// 			. 'SET @qAI=CONCAT("ALTER TABLE `image` AUTO_INCREMENT = ", @maxId);'
		// 			. 'PREPARE stmt FROM @qAI;EXECUTE stmt;DEALLOCATE PREPARE stmt;'
		// 			. 'SET @maxId=(SELECT MAX(`id`) FROM `file`) + 1;'
		// 			. 'SET @qAI=CONCAT("ALTER TABLE `file` AUTO_INCREMENT = ", @maxId);'
		// 			. 'PREPARE stmt FROM @qAI;EXECUTE stmt;DEALLOCATE PREPARE stmt;'
		// 			. 'SET @maxId=(SELECT MAX(`id`) FROM `metadata`) + 1;'
		// 			. 'SET @qAI=CONCAT("ALTER TABLE `metadata` AUTO_INCREMENT = ", @maxId);'
		// 			. 'PREPARE stmt FROM @qAI;EXECUTE stmt;DEALLOCATE PREPARE stmt;';
		foreach(['image', 'file', 'metadata'] as $tableName) {
			$maxId=(int)HDb::queryScalar('SELECT MAX(`id`) FROM `image`') + 1;
			HDb::execute("ALTER TABLE `{$tableName}` AUTO_INCREMENT = {$maxId};");
		}
		
		// удаление картинок и файлов
		foreach(['images', 'files'] as $alias) {
			foreach(['category', 'product'] as $subalias) {
				$path=\Yii::getPathOfAlias("webroot.{$alias}.{$subalias}");
				if(is_dir($path)) {
					if($files=HFile::getFiles($path, true)) {
						array_map('unlink', $files);
					}
				}
			}
		}
		
		return true;
	}	
	
	/**
	 * Экспорт каталога
	 * @param array конфигурация. Подробнее YmlExportForm::$_config.
	 */
	public function process($config=[])
	{
		if($this->local_filename) {
			$filename=HFile::path([\Yii::getPathOfAlias('webroot.uploads.import'), $this->local_filename]);
			if(!is_file($filename)) {
				return false;
			}
			$uploadedFilename=$filename;
			$uploadedFileExt=pathinfo($filename, PATHINFO_EXTENSION);
		}
		elseif($file=\CUploadedFile::getInstance($this, 'filename')) {
			$uploadedFilename=$file->getTempName();
			$uploadedFileExt=$file->extensionName;
		}
		else {
			$this->addError('filename', 'Необходимо выбрать файл импорта');
			return false;
		}
		
		if(is_file($uploadedFilename)) {
			$this->_config=A::m($this->_config, $config);
			
			$this->_vars['tmpdir']=HFile::path([
				\Yii::getPathOfAlias(A::get($this->_config, 'tmpdir')),
				date('Y_m_d_H_i_s')
			], true, 0755);
			
			$importXmlFilename=false;
			if($uploadedFileExt== 'zip') {
				$zip=new \ZipArchive();
				if($zip->open($uploadedFilename) === true) {
					$zip->extractTo($this->_vars['tmpdir']);
					$zip->close();
					if($files=HFile::getFiles($this->_vars['tmpdir'])) {
						foreach($files as $file) {
							$fileExt=pathinfo($file, PATHINFO_EXTENSION);
							if($fileExt == 'zip') {
								$zip=new \ZipArchive();
								if($zip->open(HFile::path([$this->_vars['tmpdir'], $file])) === true) {
									$zipExtractPath=HFile::path([$this->_vars['tmpdir'], pathinfo($file, PATHINFO_FILENAME)], true, 0755);
									$zip->extractTo($zipExtractPath);
									$zip->close();
								}
							}
							elseif($fileExt == 'xml') {
								$importXmlFilename=HFile::path([$this->_vars['tmpdir'], $file]);
							}
						}
					}
				}
			}
			elseif($uploadedFileExt == 'xml') {
				$importXmlFilename=$uploadedFilename;
			}
			
			$imported=$this->import($importXmlFilename);
			
			HFile::rm($this->_vars['tmpdir'], true);
			
			Y::cache()->flush();
			
			return $imported;
		}
		
		return false;
	}
	
	/**
	 * Ипортирование каталога
	 * @param string $filename полный путь к XML файлу выгрузки.
	 * @param boolean|null $isYML файл является YML форматом. 
	 * По умолчанию (NULL) будет определен автоматически. 
	 * @return boolean
	 */
	public function import($filename, $isYML=null)
	{
		$doc=new \DOMDocument();
		if(!$filename || !$doc->load($filename)) {
			return false;
		}
		
		$xml=simplexml_load_file($filename);
		
		if(!$xml->xpath('/yml_catalog/shop')) {
			return false;
		}
		
		if($this->replace) {
			static::clearCatalog();
		}
		
		$shop=$xml->shop;
		
		// загрузка категорий
		// @var array $_categories array(id=>title)
		$_categories=[];
		// @var array $_parents array(id=>parentId)
		$_parents=[];
		if($shop->xpath('categories/category')) {
			foreach($shop->categories[0]->category as $category) {
				$_categories[(int)$category['id']]=(string)$category[0];
				$_parents[(int)$category['id']]=(int)$category['parentId'];
			}
		}
		asort($_parents);
		
		if($_categories) {
			$ymlMode=true;
			$categoryModels=[];
			foreach($_categories as $id=>$title) {
				$model=new \Category();
				$model->title=$title;
				
				if($data=$shop->xpath('categories-data/category[@id='.$id.']')) {
					$ymlMode=false;
					$model->alias=(string)$data[0]->sef;
					foreach(['meta_title', 'meta_key', 'meta_desc', 'meta_h1', 'a_title', 'priority', 'lastmod', 'changefreq'] as $metaAttribute) {
						$model->$metaAttribute=(string)$data[0]->meta->$metaAttribute;
					}
					$model->description=(string)$data[0]->description;
					if($data[0]->dbschema->columns) {
						foreach($data[0]->dbschema->columns[0] as $column) {
							$name=(string)$column['name'];
							if(!in_array($name, ['id', 'title', 'alias', 'description','root','lft','rgt','level'])) {
								try {
									$model->$name=(string)$column;
								}
								catch(\Exception $e) {
								}
							}
						}
					}
				}
				
				$categoryModels[$id]=$model;
			}
			
			// сохранение
			if($categoryModels) {
				$saved=[];
				
				// @var callable $fAddCategory добавление категории.
				$fAddCategory=function($parentId, $id) use (&$fAddCategory, &$saved, &$categoryModels, $_parents) {
					if(isset($saved[$id])) {
						return true;
					}
					$saved[$id]=$id;
					
					try {
						if(!$parentId) {
							if(!$categoryModels[$id]->saveNode()) {
								$message='Не добавлена категория '.$categoryModels[$id]->title.' (#'.$categoryModels[$id]->id.')';
								$this->addImportError($categoryModels[$id], $message);
								return false;
							}
						}
						else {
							if(!isset($saved[$parentId])) {
								if(!$fAddCategory($_parents[$parentId], $parentId)) {
									return false;
								}
							}
							if(!$categoryModels[$id]->appendTo($categoryModels[$parentId])) {
								$message='Не добавлена категория '.$categoryModels[$id]->title.' (#'.$categoryModels[$id]->id.')';
								$this->addImportError($categoryModels[$id], $message);
								return false;
							}
						}
					}
					catch(\Exception $e) {
						$message='Не добавлена категория '.$categoryModels[$id]->title.' (#'.$categoryModels[$id]->id.')';
						$this->addImportError($categoryModels[$id], $message.'. Exception:'.$e->getMessage());
						return false;
					}
					
				};
				
				foreach($_parents as $id=>$parentId) {
					$fAddCategory($parentId, $id);
				}
				
				if(!$ymlMode) {
					// выгрузка картинок и файлов
					$categoryImagesSourcePath=HFile::path([$this->_vars['tmpdir'], 'categories-images']);
					$categoryImagesDestPath=\Yii::getPathOfAlias('webroot.images.category');
					$categoryFilesSourcePath=HFile::path([$this->_vars['tmpdir'], 'categories-files']);
					$categoryFilesDestPath=\Yii::getPathOfAlias('webroot.files.category');
					
					foreach($categoryModels as $id=>$categoryModel) {
						// выгрузка основной картинки
						if($this->isNewVersion) {
							$sourcePath=HFile::path([$categoryImagesSourcePath, $categoryModel->main_image]);
							if(is_file($sourcePath)) {
								copy($sourcePath, HFile::path([$categoryImagesDestPath, $categoryModel->main_image]));
							}
						}
						
						$this->importFiles(
							$categoryModel,
							$shop->xpath('categories-data/category[@id='.$id.']/images//image'),
							$categoryImagesSourcePath,
							$categoryImagesDestPath,
							'\CImage',
							['ordering', 'description']
						);
						
						$this->importFiles(
							$categoryModel,
							$shop->xpath('categories-data/category[@id='.$id.']/files//file'),
							$categoryFilesSourcePath,
							$categoryFilesDestPath,
							'\File',
							['description']
						);
					}
				}
			}
		}
		
		// загрузка товаров
		if($shop->xpath('offers/offer')) {
			$ymlMode=true;
			$productModels=[];
			foreach($shop->offers[0]->offer as $offer) {
				$id=(int)$offer['id'];
				
				$model=new \Product();
				$model->title=(string)$offer->name;
				$model->description=(string)$offer->description;
				if(isset($categoryModels[(string)$offer->categoryId])) {
					$model->category_id=$categoryModels[(string)$offer->categoryId]->id;
				}
				else {
					$model->category_id=null;
				}
				
				if(is_numeric($this->price_coefficient)) {
					$priceCoefficient=(float)$this->price_coefficient;
				}
				else {
					$priceCoefficient=1;
				}
				$model->price=(float)((string)$offer->price) * $priceCoefficient;
				
				if($this->isNewVersion) {
					$model->old_price=(float)((string)$offer->oldprice) * $priceCoefficient;
				}
				
				if($data=$shop->xpath('offers-data/offer[@id='.$id.']')) {
					$ymlMode=false;
					$model->alias=(string)$data[0]->sef;
					foreach(['meta_title', 'meta_key', 'meta_desc', 'meta_h1', 'a_title', 'priority', 'lastmod', 'changefreq'] as $metaAttribute) {
						$model->$metaAttribute=(string)$data[0]->meta->$metaAttribute;
					}
					if($data[0]->dbschema[0]->columns) {
						foreach($data[0]->dbschema[0]->columns[0]->column as $column) {
							$name=(string)$column['name'];
							if(!in_array($name, ['id', 'category_id', 'title', 'alias', 'description', 'price'])) {
								try {
									if(!($model->old_price && ($name=='old_price'))) {
										$model->$name=(string)$column;
									}
								}
								catch(\Exception $e) {
								}
							}
						}
					}
				}
				
				$productModels[$id]=$model;
			}
			
			// сохранение
			if($productModels) {
				$offersImagesSourcePath=HFile::path([$this->_vars['tmpdir'], 'offers-images']);
				$offersImagesDestPath=\Yii::getPathOfAlias('webroot.images.product');
				$offersFilesSourcePath=HFile::path([$this->_vars['tmpdir'], 'offers-files']);
				$offersFilesDestPath=\Yii::getPathOfAlias('webroot.files.product');
				foreach($productModels as $id=>$productModel) {
					if($productModel->save()) {
						if(!$ymlMode) {
							// выгрузка основной картинки
							if($this->isNewVersion) {
								$sourcePath=HFile::path([$offersImagesSourcePath, $productModel->main_image]);
								if(is_file($sourcePath)) {
									copy($sourcePath, HFile::path([$offersImagesDestPath, $productModel->main_image]));
								}
							}
							else {
								foreach(['jpg', 'jpeg', 'png', 'gif'] as $mainImageExt) {
									$filename=$id . '.' .$mainImageExt;
									$sourcePath=HFile::path([$offersImagesSourcePath, $id.'.'.$mainImageExt]);
									if(is_file($sourcePath)) {
										copy($sourcePath, HFile::path([$offersImagesDestPath, $productModel->id.'.'.$mainImageExt]));
										break;
									}
								}
							}
							
							// выгрузка дополнительных картинок и файлов
							$this->importFiles(
								$productModel,
								$shop->xpath('offers-data/offer[@id='.$id.']/images//image'),
								$offersImagesSourcePath,
								$offersImagesDestPath,
								'\CImage',
								['ordering', 'description']
							);
							
							$this->importFiles(
								$productModel,
								$shop->xpath('offers-data/offer[@id='.$id.']/files//file'),
								$offersFilesSourcePath,
								$offersFilesDestPath,
								'\File',
								['description']
							);
						}
					}
					else {
						$this->addImportError($productModel, 'Не добавлен товар '.$productModel->title.' (#'.$productModel->id.')');
					}
				}
			}
		}
		
		// загрузка связей доп.категорий
		if($data=$shop->xpath('category-related/relation')) {
			foreach($data as $relation) {
				$categoryId=(string)$relation['categoryId'];
				$offerId=(string)$relation['offerId'];
				if(isset($categoryModels[$categoryId]) && isset($productModels[$offerId])) {
					$model=new \RelatedCategory;
					$model->category_id=$categoryModels[$categoryId]->id;
					$model->product_id=$productModels[$offerId]->id;
					try {
						$model->save();
					}
					catch(\Exception $e) {
						$message='Связь товара ' . $productModels[$offerId]->title . ' (#'.$productModels[$offerId]->id.')'
							. ' с категорей ' . $categoryModels[$categoryId]->title . ' (#'.$categoryModels[$categoryId]->id.') не добавлена';
						$this->addError('importErrors', $message);
					}
				}
			}
		}
		
		return true;
	}
	
	/**
	 * Ипортирование файлов
	 * @param \CActiveRecord $owner модель для которой будут привязаны файлы
	 * @param string $xpath XPATH выражение выборки файлов
	 * @param string $sourcePath полный путь к директории из которой будут скопированы файлы
	 * @param string $destPath полный путь к директории в которую будут скопированы файлы
	 * @param string $fileModelClass имя класса модели файла
	 * @param array $attributes список атрибутов, которые будут установлены в модель файла
	 */
	protected function importFiles($owner, $fileNodes, $sourcePath, $destPath, $fileModelClass, $attributes=[])
	{
		if(is_array($fileNodes) && !empty($fileNodes)) {
			if(!is_dir($destPath)) {
				HFile::mkDir($destPath, 0755, true);
			}
			
			foreach($fileNodes as $file) {
				$fileModel=new $fileModelClass();
				$fileModel->model=strtolower($owner->tableName());
				$fileModel->item_id=$owner->id;
				$fileModel->filename=(string)$file->filename;
				
				foreach($attributes as $attribute) {
					$fileModel->$attribute=(string)$file->$attribute;
				}
				
				if($fileModel->save() && $fileModel->filename) {
					$filename=iconv('utf-8', 'cp866', $fileModel->filename);
					$from=HFile::path([$sourcePath, $filename]);
					if(is_file($from)) {
						$toUTF8=HFile::path([$destPath, $fileModel->filename]);
						// устранение проблемы с файлами с русским названием.
						if(strncasecmp(PHP_OS, 'WIN', 3) === 0) {
							$toCP1251=HFile::path([$destPath, iconv('utf-8', 'cp1251', $fileModel->filename)]);
							copy($from, $toCP1251);
						}
						else {
							copy($from, $toUTF8);
						}
					}
					// генерация миниатюры
					if($fileModel instanceof \CImage) {
						$fileModel->getTmbUrl();
					}
				}
			}
		}
	}
	
	/**
	 * Добавление ошибки импорта
	 * @param \CModel $model модель, в которой возникла ошибка.
	 * @param string $message сообщение перед текстом ошибки
	 */
	protected function addImportError($model, $message)
	{
		$errors=$model->getErrors();
		$error=array_shift($errors);
		$this->addError('importErrors', $message.': '.$error[0]);
	}
}