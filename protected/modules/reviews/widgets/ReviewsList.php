<?php
/**
 * Виджет формы добавления нового отзыва
 * 
 */
namespace reviews\widgets;

use reviews\models\Review;
use common\components\helpers\HYii as Y;

class ReviewsList extends \CWidget
{
	public $criteria=null;
	public $pagination=null;

	public $limit=null;
		
	/**
	 * (non-PHPdoc)
	 * @see \CWidget::init()
	 */
	public function init()
	{
	}
	
	/**
	 * (non-PHPdoc)
	 * @see \CWidget::run()
	 */
	public function run()
	{
		if($this->limit === null) {
			$this->limit=99999;
		}

		if($this->limit && empty($this->pagination['pageSize'])) {
			$this->pagination['pageSize']=$this->limit;
		}

		$dataProvider=Review::model()->actived()->getDataProvider($this->criteria, $this->pagination);
		
		$this->render('reviews_list', compact('dataProvider'));
	}
}
