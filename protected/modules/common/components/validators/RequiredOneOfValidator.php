<?php
namespace common\components\validators;

/**
 * Валидатор обязательного наличия "Один из".
 */
class RequiredOneOfValidator extends \CValidator
{
    /**
     * @var array|string список дополнительных атрибутов для проверки. 
     */
    public $compareAttributes = [];
    
    /**
     * @var array|string список дополнительных атрибутов для проверки типа "Файл".
     */
    public $fileAttributes = [];
    
    /**
     * @var boolean обрезать с краев пробелы у значений атрибутов. 
     */
    public $trim = true;
    
    protected function validateAttribute($object, $attribute)
    {
        $fileAttributes=$this->normalizeAttributes($this->fileAttributes);
        $compareAttributes=$this->normalizeAttributes($this->compareAttributes);
        array_unshift($compareAttributes, $attribute);
        
        $attributeLabels=[];
        foreach($compareAttributes as $attribute) {
            $value=$object->$attribute;
            if(!$this->isEmpty($value,$this->trim)) {
                return true;
            }
            if(isset($fileAttributes[$attribute])) $fileAttribute=$fileAttributes[$attribute];
            elseif(in_array($attribute, $fileAttributes)) $fileAttribute=$attribute;
            if(!empty($fileAttribute) && \CUploadedFile::getInstance($object, $fileAttribute)) {
                return true;
            }
            $attributeLabels[$attribute]=$object->getAttributeLabel($attribute);
        }

        $message=($this->message!==null) ? $this->message : 'Одно из полей "'.implode('", "', $attributeLabels) . '" обязательно для заполнения';
        $idx=0;
        foreach($attributeLabels as $attribute=>$label) {
            if($idx++) $message='';
            $this->addError($object, $attribute, $message);
        }
    }
    
    protected function normalizeAttributes($attributes)
    {
        if(empty($attributes)) {
            $attributes=[];
        }
        elseif(is_string($attributes)) {
            $attributes = explode(',', preg_replace('/\s+/', '', $attributes));
        }
        return $attributes;
    }
}