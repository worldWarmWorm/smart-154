<?php
/**
 * Checkbox type
 *
 */
namespace feedback\components\types;

class CheckboxType extends BaseType
{
	/**
	 * (non-PHPdoc)
	 * @see \feedback\components\types\BaseType::rules()
	 */
	public function rules()
	{
		return \CMap::mergeArray(parent::rules(), array(
			array($this->_name, 'length', 'max'=>255),
		));
	} 
	
	/**
	 * (non-PHPdoc)
	 * @see \feedback\components\types\BaseType::getSqlColumnDefinition()
	 */
	public function getSqlColumnDefinition()
	{
		return '`' . $this->_name . '` TINYINT(1)';// COMMENT "' . $this->_label . '"';
	}	
}
