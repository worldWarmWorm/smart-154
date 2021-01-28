<?php echo "<?php\n"; ?>
/**
 * Основной контроллер раздела администрирования модуля
 *
 */
namespace <?=$this->moduleID?>\modules\admin\controllers;

use common\components\helpers\HYii as Y;
use <?=$this->moduleID?>\modules\admin\components\BaseController;

class DefaultController extends BaseController
{
	/**
	 * (non-PHPDoc)
	 * @see BaseController::$viewPathPrefix;
	 */
	public $viewPathPrefix='<?=$this->moduleID?>.modules.admin.views.default.';
	
	/**
	 * Action: Главная страница.
	 */
	public function actionIndex()
	{	
		$t=Y::ct('\<?=$this->moduleID?>\modules\admin\AdminModule.controllers/default');
		
		$this->setPageTitle($t('page.title'));
		$this->breadcrumbs=[$t('page.title')];
		
		$this->render($this->viewPathPrefix.'index');
	}
}