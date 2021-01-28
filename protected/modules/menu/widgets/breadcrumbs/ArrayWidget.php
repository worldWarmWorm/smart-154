<?php
/**
 * Array breadcrumbs widget
 */
namespace menu\widgets\breadcrumbs;

class ArrayWidget extends \CWidget
{
    /**
     * @var array breadcrumbs (array(url=>url, title=>title))
     */
	public $breadcrumbs = array();

	/**
	 * (non-PHPdoc)
	 * @see \menu\widgets\breadcrumbs\BaseBreadcrumbsWidget::run()
	 */
	public function run()
	{
		$this->render('array', array('breadcrumbs'=>$this->breadcrumbs));
	}
}