<?php
/**
 * Simple breadcrumbs widget
 */
namespace menu\widgets\breadcrumbs;

use menu\widgets\breadcrumbs\BaseWidget;

class SimpleWidget extends BaseWidget
{
	/**
	 * (non-PHPdoc)
	 * @see \menu\widgets\breadcrumbs\BaseBreadcrumbsWidget::run()
	 */
	public function run()
	{
		$this->render('simple', array('breadcrumbs'=>$this->breadcrumbs));
	}
}