<?php
/**
 * Created by JetBrains PhpStorm.
 * User: Rick
 * Date: 05.03.12
 * Time: 11:49
 * To change this template use File | Settings | File Templates.
 */
use common\ext\email\components\helpers\HEmail;

class QuestionController extends Controller
{
	/**
	 * (non-PHPdoc)
	 * @see AdminController::filters()
	 */
	public function filters()
	{
		return CMap::mergeArray(parent::filters(), array(
			array('DModuleFilter', 'name'=>'question')
		));
	}
	
    public function actionIndex()
    {
        $model = new Question();

        if (isset($_POST['Question'])) {
            $model->attributes = $_POST['Question'];

            if ($model->save()) {
            	HEmail::cmsAdminSend(true, ['model'=>$model], 'application.views.question._email');
                echo 'ok';
            }
            else {
                echo 'error';
            }

            Yii::app()->end();
        }

        $list = Question::model()->published()->findAll(array('order'=>'created DESC'));

        $this->prepareSeo('Вопрос-ответ');
        
        $this->breadcrumbs->add('Вопрос-ответ');
        
		$this->render('index', compact('list', 'model'));
    }
}
