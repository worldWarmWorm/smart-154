<?php
use common\components\helpers\HYii as Y;
use common\components\helpers\HArray as A;
use common\components\helpers\HRequest as R;

class DefaultController extends AdminController
{
	public function accessRules()
    {
        return A::m([
            ['allow', 'users'=>['@'], 'actions'=>['index']]
        ], parent::accessRules());
    }

	public function filters()
	{
		return CMap::mergeArray(parent::filters(), array(
			'ajaxOnly +ymapSaveGeoObject +ymapBoundsChangeGeoObject +ymapRemoveGeoObject +saveMenuLimit'
		));
	}
    // *** AJAX actions *** ///

    /**
     * Apply new order of menu items
     * @return void
     */
    public function actionMenuOrder()
    {
        $items = Yii::app()->request->getParam('item');
        MenuHelper::getInstance()->reorder($items);

        Yii::app()->end();
    }
    
    public function actionSaveMenuLimit()
    {
    	$ajax=new AjaxHelper();
    	
    	$limit=Yii::app()->request->getParam('limit');
    	if(is_numeric($limit)) {
    		\Yii::app()->settings->set('cms_settings', 'menu_limit', $limit);
    		$ajax->success=true;
    		Y::cacheFlush();
    	}    	
    	$ajax->endFlush();
    }

    /**
     * Apply new order for images
     */
    public function actionImageOrder()
    {
        $orders = Yii::app()->request->getParam('image');

        $images = CImage::model()->findAllByPk($orders);

        foreach($images as $img) {
            $img->ordering = array_search($img->id, $orders) + 1;
            $img->save();
        }

        echo 'ok';
        Yii::app()->end();
    }

    public function actionShopOrder()
    {
    	$category_id = (int)Yii::app()->request->getParam('cat_id');
        $orders = Yii::app()->request->getParam('products');

        if(is_array($orders) && $category_id) {
	        array_walk($orders, function(&$v) { $v=(int)str_replace('item_', '', $v); });
	        $ids=implode(',',$orders);
	        $sql="SET @n:=0; UPDATE `product` SET `ordering`=@n:=@n+1 WHERE `id` IN ({$ids}) ORDER BY FIELD(`id`,{$ids})";
	        \Yii::app()->db->createCommand($sql)->execute();
	    }
        
        echo 'ok';
        Yii::app()->end();
    }

    public function actionSaveImageDesc()
    {
        if (Yii::app()->request->isAjaxRequest && count($_POST)) {
            $id   = (int) $_POST['id'];
            $desc = $_POST['desc'];

            $model = CImage::model()->findByPk($id);

            if ($model === null)
                throw new CHttpException(404, 'Изображение не найдено');

            $model->description = $desc;
            if ($model->save()) {
                echo 0;
            } else {
                echo 1;
            }

            Yii::app()->end();
        }

        $this->redirect('index');
    }

    /*
     * Action for removing images
     */
    public function actionRemoveImage($id)
    {
        $model = CImage::model()->findByPk($id);

        if ($model === null)
            throw new CHttpException(404, 'Изображение не найдено');

        $status = $model->delete() ? 'ok' : 'error';

        if (Yii::app()->request->isAjaxRequest) {
            echo $status;
            Yii::app()->end();
        } else
            $this->redirect(array('/admin/'.$model->model.'/update', 'id'=>$model->item_id));
    }

    // *** ------------ *** ///
    
    public function actionLogin()
    {
        if (!Yii::app()->user->isGuest)
            $this->redirect(array('index'));

        $this->layout = 'login';

        $model = new LoginForm;

        if (isset($_POST['LoginForm'])) {
            $model->attributes = $_POST['LoginForm'];

            if ($model->validate() && $model->login()){
                Yii::app()->user->setState('role', Yii::app()->user->role);
                $this->redirect(Yii::app()->user->getReturnUrl(array('/admin/default/index')));
            }
        }

        $this->pageTitle = 'Авторизация - '. $this->appName;
        $this->render('login',array('model'=>$model));
    }
    
