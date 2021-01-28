<?php
namespace iblock\models;
use \CActiveRecord;
use \CDbCriteria;
use \CActiveDataProvider;

/**
 * This is the model class for table "info_block_prop".
 *
 * The followings are the available columns in table 'info_block_prop':
 * @property integer $id
 * @property string $title
 * @property integer $active
 * @property string $type
 * @property integer $multiple
 * @property integer required
 * @property string $code
 * @property integer $sort
 * @property integer $info_block_id
 *
 * @property string $default
 * @property string $options
 *
 * The followings are the available model relations:
 * @property InfoBlockElementProp[] $infoBlockElementProps
 * @property InfoBlockPropValue[] $infoBlockPropValues
 * @property InfoBlock $infoBlock
 */
class InfoBlockProp extends CActiveRecord
{
    const TYPE_CHECKBOX = "C";
    const TYPE_STRING = "S";
    const TYPE_NUMBER = "N";
    const TYPE_LIST = "L";
    const TYPE_FILE = "F";
    const TYPE_IMAGE = "I";
    const TYPE_TEXT_AREA = "A";
    const TYPE_TEXT = "T";
    const TYPE_FULL_TEXT = "R";

    const DEFAULT_SORT = 500;

    /**
     * @return string[]
     */
    public static function getTypesList()
    {
        return array(
            static::TYPE_STRING => 'Строка',
            static::TYPE_CHECKBOX => 'Да/Нет',
            static::TYPE_NUMBER => 'Число',
            static::TYPE_LIST => 'Список',
            static::TYPE_FILE => 'Файл',
            static::TYPE_IMAGE => 'Изображение',
            static::TYPE_TEXT_AREA => 'Многострочный текст',
            static::TYPE_FULL_TEXT => 'Текстовый редактор',
            static::TYPE_TEXT => 'Упрощённый текстовый редактор',
        );
    }

    /**
     * @param string $t
     * @return string
     */
    public static function getTypeTitle ($t) {
        $types = static::getTypesList();
        return isset($types[$t]) ? $types[$t] : 'UNKNOWN TYPE';
    }

    /**
	 * @return string the associated database table name
	 */
	public function tableName()
	{
		return 'info_block_prop';
	}

	/**
	 * @return array validation rules for model attributes.
	 */
	public function rules()
	{
		// NOTE: you should only define rules for those attributes that
		// will receive user inputs.
		return array(
			array('title, type, code, info_block_id', 'required'),
			array('active, multiple, sort, info_block_id, required', 'numerical', 'integerOnly'=>true),
			array('title, code, default', 'length', 'max'=>255),
			array('options', 'safe'),
			array('type', 'length', 'max'=>1),
			// The following rule is used by search().
			// @todo Please remove those attributes that should not be searched.
			array('id, title, active, type, multiple, code, sort, info_block_id, default, options', 'safe', 'on'=>'search'),
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
			'infoBlockElementProps' => array(self::HAS_MANY, 'iblock\models\InfoBlockElementProp', 'prop_id'),
            'infoBlockPropValues' => array(self::HAS_MANY, 'iblock\models\InfoBlockPropValue', 'prop_id'),
			'infoBlock' => array(self::BELONGS_TO, 'iblock\models\InfoBlock', 'info_block_id'),
		);
	}

	/**
	 * @return array customized attribute labels (name=>label)
	 */
	public function attributeLabels()
	{
		return array(
			'id' => 'ID',
			'title' => 'Подпись',
			'active' => 'Активное',
			'type' => 'Тип',
			'multiple' => 'Множественное',
			'code' => 'Код',
			'sort' => 'Порядок',
			'info_block_id' => 'Инфо-блок',
			'default' => 'Значение по умолчанию',
			'options' => 'Дополнительные параметры',
            'required' => 'Обязательное'
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
	 * @return CActiveDataProvider the data provider that can return the models
	 * based on the search/filter conditions.
	 */
	public function search()
	{
		// @todo Please modify the following code to remove attributes that should not be searched.

		$criteria=new CDbCriteria;

		$criteria->compare('id',$this->id);
		$criteria->compare('title',$this->title,true);
		$criteria->compare('active',$this->active);
		$criteria->compare('type',$this->type,true);
        $criteria->compare('multiple',$this->multiple);
        $criteria->compare('multiple',$this->required);
		$criteria->compare('code',$this->code,true);
		$criteria->compare('sort',$this->sort);
		$criteria->compare('info_block_id',$this->info_block_id);
		$criteria->compare('default',$this->default,true);
		$criteria->compare('options',$this->options,true);

		return new CActiveDataProvider($this, array(
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
            if (is_array($this->options) || is_object($this->options)) {
                $this->options = \CJSON::encode($this->options);
            }
            return true;
        }
        return false;
    }

    public function afterSave()
    {
        parent::afterFind();
        //Добавляем новые значения
        if (isset($_POST['new_values'])) {
            foreach ($_POST['new_values'] as $attributes) {
                $prop = new InfoBlockPropValue();
                $prop->setAttributes($attributes);
                $prop->prop_id = $this->id;
                $prop->save();
            }
        }

        $del_ids = [];

        //Сохраняем изменения по старым значениям
        if (isset($_POST['values'])) {
            foreach ($_POST['values'] as $key=>$attributes) {
                if (empty($attributes['delete'])) {
                    $prop = InfoBlockPropValue::model()->findByPk($key);
                    $prop->setAttributes($attributes);
                    $prop->save();
                } else {
                    $del_ids[] = $key;
                }
            }
        }

        //Удаляем помеченные значения
        if (!empty($del_ids)) {
            $criteria = new CDbCriteria();
            $criteria->addInCondition('id', $del_ids);
            InfoBlockPropValue::model()->deleteAll($criteria);
        }

    }

    public function getField($prefix = InfoBlockElement::PROPERTY_PREFIX)
    {
        $validation = array();

        if ($this->required) {
            $validation[] = array(
                'type' => 'required',
                'params' => array()
            );
        }
        $values = array();

        if ($this->type == static::TYPE_LIST) {
            $vals = $this->infoBlockPropValues;
            if (!empty($vals)) {
                foreach ($vals as $v) {
                    $values[$v->value_key] = $v->value_text;
                }
            }
        }

        $field = array(
            'prop_id' => $this->id,
            'name' => $prefix . $this->code,
            'title' => $this->title,
            'type' => $this->type,
            'value' => $this->default,
            'validation' => $validation,
            'values' => $values,
            'multiple' => false//$this->multiple, //todo: отладить работу с множественными полями и раскомментировать эту строку
        );

        return $field;
    }

    /**
     *
     * @param $iblock_id
     * @param string $property_prefix
     * @return array
     */
    public static function getAdditionalBehaviors($iblock_id, $property_prefix = InfoBlockElement::PROPERTY_PREFIX) {
	    /** @var InfoBlockProp[] $props */
	    $props = InfoBlockProp::model()->findAllByAttributes([
	        'info_block_id' => $iblock_id,
            'type' => [InfoBlockProp::TYPE_FILE, InfoBlockProp::TYPE_IMAGE]
        ]);

	    $b = [];

	    foreach ($props as $p) {
	        $atr = $property_prefix . $p->code;
            $b[$atr . 'PropertyBehavior'] = [
                'class'=>'\common\ext\file\behaviors\FileBehavior',
                'attribute' => $atr,
                'forProperty' => true,
                'attributeLabel' => $p->title,
                'attributeEnable'=> null,
                'attributeAlt'=> null,
                'imageMode'=> $p->type == InfoBlockProp::TYPE_IMAGE
            ];
        }

        return $b;
    }

}
