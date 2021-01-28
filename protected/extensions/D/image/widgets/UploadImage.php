<?php
namespace ext\D\image\widgets;

class UploadImage extends \CWidget
{
	/** 
	 * @var \ext\D\image\components\behaviors\ImageBehavior объект поведения изображения
	 */ 
	public $behavior;
	
	/**
	 * @var \CActiveForm
	 */
	public $form;
	
	public $ajaxUrlDelete;
	
	/**
	 * (non-PHPdoc)
	 * @see CWidget::run()
	 */
	public function run()
	{
		$this->render('upload_image');
	}
}