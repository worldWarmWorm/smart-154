<?php
/**
 * Email type widget
 *
 */
namespace feedback\widgets\types;

class FileTypeWidget extends BaseTypeWidget
{
	/**
	 * (non-PHPdoc)
	 * @see \feedback\widgets\types\BaseTypeWidget::run()
	 */
	public function run($name, \feedback\components\FeedbackFactory $factory, \CActiveForm $form)
	{
		$this->render($this->getView('file'), compact('name', 'factory', 'form'));
	}
}
