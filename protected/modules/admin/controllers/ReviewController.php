<?php

    class ReviewController extends AdminController
    {
    	/**
    	 * (non-PHPdoc)
    	 * @see AdminController::filters()
    	 */
    	public function filters()
    	{
    		return CMap::mergeArray(parent::filters(), array(
    			array('DModuleFilter', 'name'=>'shop')
    		));
    	}

        public function actionAjax(){
            $action = Yii::app()->request->getPost('action');
            $item = Yii::app()->request->getPost('item');
            $return = array();
            if($action !== null && $item !== null) {

                if ($action == 'publish') {
                    $model = ProductReview::model()->findByPk((int)$item);
                    $model->published = (int)!(bool)$model->published;
                    $model->save();
                    $return = array("status" => $model->published,'count'=>ProductReview::model()->unpublished()->count());
                }

            } else {
                $return = array("status" => "request not valid");
            }
            echo CJSON::encode($return);
        }

        /**
         * Displays a particular model.
         * @param integer $id the ID of the model to be displayed
         */
        public function actionView($id)
        {
            $this->render('view',array(
                'model'=>$this->loadModel($id),
            ));
        }

        /**
         * Creates a new model.
         * If creation is successful, the browser will be redirected to the 'view' page.
         */
        public function actionCreate()
        {
            $model=new ProductReview;

            // Uncomment the following line if AJAX validation is needed
            // $this->performAjaxValidation($model);

            if(isset($_POST['ProductReview']))
            {
                $model->attributes=$_POST['ProductReview'];
                if($model->save())
                    $this->redirect(array('view','id'=>$model->id));
            }

            $this->render('create',array(
                'model'=>$model,
            ));
        }

        /**
         * Updates a particular model.
         * If update is successful, the browser will be redirected to the 'view' page.
         * @param integer $id the ID of the model to be updated
         */
        public function actionUpdate($id)
        {
            $model=$this->loadModel($id);

            // Uncomment the following line if AJAX validation is needed
            // $this->performAjaxValidation($model);

            if(isset($_POST['ProductReview']))
            {
                $model->attributes=$_POST['ProductReview'];
                if($model->save())
                    $this->redirect(array('view','id'=>$model->id));
            }

            $this->render('update',array(
                'model'=>$model,
            ));
        }

        /**
         * Deletes a particular model.
         * If deletion is successful, the browser will be redirected to the 'admin' page.
         * @param integer $id the ID of the model to be deleted
         */
        public function actionDelete($id)
        {
        	if(!\Yii::app()->request->isAjaxRequest) 
        		throw new \CHttpException(403);

            $this->loadModel($id)->delete();

            \Yii::app()->end();
        }

        /**
         * Lists all models.
         */
        public function actionIndex()
        {
            $c = new CDbCriteria();
            $c->order = "id DESC";
            $count = ProductReview::model()->count($c);
            $pages = new CPagination($count);
            $pages->pageSize = 30;
            $pages->applyLimit($c);

            $model = ProductReview::model()->findAll($c);

            $this->pageTitle = 'Отзывы - '.$this->appName;

            $this->render('index', compact('model', 'pages'));
        }

        /**
         * Manages all models.
         */
        public function actionAdmin()
        {
            $model=new ProductReview('search');
            $model->unsetAttributes();  // clear any default values
            if(isset($_GET['ProductReview']))
                $model->attributes=$_GET['ProductReview'];

            $this->render('admin',array(
                'model'=>$model,
            ));
        }

        /**
         * Returns the data model based on the primary key given in the GET variable.
         * If the data model is not found, an HTTP exception will be raised.
         * @param integer $id the ID of the model to be loaded
         * @return ProductReview the loaded model
         * @throws CHttpException
         */
        public function loadModel($id)
        {
            $model=ProductReview::model()->findByPk($id);
            if($model===null)
                throw new CHttpException(404,'The requested page does not exist.');
            return $model;
        }

        /**
         * Performs the AJAX validation.
         * @param ProductReview $model the model to be validated
         */
        protected function performAjaxValidation($model)
        {
            if(isset($_POST['ajax']) && $_POST['ajax']==='product-review-form')
            {
                echo CActiveForm::validate($model);
                Yii::app()->end();
            }
        }
    }