    public function actionLogout()
    {
        Yii::app()->user->logout();
        $this->redirect($this->createUrl('index'));
    }

    public function actionIndex()
    {
        $this->render('index');
    }

    public function actionSettings()
    {
        $model = new SettingsForm(D::isDevMode() ? SettingsForm::DEV_SCENARIO : '');

        if (isset($_POST['SettingsForm'])) {
        	$model->loadSettings();
            $model->attributes = $_POST['SettingsForm'];

            if(isset($_POST['ajax'])) {
            	echo CActiveForm::validate($model);
            	Yii::app()->end();
            	die();
            }

            if ($model->validate()) {
                $model->saveSettings();
                
                \Yii::app()->cache->flush();

                $this->refresh();
            }
        }
        $model->loadSettings();
        
        $this->render('settings', compact('model'));
    }

    public function actionClearImageCache()
    {
        $images   = CImage::model()->findAll();
        $products = array();

        foreach($images as $id=>$img) {
            $img->removeTmb();

            if ($img->model == 'product') {
                $products[] = $img;
                unset($images[$id]);
            }
        }

        $resizer = new UploadHelper();
        $resizer->createThumbnails($images);

        $params = array('max'=>100, 'master_side'=>4);
        if ($cropTop = Yii::app()->settings->get('shop_settings', 'cropTop')) {
            $params['crop']=true;
            $params['cropt_top']=$cropTop;
        }
        $resizer = new UploadHelper();
        $resizer->createThumbnails($products, $params);

        if (Yii::app()->request->isAjaxRequest) {
            echo 'ok';
            Yii::app()->end();
        } else
            $this->redirect(array('default/settings'));
    }

    public function actionError()
    {
        if($error=Yii::app()->errorHandler->error)
        {
            if(Yii::app()->request->isAjaxRequest)
                echo $error['message'];
            else
                $this->render('error', $error);
        }

    }

    public function actionExtUpdate()
    {
        /*$data = array(
            'username'=>'admin',
            'password'=>'dish_1234',
            'version'=>'1.2.6'
        );*/

        $data = Yii::app()->request->getPost('authData', array());

        $model = new LoginForm;
        $model->attributes = $data;

        $result = array('error'=>false, 'text'=>'', 'version'=>'');

        if (!$model->validate()) {
            $result['error'] = true;
            $result['text']  = 'Неверный логин или пароль';
            echo json_encode($result);
            Yii::app()->end();
        }

        $updater = new CmsUpdate($data['version']);
        $updater->update();

        $result['text']    = $updater->cmdResult ? $updater->cmdResult : '';
        $result['version'] = CmsUpdate::version();
        echo json_encode($result);
        Yii::app()->end();
    }

    public function actionGisMapDialog()
    {
        $request = Yii::app()->request;

        if ($request->isAjaxRequest) {
            $desc       = $request->getPost('description');
            $coors      = $request->getPost('coors');
            $marker_id  = $request->getPost('marker_id');
            $balloon_id = $request->getPost('balloon_id');

            $data = CJSON::encode(array(
                'coors'=>$coors,
                'desc'=>$desc
            ));

            Yii::app()->settings->set('markers', $coors, $data);

            echo json_encode(array(
                'result'=>'ok',
                'text'=>$desc,
                'marker_id'=>$marker_id,
                'balloon_id'=>$balloon_id
            ));
            Yii::app()->end();
        }

        $markers = Yii::app()->settings->get('markers');
        $params  = Yii::app()->settings->get('mapParams') or array();

        if ($markers) {
            $markers = array_values($markers);
            foreach($markers as $id=>$m) {
                $markers[$id] = json_decode($m);
            }
        } else {
            $markers = array();
        }

        $this->layout = 'clear';
        $this->pageTitle = 'Карта 2Гис';
        $assets = Yii::app()->assetManager->getPublishedUrl(Yii::getPathOfAlias('admin.widget.CmsEditor.assets'));

        $this->render('gismapdialog', compact('assets', 'markers', 'params'));
    }

