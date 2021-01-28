<?php
/**
 * This is the model class for table "category".
 *
 * The followings are the available columns in table 'category':
 * @property integer $id
 * @property string $title
 * @property string $description
 * @property string $photo_ext
 *  * 
 * @property integer $ordering
 
 * @property CUploadedFile $photoFile
 * @property string $photo
 * @property string $bigPhoto
 * @property string $smallPhoto
 * @property string $tmbPhoto
 * @property string $path Get path for images directory
 * @property string $pathForHtml Get html path for images directory
 */
use common\components\helpers\HYii as Y;
use common\components\helpers\HArray as A;
use common\components\helpers\HDb;
use settings\components\helpers\HSettings;

class Category extends \common\components\base\ActiveRecord
{
    const SHOW_CATEGORIES_MODE_DEFAULT=0;
    const SHOW_CATEGORIES_MODE_SHOW=1;
    const SHOW_CATEGORIES_MODE_HIDE=2;
    const SHOW_CATEGORIES_MODE_SHOW_INSUB=3;
    const SHOW_CATEGORIES_MODE_HIDE_INSUB=4;
    
    public function behaviors()
    {
        return A::m(parent::behaviors(), array(
            'NestedSetBehavior'=>array(
                'class'=>'ext.yiiext.behaviors.trees.NestedSetBehavior',
                'leftAttribute'=>'lft',
                'rightAttribute'=>'rgt',
                'levelAttribute'=>'level',
                'hasManyRoots'=>true
            ),
        	'aliasBehavior'=>'\DAliasBehavior',
        	'metaBehavior'=>'\MetadataBehavior',
        	'mainImageBehavior'=>[
        		'class'=>'\common\ext\file\behaviors\FileBehavior',
        		'attribute'=>'main_image',
        		'attributeLabel'=>'Главная фотография',
        		'attributeEnable'=>'main_image_enable',
        		'attributeAlt'=>'main_image_alt',
        		'enableValue'=>true,
        		'defaultSrc'=>'/images/shop/category_no_image.png',
        		'imageMode'=>true
        	],
        	'updateTimeBehavior'=>[
        		'class'=>'\common\ext\updateTime\behaviors\UpdateTimeBehavior',
        		'addColumn'=>false
        	],
            'sitemapAutoGenerateBehavior'=>'\SitemapAutoGenerateBehavior'
        ));
    }
    
	/**
	 * @return string the associated database table name
	 */
	public function tableName()
	{
		return 'category';
	}

	/**
	 * @return array validation rules for model attributes.
	 */
	public function rules()
	{
		return $this->getRules(array(
			array('title', 'required'),
			array('title', 'length', 'max'=>255),
			array('description, parent_id, show_categories_mode', 'safe'),
			array('id, title, description, ordering', 'safe', 'on'=>'search'),
		));
	}
    
	/**
	 * {@inheritDoc}
	 * @see CActiveRecord::relations()
	 */
	public function relations()
	{
		return $this->getRelations(array(
            'tovars'=>array(self::HAS_MANY, 'Product', '`t`.`category_id`'),
            'products'=>array(self::HAS_MANY, 'Product', 'category_id'),
			'images'=>array(self::HAS_MANY, 'CImage', 'item_id',
				'condition'=>'model = :model',
				'params'=>array(':model' => strtolower(get_class($this))),
				'order'=>'images.ordering'
			),
			'productCount'=>[self::STAT, 'Product', 'category_id']
		));
	}
    
	/**
	 * {@inheritDoc}
	 * @see CActiveRecord::scopes()
	 */
    public function scopes()
	{
		return $this->getScopes();
	}

