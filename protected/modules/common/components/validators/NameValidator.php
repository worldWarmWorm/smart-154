<?php
namespace common\components\validators;

use common\components\helpers\HYii as Y;

/**
 * Валидатор Имени / Фамилии / Отчества.
 */
class NameValidator extends \CValidator
{
    /**
     * 
     * {@inheritDoc}
     * @see \CValidator::validateAttribute()
     */
    public function validateAttribute($object, $attribute)
    {
        if($object->$attribute) {
            if(
                ($a=(($a1=trim($object->$attribute, '- ')) != ($a2=trim($object->$attribute))))
                || !preg_match('/^[абвгдеёжзийклмнопрстуфхцчшщъыьэюяАБВГДЕЁЖЗИЙКЛМНОПРСТУФХЦЧШЩЪЫЬЭЮЯa-zA-Z\-\s]+$/', trim($object->$attribute))
                || $c=preg_match('/^[\-]+$/', trim($object->$attribute))
                )
            {
                if($this->message === null) {
                    $this->message=Y::module('common')->t('components.validators.nameValidator.message');
                }
                $this->addError($object, $attribute, $this->message);
            }
        }
    }
}