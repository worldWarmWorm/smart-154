<?php

/**
 * This is the model class for table "gallery".
 *
 * The followings are the available columns in table 'gallery':
 * @property integer $id
 * @property string $title
 * @property string $description
 * @property integer $preview_id
 */
use common\components\helpers\HYii as Y;
use common\components\helpers\HArray as A;

class Gallery extends \common\components\base\ActiveRecord
{
	/**
	 * @return string the associated database table name
	 */
	public function tableName()
	{
		return 'gallery';
	}
	
	public function behaviors()
	{
		$tc=Y::ct('CommonModule.labels', 'common');
		return A::m(parent::behaviors(), [
			'publishedBehavior'=>[
				'class'=>'\common\ext\active\behaviors\ActiveBehavior',
				'attribute'=>'published',
				'attributeLabel'=>$tc('published'),
				'scopeActivlyName'=>'published',
				'scopeNotActivlyName'=>'unpublished'
			],
            'updateTimeBehavior'=>[
        		'class'=>'\common\ext\updateTime\behaviors\UpdateTimeBehavior',
        		'addColumn'=>false
        	],
		]);
	}
	
	/**
	 * (non-PHPdoc)
	 * @see CActiveRecord::scopes()
	 */
	public function scopes()
	{
		return $this->getScopes([				
		]);
	}

	/**
	 * @return array validation rules for model attributes.
	 */
	public function rules()
	{
		return $this->getRules(array(
			array('title', 'required'),
			// array('preview_id', 'numerical', 'integerOnly'=>true),
			array('title', 'length', 'max'=>500),
			array('preview_id', 'length', 'max'=>255),
			array('id, title, description, preview_id', 'safe'),
		));
	}

	/**
	 * @return array relational rules.
	 */
	public function relations()
	{
		return $this->getRelations([
			'photos'=>[self::HAS_MANY, '\GalleryImg', 'gallery_id', 'order'=>'image_order']
		]);
	}

	/**
	 * @return array customized attribute labels (name=>label)
	 */
	public function attributeLabels()
	{
		return $this->getAttributeLabels(array(
			'id' => 'ID',
			'title' => 'Заголовок альбома',
			'description' => 'Описание альбома',
			'preview_id' => 'Preview',
		));
	}
	
	public function getAlbumLink($album_id)
	{
		return Gallery::model()->findByPk((int)$album_id);
	}

	public function getImageCount()
	{
		$count = count(GalleryImg::model()->findAll(array('condition'=>'gallery_id='.(int)$this->id)));
		return $count;
	}	

	public function getAlbumPreview() 
	{
		if(strlen($this->preview_id)){
			if(is_file('images/gallery/' . $this->preview_id)) {
				return '/images/gallery/' . $this->preview_id;
			}
		}
		
		$img = GalleryImg::model()->find(array('condition'=>'gallery_id='.(int)$this->id));
		if(count($img)) { 
			return '/images/gallery/tmb_' . $img->image;
		}
		else{
			return '/images/no_photo.png';
		}
	}

	public static function isTmbExist( $preview_id ) {
		$criteria=new CDbCriteria;
		$criteria->compare('preview_id',$preview_id, true);
		$cod = Gallery::model()->find($criteria);
		if(count($cod)){
			return true;
		} else {
			false;
		}
	}

	/**
	 * Retrieves a list of models based on the current search/filter conditions.
	 *
	 * Typical usecase:
	 * - Initialize the model fields with values from filter form.
	 * - Execute this method to get CActiveDataProvider instance which will filter
	 * models according to data in model fields.
	 * - Pass data provider to CGridView, CListView or any similar widget.
	 *
	 * @return CActiveDataProvider the data provider that can return the models
	 * based on the search/filter conditions.
	 */
	public function search()
	{
		// @todo Please modify the following code to remove attributes that should not be searched.

		$criteria=new CDbCriteria;

		$criteria->compare('id',$this->id);
		$criteria->compare('title',$this->title,true);
		$criteria->compare('description',$this->description,true);
		$criteria->compare('preview_id',$this->preview_id);

		return new CActiveDataProvider($this, array(
			'criteria'=>$criteria,
		));
	}
}