    public function actionSaveMapParams()
    {
        $params = Yii::app()->request->getPost('mapParams');

        Yii::app()->settings->set('mapParams', $params);
        echo 'ok';
        Yii::app()->end();
    }

    public function actionGisMapRemoveMarker()
    {
        $coors = Yii::app()->request->getPost('coors');

        Yii::app()->settings->delete('markers', $coors);
        echo 'ok';
        Yii::app()->end();
    }
    
    public function actionYmapDialog()
    {
    	$this->layout = 'clear';
    	$this->pageTitle = 'Яндекс.Карта';
    	
    	$geoObjects=Yii::app()->settings->get('ymaps_geo_objects') or array();
    	$ymapBounds=Yii::app()->settings->get('ymaps_bounds') or array(
    		'zoom'=>16,
    		'center'=>array('55.04902279997517', '82.91542373974723'),
    		'globalPixelCenter'=>array('12252746.816147964', '5317285.322633277')
    	);
    	
    	$this->render('ymapdialog', compact('geoObjects', 'ymapBounds'));
    }
    
    /**
     * Сохранение параметров геообъекта для Яндекс.Карты.
     */
    public function actionYmapSaveGeoObject()
    {
    	$hash=A::get($_POST, 'hash');
    	if($hash) {
    		$ymapsGeoObjects=Yii::app()->settings->get('ymaps_geo_objects') or array();
    		$ymapsGeoObjects[$hash]=array(
    			'x'=>A::get($_POST, 'x'),
    			'y'=>A::get($_POST, 'y'),
    			'balloonContentHeader'=>A::get($_POST, 'balloonContentHeader'),
    			'balloonContentBody'=>A::get($_POST, 'balloonContentBody'),
    			'balloonContentFooter'=>A::get($_POST, 'balloonContentFooter')
    		);
    		Yii::app()->settings->set('ymaps_geo_objects', $ymapsGeoObjects);
    	}
    	
    	Yii::app()->end();
    	die;
    } 
    /**
     * Удаление геообъекта для Яндекс.Карты.
     */
    public function actionYmapRemoveGeoObject()
    {
    	$hash=A::get($_POST, 'hash');
    	if($hash) {
    		$ymapsGeoObjects=Yii::app()->settings->get('ymaps_geo_objects') or array();
    		Yii::app()->settings->delete('ymaps_geo_objects', $hash);
    	}
    	 
    	Yii::app()->end();
    	die;
    }
    
    /**
     * Сохранение основных настроек Яндекс.Карты.
     */
    public function actionYmapBoundsChangeGeoObject()
    {
    	Yii::app()->settings->set('ymaps_bounds', array(
    		'zoom'=>A::get($_POST, 'zoom'),
    		'center'=>A::get($_POST, 'center'),
    		'globalPixelCenter'=>A::get($_POST, 'globalPixelCenter')
    	));
    	Yii::app()->end();
    	die;
    }

    public function actionCheckDb()
    {
        $dbUpdater = new CmsDbUpdate();

        if (Yii::app()->request->isAjaxRequest) {
            $dbUpdater->update(true);
            Yii::app()->end();
        }

        $query_list = $dbUpdater->update()->queryList();
        $this->render('check_db', compact('query_list'));
    }

    public function actionClearCache()
    {
    	\Yii::app()->cache->flush();
    	\Yii::app()->user->setFlash(Y::FLASH_SYSTEM_SUCCESS, 'Файлы кэша были успешно удалены');
    	$this->redirect('settings');
    }

    public function actionDeleteSettingFile()
    {
        if($attribute = Yii::app()->request->getPost('attribute')) {
            $filename = D::cms($attribute);

            $delete = Yii::getPathOfAlias('webroot') . Yii::app()->params['uploadSettingsPath'] . DS . $filename;
            if(file_exists($delete)) unlink($delete);
        }
    }
}
