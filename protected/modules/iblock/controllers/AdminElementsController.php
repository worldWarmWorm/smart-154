<?php
/**
 * Admin backend controller
 *
 */
namespace iblock\controllers;

use iblock\components\controllers\BackendController;
use iblock\models\InfoBlock;
use iblock\models\InfoBlockElement;
use common\components\helpers\HArray as A;

class AdminElementsController extends BackendController
{
    public function actions()
    {
        return A::m(parent::actions(), [
            'removeImage'=>[
                'class'=>'\common\ext\file\actions\RemoveFileAction',
                'modelName'=>'iblock\models\InfoBlockElement',
                'behaviorName'=>'imageBehavior',
                'ajaxMode'=>true
            ]
        ]);
    }

    public function filters()
    {
        return A::m(parent::filters(), [
            'ajaxOnly + removeImage'
        ]);
    }

    /**
     * Creates a new model.
     * If creation is successful, the browser will be redirected to the 'view' page.
     * @param integer $block_id the ID of the info-block
     */
    public function actionCreate($block_id)
    {
        $model = new InfoBlockElement();
        $iblock = InfoBlock::model()->findByPk($block_id);
        $model->info_block_id = $block_id;
        $model->load_fields();

        // Uncomment the following line if AJAX validation is needed
        // $this->performAjaxValidation($model);

        if(isset($_POST['iblock_models_InfoBlockElement']))
        {
            $model->attributes=$_POST['iblock_models_InfoBlockElement'];
            if($model->save())
                $this->redirect(['/admin/iblockElements/index', 'block_id' => $block_id]);
        }

        $this->render('iblock.views.admin-elements.create',compact(
            'model', 'iblock'
        ));
    }

    /**
     * Updates a particular model.
     * If update is successful, the browser will be redirected to the 'view' page.
     * @param integer $id the ID of the model to be updated
     */
    public function actionUpdate($id)
    {
        $model=$this->loadModel(get_class(new InfoBlockElement()), $id);
        $model->load_fields();

        $block_id = $model->info_block_id;
        $iblock = InfoBlock::model()->findByPk($block_id);

        // Uncomment the following line if AJAX validation is needed
        // $this->performAjaxValidation($model);

        if(isset($_POST['iblock_models_InfoBlockElement']))
        {
            $model->attributes=$_POST['iblock_models_InfoBlockElement'];
            if($model->save())
                $this->redirect(['/admin/iblockElements/index', 'block_id' => $block_id]);
        }

        $this->render('iblock.views.admin-elements.update', compact(
            'model',
            'iblock'
        ));
    }

    /**
     * Deletes a particular model.
     * If deletion is successful, the browser will be redirected to the 'admin' page.
     * @param integer $id the ID of the model to be deleted
     */
    public function actionDelete($id)
    {
       $model = $this->loadModel(get_class(new InfoBlockElement()), $id);
       $block_id = $model->info_block_id;
        $model->delete();
        // if AJAX request (triggered by deletion via admin grid view), we should not redirect the browser
        if(!isset($_GET['ajax']))
            $this->redirect(isset($_POST['returnUrl']) ? $_POST['returnUrl'] : ['/admin/iblockElements/index', 'block_id' => $block_id]);
    }

    /**
     * Lists all models.
     * @param integer $block_id the ID of the info-block
     */
    public function actionIndex($block_id)
    {
        $model=new InfoBlockElement('search');
        $model->unsetAttributes();  // clear any default values
        $model->info_block_id = $block_id;
        $iblock = InfoBlock::model()->findByPk($block_id);
        if(isset($_GET['InfoBlock']))
            $model->attributes=$_GET['InfoBlock'];

        $this->render('iblock.views.admin-elements.index', compact(
            'model',
            'iblock'
        ));
    }

}