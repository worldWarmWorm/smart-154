<?php
/**
 * Поведение атрибута активности.
 *
 */
use ARHelper as ARH;

class DActiveBehavior extends \CBehavior
{
	public $attribute='active';
	
	public $attributeId='id';
	
	public $attributeLabel;
	
	/**
	 * (non-PHPdoc)
	 * @see CBehavior::events()
	 */
	public function events()
	{
		return array(
			'onBeforeValidate'=>'beforeValidate'
		);
	}
	
	/**
     * (non-PHPdoc)
     * @see CActiveRecord::scopes()
     */
	public function scopes() 
	{
		return array(
			'actived'=>array(
				'condition'=>$this->attribute.'=1'
			)
		);
	}
	
	/**
	 * (non-PHPdoc)
	 * @see CModel::rules()
	 */
	public function rules()
	{
		return array(
			array($this->attribute, 'boolean')
		);
	}
	
	/**
	 * (non-PHPdoc)
	 * @see CModel::attributeLabels()
	 */
	public function attributeLabels()
	{
		return array(
			$this->attribute=>($this->attributeLabel !== null) ? $this->attributeLabel : \Yii::t('DActiveBehavior.dActiveBehavior', 'label.attribute')
		);
	}
	
	/**
	 * (non-PHPdoc)
	 * @see CActiveRecordBehavior::beforeValidate()
	 */
	public function beforeValidate()
	{
		$this->owner->{$this->attribute}=(int)$this->owner->{$this->attribute} ? 1 : 0;
		return true;
	}
	
	/**
	 * Проверяет значение атрибута на активность. 
	 * @return boolean активен. TRUE(активен)/FALSE(неактивен)
	 */
	public function isActive()
	{
		return ((int)$this->owner->{$this->attribute} == 1);
	}
	
	/**
	 * Сменить активность 
	 * @param boolean $update сохранить изменения или нет. По умолчанию FALSE(не сохранять).
	 * @return boolean 
	 */
	public function changeActive($update=false)
	{
		$this->owner->{$this->attribute} = (int)$this->owner->{$this->attribute} ? 0 : 1;
		
		if($update) {
			$owner=$this->owner;
			$owner->{$this->attribute}=new \CDbExpression('IF('.ARH::dbQC($this->attribute).'=1, 0, 1)');
			$owner->update(array($this->attribute));
		}
			
		return true;	
	}
}