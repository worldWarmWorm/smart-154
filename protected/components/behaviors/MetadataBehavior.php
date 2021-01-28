<?php
/**
 * Metadata behavior
 *
 */
use common\components\helpers\HArray as A;

class MetadataBehavior extends \CBehavior
{
	public $attributeTitle='title';
	
	/**
	 * @var array виртуальные атрибуты поведения для модели вида array(attribute=>loaded), где
	 * attribute - имя атрибута
	 * loaded - атрибут загружен из модели \Metadata
	 */
	private $_attributes=['meta_h1', 'meta_title', 'meta_key', 'meta_desc', 'a_title', 'priority', 'changefreq', 'lastmod'];
    
    /**
     * @var array значение вируальных атрибутов для модели, вида array(attribute=>value)
     */
    private $values=[];

	/**
	 * {@inheritDoc}
	 * @see CComponent::__get()
	 */
	public function __get($name)
	{
		if(in_array($name, $this->_attributes)) {
			if(!$this->owner->isNewRecord && ($meta=$this->owner->getRelated('meta'))) {
				return $meta->$name;
			}
            elseif(array_key_exists($name, $this->values)) {
                return $this->values[$name];
            }
			return null;
		}
		return parent::__get($name);
	}
	
	/**
	 * {@inheritDoc}
	 * @see CComponent::__set()
	 */
	public function __set($name, $value)
	{
		if(in_array($name, $this->_attributes)) {
			if($meta=$this->owner->getRelated('meta')) {
				$meta->$name=$value;
				$this->owner->meta->$name=$value;
			}
            else {
                $this->values[$name]=$value;
            }
		}
		else {
			parent::__set($name, $value);
		}
	}

	/**
	 * Обработчик для магического метода __get().
	 * @param string $name имя атрибута
	 * @return NULL|mixed
	 */
	public function __handlerGet($name)
	{
		return function($name) {
			if(in_array($name, $this->_attributes)) {
				if(!$this->owner->isNewRecord && ($meta=$this->owner->getRelated('meta'))) {
					return $meta->$name;
				}
                elseif(array_key_exists($name, $this->values)) {
                    return $this->values[$name];
                }
				return null;
			}
			throw new \common\components\exceptions\PropertyNotFound();
		};
	}

	/**
	 * Обработчик для магического метода __set().
	 * @param string $name имя атрибута
	 * @param mixed $value значение
	 */
	public function __handlerSet($name, $value)
	{
		return function($name, $value) {
			if(in_array($name, $this->_attributes)) {
				if($meta=$this->owner->getRelated('meta')) {
					$this->owner->$name=$value;
					$this->owner->meta->$name=$value;
					$meta=$value;
				}
                else {
                    $this->values[$name]=$value;
                }
			}
			else {
				throw new \common\components\exceptions\PropertyNotFound();
			}
		};
	}

	/**
	 * {@inheritDoc}
	 * @see CBehavior::events()
	 */
	public function events()
	{
		return array(
			'onAfterSave'=>'afterSave',
			'onAfterDelete'=>'afterDelete'
		);
	}
	
	/**
	 * Дополнительные правила для модели
	 * @return array
	 */
	public function rules()
	{
		return array(
			array('meta_h1, meta_title, a_title', 'length', 'max'=>255),
            array('a_title', 'match', 'pattern'=>'/^[^"]+$/', 'message'=>'Не допускается использование символа двойной кавычки (").'),
			array('meta_h1, meta_title, meta_key, meta_desc, priority, changefreq, lastmod, a_title', 'safe'),
			array('priority', 'numerical', 'min' => 0, 'max' => 1)
			
		);
	}
	
	/**
	 * Дополнительные связи для модели
	 * @return array
	 */
	public function relations()
	{
		return array(
			'meta'=>array(CActiveRecord::BELONGS_TO, 'Metadata', array('id'=>'owner_id'),
                'together'=>true,
                'condition'=>'owner_name = :ownerName',
                'params'=>array(':ownerName'=>strtolower(get_class($this->owner)))
            )
		);
	}
	
	/**
	 * Дополнительные метки атрибутов для модели
	 * @return array
	 */
	public function attributeLabels()
	{
		return array(
			'meta_title'=>'META: Заголовок',
			'meta_key'=>'META: Ключевые слова',
			'meta_desc'=>'META: Описание',
			'meta_h1'=>'H1',
			'a_title'=>'SEO &lt;A title="" ...&gt;',
			'priority'=>'Приоритетность URL относительно других URL на Вашем сайте',
			'changefreq'=>'Вероятная частота изменения этой страницы',
			'lastmod'=>'Дата последнего изменения файла',
		);
	}
		
	public function getPriority()
	{
		$meta=$this->owner->getRelated('meta');	
		return $meta->priority;
	}

	public function getChangefreq()
	{
		$meta=$this->owner->getRelated('meta');	
		return $meta->changefreq;
	}

	public function getLastmod()
	{
		$meta=$this->owner->getRelated('meta');	
		return $meta->lastmod;
	}

	public function getMetaH1()
	{
		$meta=$this->owner->getRelated('meta'); 
		return ($meta && $meta->meta_h1) ? $meta->meta_h1 : $this->owner->{$this->attributeTitle};
	}
	
	public function getMetaATitle()
	{
		$meta=$this->owner->getRelated('meta');
		return ($meta && $meta->a_title) ? $meta->a_title : $this->owner->{$this->attributeTitle};
	}
	
	/**
	 * @return boolean
	 */
	public function afterSave()
	{  
		$meta=$this->owner->getRelated('meta');
		if (!$meta) {
			$this->owner->meta = new Metadata();
			$this->owner->meta->owner_name = $this->_getOwnerName();
			$this->owner->meta->owner_id   = $this->owner->id;
		}
		$this->owner->meta->meta_h1 = $this->meta_h1; 
		$this->owner->meta->meta_title = $this->meta_title; 
		$this->owner->meta->meta_key = $this->meta_key; 
		$this->owner->meta->meta_desc = $this->meta_desc; 
		$this->owner->meta->a_title = $this->a_title; 
			
		$this->owner->meta->priority = $this->priority;
		$this->owner->meta->changefreq = $this->changefreq;
		$this->owner->meta->lastmod = date('Y-m-d', time());

		$this->owner->meta->save();
		return true;
	}
	
	public function afterDelete()
	{
		$model=Metadata::model()->find('owner_name=:ownerName AND owner_id=:ownerID', array(
			':ownerName'=>$this->_getOwnerName(),
			':ownerID'=>$this->owner->id
		));
		
		if($model) $model->delete();
		
		return true;
	}
	
	/**
	 * @access private
	 * @return string
	 */
	private function _getOwnerName()
	{
		return strtolower(get_class($this->owner));
	}
}