    /**
     * Scope: выбор категорий по ЧПУ бренда
     * @param string $alias ЧПУ бренда
     */
    public function byBrandAlias($alias)
    {
    	$cacheId=md5('brand_'.$alias.'_categoryIDs');
    	$categoryIDs=\Yii::app()->cache->get($cacheId);
    	if(!$categoryIDs && !is_array($categoryIDs)) {
    	     $categoryIDs=HDb::queryColumn(
    	     	'SELECT `t`.`category_id` FROM `product` AS `t`'
    	     	. ' INNER JOIN `brand` as `b` ON (`t`.`brand_id`=`b`.`id`)'
    	     	. ' WHERE `b`.`alias`=:alias GROUP BY `t`.`category_id`',
    	     	[':alias'=>$alias]
    	     );
    	     \Yii::app()->cache->set($cacheId, $categoryIDs);
    	}

    	$criteria=new CDbCriteria;
    	if(!empty($categoryIDs)) {
	    	$criteria->addInCondition('`t`.`id`', $categoryIDs);
	    }
	    else {
	    	$criteria->AddCondition('`t`.`id`<>`t`.`id`');
	    }
    	$this->getDbCriteria()->mergeWith($criteria);

    	return $this;
    }

    /**
     * Scope: выбор категорий по id бренда
     * @param string $id id бренда
     */
    public function byBrandId($id)
    {
    	$cacheId=md5('brand_id_'.$id.'_categoryIDs');
    	$categoryIDs=\Yii::app()->cache->get($cacheId);
    	if(!$categoryIDs && !is_array($categoryIDs)) {
    	     $categoryIDs=HDb::queryColumn(
    	     	'SELECT `t`.`category_id` FROM `product` AS `t` WHERE `t`.`brand_id`=:id GROUP BY `t`.`category_id`',
    	     	[':id'=>$id]
    	     );
    	     \Yii::app()->cache->set($cacheId, $categoryIDs);
    	}

    	$criteria=new CDbCriteria;
    	if(!empty($categoryIDs)) {
	    	$criteria->addInCondition('`t`.`id`', $categoryIDs);
	    }
	    else {
	    	$criteria->AddCondition('`t`.`id`<>`t`.`id`');
	    }
    	$this->getDbCriteria()->mergeWith($criteria);

    	return $this;
    }

	/**
	 * @return array customized attribute labels (name=>label)
	 */
	public function attributeLabels()
	{
		return $this->getAttributeLabels(array(
			'id' => 'ID',
			'title' => 'Название',
			'description' => 'Описание',
			'ordering' => 'Порядок',
            'parent_id'=>'Родитель',
            'show_categories_mode'=>'Показывать список категорий на странице категорий'
		));
	}
    
    public function showCategoriesModes()
    {
        return [
            self::SHOW_CATEGORIES_MODE_DEFAULT=>'По умолчанию',
            self::SHOW_CATEGORIES_MODE_SHOW=>'Показывать только для данной категории',
            self::SHOW_CATEGORIES_MODE_HIDE=>'Скрыть только для данной категории',
            self::SHOW_CATEGORIES_MODE_SHOW_INSUB=>'Показывать для данной категории и всех подкатегорий',
            self::SHOW_CATEGORIES_MODE_HIDE_INSUB=>'Скрыть для данной категории и всех подкатегорий',
        ];
    }
    
    public function isShowCategoriesList()
    {
        $show=false;
        if(D::cms('shop_show_categories')) {
            $shopSettings=HSettings::getById('shop');
            if($shopSettings->show_categories_on_category_page) {
                switch($this->show_categories_mode) {
                    case self::SHOW_CATEGORIES_MODE_SHOW:
                    case self::SHOW_CATEGORIES_MODE_SHOW_INSUB:
                        $show=true;
                        break;
                    case self::SHOW_CATEGORIES_MODE_HIDE:
                    case self::SHOW_CATEGORIES_MODE_HIDE_INSUB:
                        $show=false;
                        break;
                    default:
                        if(!$this->isRoot()) {
                            if($ancestors=$this->ancestors()->findAll(['select'=>'id, lft, rgt, root, level, show_categories_mode'])) {
                                foreach($ancestors as $ancestor) {
                                    switch((int)$ancestor->show_categories_mode) {
                                        case self::SHOW_CATEGORIES_MODE_SHOW_INSUB:
                                            return true;
                                        case self::SHOW_CATEGORIES_MODE_SHOW:
                                        case self::SHOW_CATEGORIES_MODE_HIDE_INSUB:
                                            return false;
                                        case self::SHOW_CATEGORIES_MODE_HIDE:
                                            return ((int)$shopSettings->show_categories_on_category_page_default > 0);
                                    }
                                }
                            }
                        }
                        $show=((int)$shopSettings->show_categories_on_category_page_default > 0);
                }
            }
        }
        return $show;
    }

