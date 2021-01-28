<?php
/**
 * Основной контроллер раздела администрирования модуля
 *
 */
namespace ecommerce\modules\order\modules\admin\controllers;

use common\components\helpers\HYii as Y;
use common\components\helpers\HArray as A;
use common\components\helpers\HRequest as R;
use common\components\helpers\HModel;
use common\components\helpers\HAjax;
use ecommerce\modules\order\modules\admin\components\BaseController;
use ecommerce\modules\order\models\Order;

class DefaultController extends BaseController
{
	/**
	 * (non-PHPDoc)
	 * @see BaseController::$viewPathPrefix;
	 */
	public $viewPathPrefix='ecommerce.modules.order.modules.admin.views.default.';
	
    /**
	 * (non-PHPdoc)
	 * @see \СController::filters()
	 */
	public function filters()
	{
		return A::m(parent::filters(), [
			['\DModuleFilter', 'name'=>'shop'],
			'ajaxOnly + changeCompleted, changePaid, detail, updateComment, delete'
		]);
	}
    
	/**
	 * Action: Главная страница.
	 */
	public function actionIndex()
	{	
		$t=Y::ct('\ecommerce\modules\order\modules\admin\AdminModule.controllers/default');
		
        Y::publish([
            'path'=>\Yii::getPathOfAlias('ecommerce.modules.order.modules.admin.assets.controllers.default'),
            'js'=>'scripts.js',
            'css'=>'styles.css'
        ]);
        
		$this->setPageTitle($t('page.title'));
        $this->breadcrumbs=[
            \D::cms('shop_title', 'Каталог')=>'shop/index',
            $t('page.title')
        ];
        
        if(R::isAjaxRequest()) {
            $order=Order::model();
			$order->unsetAttributes();
			$order=HModel::massiveAssignment($order, true, false, 'search');
			$ordersDataProvider=$order->search();
	        $ordersDataProvider->pagination->pageSize=50;
            $this->renderPartial('ecommerce.modules.order.modules.admin.views.default._orders_gridview', [
                'dataProvider'=>$ordersDataProvider
            ]);
        }
        else {
			$order=Order::model();
			$order->unsetAttributes();
			$ordersDataProvider=$order->search();
            $ordersDataProvider->pagination->pageSize=50;
            $this->render($this->viewPathPrefix.'index', compact('ordersDataProvider'));
        }
	}
    
    /**
     * Action: смена статуса оплаты
     */
    public function actionChangePaid()
	{
		$ajax=HAjax::start();
		if($order=Order::model()->findByPk(R::post('item'))) {
			$order->paid=($order->paid == 1) ? 0 : 1;
			$ajax->data=['paid'=>$order->paid];
			$ajax->success=$order->save();
		}
		$ajax->end();
	}
    
    /**
     * Action: смена статуса заказа
     */
    public function actionChangeCompleted()
	{
		$ajax=HAjax::start();
        if($order=Order::model()->findByPk(R::post('item'))) {
            $order->completed = (int)!(bool)$order->completed;
			$ajax->data=[
                'status' => $order->completed, 
				'count' => Order::model()->uncompleted()->count()
            ];
			$ajax->success=$order->save();
		}
		$ajax->end();
	}
    
    /**
     * Action: сохранение комментария
     */
    public function actionUpdateComment()
	{
		$ajax=HAjax::start();
        if($order=Order::model()->findByPk(R::post('item'))) {
            $order->comment=R::post('comment');
			$ajax->success=$order->save();
		}
		$ajax->end();
	}
    
    /**
     * Action: удаление заказа
     */
    public function actionDelete($id)
	{
		$ajax=HAjax::start();
        if($order=Order::model()->findByPk($id)) {
            $order->delete();
			$ajax->success=true;
		}
		$ajax->end();
	}
    
     /**
     * Action: получение подробной информации о заказе
     */
    public function actionDetail()
	{
		$ajax=HAjax::start();
        if($order=Order::model()->findByPk(R::post('item'))) {
            $ajax->data=[
                'html'=>$this->renderPartial('ecommerce.modules.order.modules.admin.views.default._order_detail', compact('order'), true)				
            ];
			$ajax->success=$order->save();
		}
		$ajax->end();
	}
}
