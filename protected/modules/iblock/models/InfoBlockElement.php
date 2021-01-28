<?php
namespace iblock\models;
use common\components\base\ActiveRecord;
use common\components\helpers\HArray as A;
use common\components\helpers\HTools as T;
use common\components\helpers\HYii as Y;
/**
 * This is the model class for table "info_block_element".
 *
 * The followings are the available columns in table 'info_block_element':
 * @property integer $id
 * @property string $code
 * @property integer $active
 * @property string $title
 * @property string $preview
 * @property string $description
 * @property string $created_at
 * @property string $updated_at
 * @property integer $sort
 * @property integer $info_block_id
 *
 * The followings are the available model relations:
 * @property InfoBlock $infoBlock
 * @property InfoBlockElementProp[] $infoBlockElementProps
 */
class InfoBlockElement extends ActiveRecord
{

    const PROPERTY_PREFIX = 'prop___';

    public function behaviors()
    {
        $b = [
            'imageBehavior'=>[
                'class'=>'\common\ext\file\behaviors\FileBehavior',
                'attribute'=>'preview',
                'attributeLabel'=>'Изображение',
                'attributeEnable'=> null,
                'attributeAlt'=> null,
                'imageMode'=>true
            ],
        ];

        return A::m(parent::behaviors(), $b);
    }

    /**
     * добавляет к модели поведения для свойств типа файл/изображение
     */
    public function appendBehaviors() {
        $b = InfoBlockProp::getAdditionalBehaviors($this->info_block_id);
        if (!empty($b)) {
           $this->attachBehaviors($b);
        }
    }

	/**
	 * @return string the associated database table name
	 */
	public function tableName()
	{
		return 'info_block_element';
	}


    /**
     * @return array
     */
	public function relations()
	{
		// NOTE: you may need to adjust the relation name and the related
		// class name for the relations automatically generated below.
     	return $this->getRelations(array(
            'infoBlock' => array(self::BELONGS_TO, 'iblock\models\InfoBlock', 'info_block_id'),
            'infoBlockElementProps' => array(self::HAS_MANY, 'iblock\models\InfoBlockElementProp', 'element_id'),
        ));
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

		$criteria=new \CDbCriteria;

		$criteria->compare('id',$this->id);
		$criteria->compare('code',$this->code,true);
		$criteria->compare('active',$this->active);
		$criteria->compare('title',$this->title,true);
		$criteria->compare('preview',$this->preview,true);
		$criteria->compare('description',$this->description,true);
		$criteria->compare('created_at',$this->created_at,true);
		$criteria->compare('updated_at',$this->updated_at,true);
		$criteria->compare('sort',$this->sort);
		$criteria->compare('info_block_id',$this->info_block_id);

		return new \CActiveDataProvider($this, array(
			'criteria'=>$criteria,
		));
	}

    public static function model($className=__CLASS__)
    {
        return parent::model($className);
    }

    public function beforeSave()
    {
        if (parent::beforeSave()) {
            $d = date('Y-m-d H:i:s');
            if ($this->isNewRecord) {
                $this->created_at = $d;
            }
            $this->updated_at = $d;
            return true;
        }
        return false;
    }

    public function load_fields($prefix = InfoBlockElement::PROPERTY_PREFIX)
    {
        $criteria = new \CDbCriteria();
        $criteria->order = 'sort ASC';
        $criteria->compare('info_block_id', $this->info_block_id);
        $fieldsObj = InfoBlockProp::model()->findAll($criteria);
        $fieldsArr = array();
        if ($fieldsObj) {
            foreach ($fieldsObj as $obj) {
                $fieldsArr[$obj->id] = $obj->getField($prefix);
            }
        }

        if (!$this->isNewRecord) {
            $p_v = $this->infoBlockElementProps;
            if ($p_v) {
                foreach ($p_v as $p) {
                    if (isset($fieldsArr[$p->prop_id])) {
                        $fieldsArr[$p->prop_id]['value'] = $p->value;
                    }
                }
            }
        }
        $this->setFields($fieldsArr);
        $this->appendBehaviors();
    }

    ////

    private $_fieldsByKeys;
    private $_fields;
    private $_rules = [];
    private $_properties;

    public function setFields($fields) {
        $rules = [];
        $properties = [];

        foreach ($fields as $key => $field) {
            $fieldName = $field['name'];
            $properties[$fieldName] = isset($field['value']) ? $field['value'] : '';

            if (count($field['validation'])) {
                foreach ($field['validation'] as &$validation_row) {
                    $params = array($fieldName, $validation_row['type']);
                    if (isset($validation_row['params']) && is_array($validation_row['params'])) {
                        $params = array_merge($params, $validation_row['params']);
                    }
                    $rules[] = $params;
                }
            } else {
                $rules[] = array($fieldName, 'safe');
            }

            $this->_fieldsByKeys[$fieldName] = $field;
        }

        $this->_fields = $fields;
        $this->_properties = $properties;
        $this->_rules = $rules;
    }

