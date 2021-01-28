<?php
namespace DOrder\models;
/**
 * This is the model class for table "order_customer_fields".
 *
 * The followings are the available columns in table 'order_customer_fields':
 * @property integer $id
 * @property string $name
 * @property string $label
 * @property string $placeholder
 * @property string $type
 * @property integer $required
 * @property integer $sort
 * @property string $default_value
 * @property string $values - json со списком возможных значений (для выпадающего списка)
 * @property string $mask
 */
class OrderCustomerFields extends \CActiveRecord
{

	const TYPE_TEXT = 'text';
	const TYPE_PHONE = 'phone';
	const TYPE_EMAIL = 'email';
	const TYPE_TEXTAREA = 'textarea';
	const TYPE_SELECT = 'select';
	const TYPE_CHECKBOX = 'checkbox';
	const TYPE_CHECKBOX_GROUP = 'checkbox_group';
	const TYPE_RADIOBUTTON = 'radiobutton';

	public static function getTypes() {
		return [
			self::TYPE_TEXT=> 'тексовое поле',
			self::TYPE_PHONE => 'номер телефона',
			self::TYPE_EMAIL => 'e-mail',
			self::TYPE_TEXTAREA => 'многострочное текстовое поле',
			self::TYPE_SELECT => 'выпадающий список',
			self::TYPE_CHECKBOX => 'флажок',
			self::TYPE_CHECKBOX_GROUP => 'группа флажков',
			self::TYPE_RADIOBUTTON => 'группа переключателей',
		];
	}

	public static function getTypeTitle($t) {
		$list = static::getTypes();
		return isset($list[$t]) ? $list[$t] : '';
	}
	
	/**
	 * @return string the associated database table name
	 */
	public function tableName()
	{
		return 'order_customer_fields';
	}

	/**
	 * @return array validation rules for model attributes.
	 */
	public function rules()
	{
		// NOTE: you should only define rules for those attributes that
		// will receive user inputs.
		return array(
			array('name, type, label', 'required'),
			array('required, sort', 'numerical', 'integerOnly'=>true),
			array('name, type', 'length', 'max'=>25),
			array('label', 'length', 'max'=>100),
			array('placeholder, mask', 'length', 'max'=>50),
			array('default_value', 'length', 'max'=>255),
			array('values', 'safe'),
			// The following rule is used by search().
			// @todo Please remove those attributes that should not be searched.
			array('id, name, label, placeholder, type, required, sort, default_value, values, mask', 'safe', 'on'=>'search'),
		);
	}

	public function getField()
	{
		$validation = array();

		if ($this->required) {
            $validation[] = array(
                'type' => 'required',
                'params' => array()
            );
        }

        if ($this->type == OrderCustomerFields::TYPE_EMAIL) {
            $validation[] = array(
                'type' => 'email',
                'params' => array()
            );
        }

		if ($this->type == OrderCustomerFields::TYPE_PHONE) {
        	$validation[] = array(
                'type' => 'match',
                'params' => array('pattern'=>'/^\+7 \( \d{3} \) \d{3} - \d{2} - \d{2}$/')
            );
        }

		$values = array();
		if (!empty($this->values)) {
			$tmp = explode("\n", $this->values);
			foreach ($tmp as $v) {
			    $v = trim($v);
				$values[$v] = $v;
			}
		}
		$field = array(
			'name' => $this->name,
			'title' => $this->label,
			'type' => $this->type,
			'mask' => $this->mask,
			'value' => $this->default_value,
			'validation' => $validation,
			'values' => $values,
		);
		return $field;
	}

	/**
	 * @return array relational rules.
	 */
	public function relations()
	{
		// NOTE: you may need to adjust the relation name and the related
		// class name for the relations automatically generated below.
		return array(
		);
	}

	/**
	 * @return array customized attribute labels (name=>label)
	 */
	public function attributeLabels()
	{
		return array(
			'id' => 'ID',
			'name' => 'Имя',
			'label' => 'Подпись',
			'placeholder' => 'Подсказка',
			'type' => 'Тип',
			'required' => 'Обязательное поле',
			'sort' => 'Позиция',
			'default_value' => 'Значение по умолчанию',
			'values' => 'Возможные значения',
			'mask' => 'Маска',
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

		$criteria=new \CDbCriteria;

		$criteria->compare('id',$this->id);
		$criteria->compare('name',$this->name,true);
		$criteria->compare('label',$this->label,true);
		$criteria->compare('placeholder',$this->placeholder,true);
		$criteria->compare('type',$this->type,true);
		$criteria->compare('required',$this->required);
		$criteria->compare('sort',$this->sort);
		$criteria->compare('default_value',$this->default_value,true);
		$criteria->compare('values',$this->values,true);
		$criteria->compare('mask',$this->mask,true);

		$criteria->order = 'sort ASC';

		return new \CActiveDataProvider($this, array(
			'criteria'=>$criteria,
            'pagination'=>array(
                'pageSize'=>1000,
            ),
		));
	}

	/**
	 * Returns the static model of the specified AR class.
	 * Please note that you should have this exact method in all your CActiveRecord descendants!
	 * @param string $className active record class name.
	 * @return OrderCustomerFields|\CActiveRecord
	 */
	public static function model($className=__CLASS__)
	{
		return parent::model($className);
	}
}
