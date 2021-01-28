<?php

/**
 * This is the model class for table "gallery_img".
 *
 * The followings are the available columns in table 'gallery_img':
 * @property integer $id
 * @property integer $gallery_id
 * @property string $title
 * @property string $description
 * @property string $image
 */
use common\components\helpers\HArray as A;

class GalleryImg extends \common\components\base\ActiveRecord
{

	const MAX_LINEAR_SIZE = 1200;

	/**
	 * @return string the associated database table name
	 */
	public $files;
	public function tableName()
	{
		return 'gallery_img';
	}
    
    /**
	 * (non-PHPdoc)
	 * @see CModel::behaviors()
	 */
	public function behaviors()
	{
		return A::m(parent::behaviors(), array(
            'updateTimeBehavior'=>[
        		'class'=>'\common\ext\updateTime\behaviors\UpdateTimeBehavior',
        		'addColumn'=>false
        	],
		));
	}

	/**
	 * @return array validation rules for model attributes.
	 */
	public function rules()
	{
		// NOTE: you should only define rules for those attributes that
		// will receive user inputs.
		return $this->getRules(array(
			array('gallery_id', 'required'),
			array('gallery_id', 'numerical', 'integerOnly'=>true),
			array('title', 'length', 'max'=>500),
			    //устанавливаем правила для файла, позволяющие загружать
			    // только картинки!
			array('files', 'file', 'types'=>'jpg,jpeg,gif,png','maxSize'=>10485760, 'allowEmpty'=>true),
			
			array('image', 'length', 'max'=>255),
			// The following rule is used by search().
			// @todo Please remove those attributes that should not be searched.
			array('id, gallery_id, title, description, image', 'safe', 'on'=>'search'),
		));
	}

	/**
	 * @return array relational rules.
	 */
	public function relations()
	{
		// NOTE: you may need to adjust the relation name and the related
		// class name for the relations automatically generated below.
		return $this->getRelations(array(
		));
	}

	/**
	 * @return array customized attribute labels (name=>label)
	 */
	public function attributeLabels()
	{
		return $this->getAttributeLabels(array(
			'id' => 'ID',
			'gallery_id' => 'Gallery',
			'title' => 'Title',
			'description' => 'Desctiption',
			'image' => 'Image',
		));
	}

	public static function getAlbumImages( $album_id ){
		$return_data = GalleryImg::model()->findAll(array('condition'=>'gallery_id = ' . $album_id . ' order by image_order ASC'));
		if(!count($return_data)){
			return false;
		}
		return $return_data;
	}

	public function getImg(){
		return '/images/gallery/' . $this->image;
	}

	public function getTmb(){
		return '/images/gallery/tmb_' . $this->image;
	}

	public function getMainTmb(){
		return '/images/gallery/main_tmb_' . $this->image;
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
		$criteria->compare('gallery_id',$this->gallery_id);
		$criteria->compare('title',$this->title,true);
		$criteria->compare('description',$this->description,true);
		$criteria->compare('image',$this->image,true);

		return new CActiveDataProvider($this, array(
			'criteria'=>$criteria,
		));
	}

	/**
	 * Returns the static model of the specified AR class.
	 * Please note that you should have this exact method in all your CActiveRecord descendants!
	 * @param string $className active record class name.
	 * @return GalleryImg the static model class
	 */
	public static function model($className=__CLASS__)
	{
		return parent::model($className);
	}
}
