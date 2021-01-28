<?php
namespace common\components\validators;

use common\components\helpers\HYii as Y;

/**
 * Валидатор сложного пароля
 */
class StrongPasswordValidator extends \CValidator
{
    /**
     * Минимальная длина пароля
     * @var integer
     */
    public $min=6;
    
    /**
     * Максимальная длина пароля
     * @var integer
     */
    public $max=20;
    
    /**
     * 
     * {@inheritDoc}
     * @see \CValidator::validateAttribute()
     */
    public function validateAttribute($object, $attribute)
    {
        if($object->$attribute) {
            if(!preg_match('/((?=.*[a-z])(?=.*[A-Z])(?=.*[\\\\@#$%^&*\[\]\/!:;.,?\-+]).{'.(int)$this->min.','.(int)$this->max.'})/', $object->$attribute)) {
                if($this->message === null) {
                    $this->message=Y::module('common')->t('components.validators.strongPasswordValidator.message', ['{spec_chars}'=>'@#$%^&*-+[]\/!:;.,?']);
                }
                $this->addError($object, $attribute, $this->message);
            }
        }
    }
}