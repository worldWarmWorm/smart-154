<?php
/**
 * Валидатор файла изображения
 */
use YiiHelper as Y;

class DImageValidator extends \CFileValidator
{
	/**
	 * (non-PHPdoc)
	 * @see CFileValidator::validateFile()
	 */
	protected function validateFile($object, $attribute, $file)
	{
		if($file->type!==image_type_to_mime_type(exif_imagetype($file->tempName))) {
			$this->addError($object, $attribute, ($this->wrongMimeType!==null) ? $this->wrongMimeType : \Yii::t('DImageValidator.dImageValidator', 'wrongMimeType', array('{filename}'=>$file->name, '{type}'=>$file->type)));
		}
		else {
			parent::validateFile($object, $attribute, $file);
		}
	}
}