	/**
	 * {@inheritDoc}
	 * @see CActiveRecord::afterSave()
	 */
	public function afterSave()	
	{		
		parent::afterSave();
		
		Y::cacheFlush();
	}

	/**
	 * Поиск категорий
	 * @return \CActiveDataProvider
	 */
	public function search()
	{
		$criteria=new CDbCriteria;

		$criteria->compare('id',$this->id);
		$criteria->compare('title',$this->title,true);
		$criteria->compare('description',$this->description,true);
		$criteria->compare('ordering',$this->ordering);

		return new CActiveDataProvider($this, array(
			'criteria'=>$criteria,
		));
	}
	
	/**
	 * Получить максимальную цену из категории
	 * @param integer|boolean $id идентификатор категории. 
	 * По умолчанию (false) текущая.
	 * @return number|false возвращает false, если идентификатор не задан.
	 */
	public function getMaxPrice($id=false)
	{
		if($id === false) {
			$id=$this->id;
		}
		if($id) {
			return (int)HDb::queryScalar('SELECT MAX(`price`) FROM `product` WHERE `category_id`=:id', [':id'=>$id]);
		}
		
		return false;
	}
	
	/**
	 * Получить минимальную цену из категории
	 * @param integer|boolean $id идентификатор категории. 
	 * По умолчанию (false) текущая.
	 * @return number|false возвращает false, если идентификатор не задан.
	 */
	public function getMinPrice($id=false)
	{
		if($id === false) {
			$id=$this->id;
		}
		if($id) {
			return (int)HDb::queryScalar('SELECT MIN(`price`) FROM `product` WHERE `category_id`=:id', [':id'=>$id]);
		}
		
		return false;
	}
	
	/**
	 * Получение списка категорий
	 * @param \CDbCriteria|array|false $criteria критерий выборки
	 * @param string $prefix префикс для подкатегорий.
	 * @return array
	 */
	public function getCategories($criteria=false, $prefix='-')
	{
		$criteria=HDb::criteria($criteria);
		$criteria->order='root, lft';
		$criteria->select='id, title, root, lft, rgt, level';
		
		$categories=static::model()->findAll($criteria);
		if(Y::param('subcategories', false)) {
			$_categories=[];			
			foreach($categories as $category) {
				if ($category->level >1) {
					if($prefix) {
						$category->title=str_repeat($prefix, $category->level-1) . ' ' . $category->title;
					}
				}
				$_categories[]=$category;
			}
			$categories=$_categories;
		}
		
		return \CHtml::listData($categories, 'id', 'title');
	}
	
	/**
	 * Получить кол-во товаров в категории
	 * @param unknown $criteria
	 * @param unknown $descendantsLevel
	 * @return string
	 */
	public function getProductsCount($criteria=false, $descendantsLevel=false)
	{
        $categoryIDs=[];
        if($descendantsLevel) {
	       	$descendants=$this->descendants($descendantsLevel)->findAll(['index'=>'id', 'select'=>'id']);
       		if($descendants) {
    	    	$categoryIDs=array_keys($descendants);
	        }
        }
        $categoryIDs[]=$this->id;

        $criteria=HDb::criteria($criteria);
	    $criteria->addInCondition('`t`.`category_id`', $categoryIDs);
        $criteria->mergeWith(\Product::model()->getRelatedCriteria($categoryIDs), 'OR');
        $criteria->select='`t`.`id`';

        return \Product::model()->count($criteria);
	}
}
