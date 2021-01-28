<?php
/**
 * Валидатор ЧПУ для DishCMS
 */
class DUrlValidator extends \CValidator
{
	/**
	 * @var string паттерн проверки ЧПУ.
	 */
	public $pattern='/^[0-9\-a-z]*[a-z][0-9\-a-z]*$/u';
	
	/**
	 * @var string паттерн проверки ЧПУ для \CValidator::clientValidateAttribute().
	 */
	public $clientPattern='/^[0-9\-a-z]*[a-z][0-9\-a-z]*$/';
	
	/**
	 * @var boolean регистро-(не)зависимость. По умолчанию FALSE (регистро-независимый).
	 */
	public $caseSensitive=false;
	
	/**
	 * @var string текст сообщения об ошибке
	 */
	public $message='{attribute} может содержать только латинские символы, цифры и символ "-"';
	
	/**
	 * (non-PHPdoc)
	 * @see CValidator::validateAttribute()
	 */
	protected function validateAttribute($object, $attribute)
	{
		if(!empty($object->$attribute) && !preg_match($this->pattern.($this->caseSensitive?'':'i'), $object->$attribute)) {
			$this->addError($object, $attribute, $this->message);
		}
	}
	
	/**
	 * (non-PHPdoc)
	 * @see CValidator::clientValidateAttribute()
	 */
	public function clientValidateAttribute($object, $attribute)
	{
		$message=str_replace('{attribute}', $object->getAttributeLabel($attribute), $this->message);
		return 'if(value && !value.match('.$this->clientPattern.($this->caseSensitive?'':'i').')) { messages.push('.CJSON::encode($message).'); }';
	}
}