    public function getFields() {
        return $this->_fields;
    }

    public function rules() {
        //todo учитывать переданный массим. здесь он вроде бы и не нужен, объявлен сейчас исключительно для нормального переопределения метода родительского класса
        $rules =  array(
            array('title, info_block_id', 'required'),
            array('active, sort, info_block_id', 'numerical', 'integerOnly'=>true),
            array('code, title, preview', 'length', 'max'=>255),
            array('created_at, updated_at, description', 'safe'),
            // The following rule is used by search().
            // @todo Please remove those attributes that should not be searched.
            array('id, code, active, title, preview, description, created_at, updated_at, sort, info_block_id', 'safe', 'on'=>'search'),
        );
        return $this->getRules(array_merge($rules, $this->_rules));
    }

    public function getProperties() {
        return $this->_properties;
    }

    public function getFieldsByKeys() {
        return $this->_fieldsByKeys;
    }


    public function setProperty($name, $value){
        if (isset($this->_properties[$name])) {
            $this->_properties[$name] = $value;
        }
    }

    public function __get($name) {
        if (isset($this->_properties[$name])) {
            return $this->_properties[$name];
        }
        return parent::__get($name);
    }

    public function __set($name, $value) {
        if (isset($this->_fieldsByKeys[$name])
            && (isset($this->_fieldsByKeys[$name]['type'])
                && ($this->_fieldsByKeys[$name]['type']) == InfoBlockProp::TYPE_NUMBER)
        ) {
            $value = T::makeNumber($value);
        }
        if (isset($this->_properties[$name])) {
            return $this->_properties[$name] = $value;
        }
        //todo: check this
        try {
            return parent::__set($name, $value);
        } catch (\Exception $e) {
            return null;
        }
    }

    /**
     * @return array customized attribute labels (name=>label)
     */
    public function attributeLabels() {
        //todo учитывать переданный массим. здесь он вроде бы и не нужен, объявлен сейчас исключительно для нормального переопределения метода родительского класса
        $result = array(
            'id' => 'ID',
            'code' => 'Символьный код',
            'active' => 'Активность',
            'title' => 'Название',
            'preview' => 'Изображение',
            'description' => 'Описание',
            'created_at' => 'Дата создания',
            'updated_at' => 'Дата изменения',
            'sort' => 'Порядок',
            'info_block_id' => 'Инфо-блок',
        );

        $fields = $this->getFields();

        if (!empty($fields)) {
            foreach ($fields as $v) {
                $result[$v['name']] = $v['title'];
            }
        }

        return $this->getAttributeLabels($result);
    }

    public function afterSave()
    {
        parent::afterSave();

        InfoBlockElementProp::model()->deleteAll(
            'element_id = :element_id',
            ['element_id' => $this->id]
        );

        foreach ($this->getFields() as $f) {
            //todo:: продумать сохранение фото и файлов
            if (isset($this->_properties[$f['name']])) {
                $prop = new InfoBlockElementProp();
                $prop->element_id = $this->id;
                $prop->prop_id = $f['prop_id'];
                $input = $this->_properties[$f['name']];

                switch ($f['type']) {
                    case InfoBlockProp::TYPE_STRING :
                        $value = $f['multiple'] ?
                            \CJSON::encode($input)
                            : $input;
                        break;
                    case InfoBlockProp::TYPE_CHECKBOX :
                        $value = $f['multiple'] ?
                            \CJSON::encode($input)
                            : $input;
                        break;
                    case InfoBlockProp::TYPE_NUMBER :
                        $value = $f['multiple'] ?
                            \CJSON::encode(T::makeNumberArrayItems($input))
                            : T::makeNumber($input);
                        break;
                    case InfoBlockProp::TYPE_LIST :
                        $value = $f['multiple'] ?
                            \CJSON::encode($input)
                            : $input;
                        break;
                    case InfoBlockProp::TYPE_FILE :






                        $value = $f['multiple'] ?
                            \CJSON::encode($input)
                            : $input;
                        break;
                    case InfoBlockProp::TYPE_IMAGE :




                        $value = $f['multiple'] ?
                            \CJSON::encode($input)
                            : $input;
                        break;
                    case InfoBlockProp::TYPE_TEXT_AREA :
                    case InfoBlockProp::TYPE_FULL_TEXT:
                    case InfoBlockProp::TYPE_TEXT :
                        $value = $input;
                        break;
                    default :
                        $value = null;
                }

                $prop->value = $value;
                $prop->save();
            }
        }
        Y::cacheFlush();
    }

    /**
     * {@inheritDoc}
     * @see CActiveRecord::scopes()
     */
    public function scopes()
    {
        return $this->getScopes();
    }


}
