<?php
/**
 * Frontend default controller
 * 
 */
namespace iblock\controllers;

use common\components\helpers\HRequest as R;
use iblock\components\controllers\FrontController;
use iblock\models\InfoBlock;
use iblock\components\InfoBlockHelper;

class DefaultController extends FrontController
{
	/**
	 * Список элементов инфоблока
	 * @param integer $id идентификатор инфоблока
	 */
	public function actionIndex($id)
	{
		if(!($iblock=InfoBlock::model()->findByPk($id))) {
			R::e404();
		}
		
		$this->prepareSeo($element['title']);
		$this->breadcrumbs->add($iblock->title);
		
		$this->render('index', compact('iblock'));
	}
	
	/**
	 * Action: просмотр элемента инфоблока
	 * @param integer $id идентификатор элемента инфоблока
	 */
	public function actionView($id)
	{
		if($element=InfoBlockHelper::getElementByPk($id, true)) {
			if($iblock=InfoBlock::model()->findByPk($element['info_block_id'])) {
				$this->prepareSeo($element['title']);
				$this->breadcrumbs->add($iblock->title, ['infoblock/index', 'id'=>$iblock->id]);
				$this->breadcrumbs->add($element['title']);
				$this->render('view', compact('iblock', 'element'));
				return true;
			}
		}
		
		R::e404();		
	}
}