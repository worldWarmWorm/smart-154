<?php

/**
 * This is the model class for table "brand".
 *
 * The followings are the available columns in table 'brand':
 * @property integer $id
 * @property string $alias
 * @property string $title
 * @property string $logo
 * @property string $preview_text
 * @property string $detail_text
 */
use common\components\helpers\HArray as A;

class Brand extends \common\components\base\ActiveRecord
{
	/**
	 * @return string the associated database table name
	 */
	public function tableName()
	{
		return 'brand';
	}

	/**
	 * (non-PHPdoc)
	 * @see CModel::behaviors()
	 */
	public function behaviors()
	{
		return A::m(parent::behaviors(), array(
			'aliasBehavior'=>array('class'=>'DAliasBehavior'),
			'metaBehavior'=>array('class'=>'MetadataBehavior'),
			'imageBehavior'=>array(
				'class'=>'\ext\D\image\components\behaviors\ImageBehavior',
				'attribute'=>'logo',
				'attributeLabel'=>'Логотип', // \Yii::t('brand', 'attribute.label.logo'),
				'tmbWidth'=>D::cms('brand_preview_width', 320),
				'tmbHeight'=>D::cms('brand_preview_height', 240),
			),
			'activeBehavior'=>array(
				'class'=>'DActiveBehavior',
				'attributeLabel'=>'Активен' //\Yii::t('brand', 'attribute.label.active')
			),
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
			['title, alias', 'required'],
			array('title', 'length', 'max'=>255),
			array('preview_text, detail_text', 'safe'),
			array('id, title, preview_text, detail_text', 'safe', 'on'=>'search'),
		));
	}

	/**
	 * {@inheritDoc}
	 * @see DActiveRecord::scopes()
	 */
    public function scopes()
    { 
        return $this->getScopes([
        	'previewColumns'=>array('select'=>'`t`.`id`, `t`.`alias`, title, logo, preview_text')
        ]);
    }
    
    /**
     * {@inheritDoc}
     * @see DActiveRecord::relations()
     */
    public function relations()
    {
    	return $this->getRelations([
    			
    	]);
    }

	/**
	 * @return array customized attribute labels (name=>label)
	 */
	public function attributeLabels()
	{
		return $this->getAttributeLabels(array(
			'id' => 'ID',
			'title' => 'Название',
			'preview_text' => 'Анонс',
			'detail_text' => 'Подробный текст',
		));
	}

	public static function getListData($actived=false)
	{
		$model=new self();
		if($actived) $model=$model->actived();
		return \CHtml::listData($model->findAll(['select'=>'id,title']), 'id', 'title');
	}

	public function afterSave()
	{
        // очистка всего кэша
        \Yii::app()->cache->flush();

        return parent::afterSave();
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
		$criteria->compare('alias',$this->alias,true);
		$criteria->compare('title',$this->title,true);
		$criteria->compare('logo',$this->logo,true);
		$criteria->compare('preview_text',$this->preview_text,true);
		$criteria->compare('detail_text',$this->detail_text,true);

		return new CActiveDataProvider($this, array(
			'criteria'=>$criteria,
		));
	}
}
