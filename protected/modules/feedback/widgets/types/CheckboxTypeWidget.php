<?php
/**
 * String type widget
 *
 */
namespace feedback\widgets\types;

class CheckboxTypeWidget extends BaseTypeWidget
{
	/**
	 * (non-PHPdoc)
	 * @see \feedback\widgets\types\BaseTypeWidget::run()
	 */
	public function run($name, \feedback\components\FeedbackFactory $factory, \CActiveForm $form)
	{
		$this->render($this->getView('checkbox'), compact('name', 'factory', 'form'));
	}
}
