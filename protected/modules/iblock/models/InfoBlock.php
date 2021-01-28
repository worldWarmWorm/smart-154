<?php

namespace iblock\models;
use \CActiveRecord;
use \CDbCriteria;
use \CActiveDataProvider;
use common\components\helpers\HYii as Y;

/**
 * This is the model class for table "info_block".
 *
 * The followings are the available columns in table 'info_block':
 * @property integer $id
 * @property string $title
 * @property string $code
 * @property integer $sort
 * @property integer $active
 * @property integer $use_preview
 * @property integer $use_description
 *
 * The followings are the available model relations:
 * @property InfoBlockElement[] $infoBlockElements
 * @property InfoBlockProp[] $infoBlockProps
 */
class InfoBlock extends CActiveRecord
{
	/**
	 * @return string the associated database table name
	 */
	public function tableName()
	{
		return 'info_block';
	}

	/**
	 * @return array validation rules for model attributes.
	 */
	public function rules()
	{
		// NOTE: you should only define rules for those attributes that
		// will receive user inputs.
		return array(
			array('title', 'required'),
			array('active, sort, use_preview, use_description', 'numerical', 'integerOnly'=>true),
			array('title, code', 'length', 'max'=>255),
			// The following rule is used by search().
			// @todo Please remove those attributes that should not be searched.
			array('id, title, code, sort', 'safe', 'on'=>'search'),
		);
	}

	/**
	 * @return array relational rules.
	 */
	public function relations()
	{
		// NOTE: you may need to adjust the relation name and the related
		// class name for the relations automatically generated below.
		return array(
			'infoBlockElements' => array(self::HAS_MANY, 'iblock\models\InfoBlockElement', 'info_block_id'),
			'infoBlockProps' => array(self::HAS_MANY, 'iblock\models\InfoBlockProp', 'info_block_id'),
		);
	}

	/**
	 * @return array customized attribute labels (name=>label)
	 */
	public function attributeLabels()
	{
		return array(
			'id' => 'ID',
			'title' => 'Заголовок',
			'code' => 'Символьный код',
			'sort' => 'Порядок',
            'active' => 'Активность',
            'use_preview' => 'Использовать для записей главное фото',
            'use_description' => 'Использовать для записей описание',
		);
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
	 * @return \CActiveDataProvider the data provider that can return the models
	 * based on the search/filter conditions.
	 */
	public function search()
	{
		// @todo Please modify the following code to remove attributes that should not be searched.

		$criteria=new CDbCriteria;

		$criteria->compare('id',$this->id);
		$criteria->compare('title',$this->title,true);
		$criteria->compare('code',$this->code,true);
		$criteria->compare('sort',$this->sort);
        $criteria->compare('active',$this->active);
        $criteria->compare('use_preview',$this->use_preview);
        $criteria->compare('use_description',$this->use_description);

		return new CActiveDataProvider($this, array(
			'criteria'=>$criteria,
		));
	}

    public static function model($className=__CLASS__)
    {
        return parent::model($className);
    }

    public static function getListForMenu () {
	    $list = array();
        $db = \Yii::app()->getDb();
        $blocks = $db->createCommand()->select('id, title')->from(self::tableName())->where('active=1')->order('sort ASC, title ASC')->queryAll();
        foreach ($blocks as $b) {
            $list[] = [
                'visible' => true,
                'active' => (Y::isAction(Y::controller(), 'iblockElements') && isset($_GET['block_id']) && ($_GET['block_id'] == $b['id'])),
                'label' => $b['title'],
                'url'=>['/admin/iblockElements/index', 'block_id' => $b['id']],
            ];
        }
	    return $list;
    }


    public function afterSave()
    {
        parent::afterSave();

        //Добавляем новые свойства
        if (isset($_POST['new_properties'])) {
            foreach ($_POST['new_properties'] as $attributes) {
                $prop = new InfoBlockProp();
                $prop->setAttributes($attributes);
                $prop->info_block_id = $this->id;
                $prop->save();
            }
        }

        $del_ids = [];

        //Сохраняем изменения по старым свойствам
        if (isset($_POST['properties'])) {
            foreach ($_POST['properties'] as $key=>$attributes) {
                if (empty($attributes['delete'])) {
                    $prop = InfoBlockProp::model()->findByPk($key);
                    $prop->setAttributes($attributes);
                    $prop->save();
                } else {
                    $del_ids[] = $key;
                }
            }
        }

        //Удаляем помеченные свойства
        if (!empty($del_ids)) {
            $criteria = new CDbCriteria();
            $criteria->addInCondition('id', $del_ids);
            InfoBlockProp::model()->deleteAll($criteria);
        }

    }

}
