<?php
/**
 * Admin backend controller
 *
 */
namespace iblock\controllers;

use \AttributeHelper as A;
use iblock\components\controllers\BackendController;
use iblock\models\InfoBlock;
use iblock\models\InfoBlockElement;
use iblock\models\InfoBlockProp;

class AdminController extends BackendController
{

    public function accessRules()
    {
        return array(
            array('deny',
                'users'=>array('?')
            ),
        );
    }

    public function filters() {
        return array(
            'accessControl',
        );
    }



    /**
     * Creates a new model.
     * If creation is successful, the browser will be redirected to the 'view' page.
     */
    public function actionCreate()
    {
        $model = new InfoBlock;

        $prop_model = new InfoBlockProp;
        $prop_model->sort = '';

        // Uncomment the following line if AJAX validation is needed
        // $this->performAjaxValidation($model);

        if(isset($_POST['iblock_models_InfoBlock']))
        {
            $model->attributes=$_POST['iblock_models_InfoBlock'];
            if($model->save())
                $this->redirect(array('index'));
        }

        // publish assets
        \AssetHelper::publish(array(
            'path' 	=> \Yii::getPathOfAlias('iblock.assets'),
            'js' 	=> array('js/IblockAdmin.js')
        ));


        $this->render('iblock.views.admin.create',compact(
            'model', 'prop_model'
        ));
    }

    /**
     * Updates a particular model.
     * If update is successful, the browser will be redirected to the 'view' page.
     * @param integer $id the ID of the model to be updated
     */
    public function actionUpdate($id)
    {
        $model=$this->loadModel(get_class(new InfoBlock()), $id);

        $prop_model = new InfoBlockProp;
        $prop_model->info_block_id = $id;
        $prop_model->sort = '';

        // Uncomment the following line if AJAX validation is needed
        // $this->performAjaxValidation($model);

        if(isset($_POST['iblock_models_InfoBlock']))
        {
            $model->attributes=$_POST['iblock_models_InfoBlock'];
            if($model->save())
                $this->redirect(array('index'));
        }


        // publish assets
        \AssetHelper::publish(array(
            'path' 	=> \Yii::getPathOfAlias('iblock.assets'),
            'js' 	=> array('js/IblockAdmin.js')
        ));

        $this->render('iblock.views.admin.update', compact(
            'model',
            'prop_model'
        ));
    }

    /**
     * Deletes a particular model.
     * If deletion is successful, the browser will be redirected to the 'admin' page.
     * @param integer $id the ID of the model to be deleted
     */
    public function actionDelete($id)
    {
        $this->loadModel(get_class(new InfoBlock()), $id)->delete();

        // if AJAX request (triggered by deletion via admin grid view), we should not redirect the browser
        if(!isset($_GET['ajax']))
            $this->redirect(isset($_POST['returnUrl']) ? $_POST['returnUrl'] : array('index'));
    }

    /**
     * Lists all models.
     */
    public function actionIndex()
    {
        $model=new InfoBlock('search');
        $model->unsetAttributes();  // clear any default values
        if(isset($_GET['InfoBlock']))
            $model->attributes=$_GET['InfoBlock'];

        $this->render('iblock.views.admin.index',compact(
            'model'
        ));
    }

}