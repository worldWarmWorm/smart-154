<?php
/**
 * Created by JetBrains PhpStorm.
 * User: Rick
 * Date: 18.01.12
 * Time: 14:52
 * To change this template use File | Settings | File Templates.
 */
use common\components\helpers\HArray as A;

class galleryController extends AdminController
{
	/**
	 * (non-PHPdoc)
	 * @see AdminController::filters()
	 */
	public function filters()
	{
		return A::m(parent::filters(), [
			['DModuleFilter', 'name'=>'gallery'],
			'ajaxOnly +changePublished'				
		]);
	}
	
	/**
	 * (non-PHPdoc)
	 * @see \CController::actions()
	 */
	public function actions()
	{
		return A::m(parent::actions(), [
			'changePublished'=>[
				'class'=>'\common\ext\active\actions\AjaxChangeActive',
				'className'=>'\Gallery',
				'behaviorName'=>'publishedBehavior'
			]
		]);
	}
	
    public function actionDeleteGallery($id){
        $model = Gallery::model()->findByPk((int)$id); 
        $images = GalleryImg::model()->findAll(array('condition'=>'gallery_id =' . (int)$id));

        if(count($model)>0){
            if($model->delete()){
                foreach($images as $image){
                    $img = 'images/gallery/' . $image->image;
                    $img_tmb = 'images/gallery/' . 'tmb_' . $image->image;
                    if(is_file($img) && is_file($img_tmb)){
                        unlink($img);
                        unlink($img_tmb);
                        $image->delete();
                    }
                }
            }
        }
    }

    public function actionUpdateGallery($id)
    {   
        $model = Gallery::model()->findByPk((int)$id);

        if (isset($_POST['Gallery'])) {
            $model->attributes = $_POST['Gallery'];

            if ($model->save()){
                $this->redirect(array('gallery/index', 'id'=>$model->id));
            }
        }

        $this->render('gallery/update', compact('model'));
    }

    public function actionCreateGallery()
    {   
        $model = new Gallery();

        if (isset($_POST['Gallery'])) {
			$model->attributes = $_POST['Gallery'];

			if ($model->save()){
				$this->redirect(array('gallery/images', 'id'=>$model->id));
            }
		}

		$this->render('gallery/create', compact('model'));
    }

    public function actionOrderAlbums(){
        $items = Yii::app()->request->getQuery('items');
        if (isset($items) && is_array($items)) {
            $i = 0;
            foreach ($items as $item) {
                $image = Gallery::model()->findByPk((int)$item);
                $image->ordering = $i;
                $image->save();
                $i++;
            }
        }    
    }

    public function actionOrderAlbumImages(){
        $items = Yii::app()->request->getQuery('sort');
        //$album_id = Yii::app()->request->getQuery('album_id');
        if (isset($items) && is_array($items)) {
            $i = 0;
            foreach ($items as $item) {
                $image = GalleryImg::model()->findByPk((int)$item);
                $image->image_order = $i;
                $image->save();
                $i++;
            }
        }
    }

    public function actionUpdateDescription(){
        $image_id = Yii::app()->request->getQuery('image_id');
        $model = GalleryImg::model()->findByPk($image_id);
        if(count($model)){
            $model->description = Yii::app()->request->getQuery('image_desc_text');
            if($model->save()){
                echo 'Cохранено.';
                Yii::app()->end();
            }
        }
        echo 'При сохранении произошли ошибки!';
        Yii::app()->end();
    }

    public function actionDeleteImage(){
        $image_id = Yii::app()->request->getQuery('image_id');
        $imagesForDelete = GalleryImg::model()->findByPk((int)$image_id);
        if(count($imagesForDelete)) {
            $img = 'images/gallery/' . $imagesForDelete->image;
            $img_tmb = 'images/gallery/' . 'tmb_' . $imagesForDelete->image;
            if($imagesForDelete->delete()){
                if(is_file($img) && is_file($img_tmb)){
                    unlink($img);
                    unlink($img_tmb);
                }
                echo 'Успешно удалено!';
                Yii::app()->end();
            }
        }
        echo 'При удалении произошли ошибки';
        Yii::app()->end();
    }

    //Загрузка и обрезка изображений
    public function actionUpload()
    {   
        $img_path = 'images/gallery/';
        $album_id = (int)$_POST['id'];

        //Заводим новую модель
        $model = new GalleryImg;
        $model->gallery_id = $album_id;
        $result_data = array();

        //Получаем файл из $_FILES
        $model->files = CUploadedFile::getInstanceByName('files[0]');

        //Проводим валидацию файла, если всё ок идём дальше. Если нет пишем ошибку.
        if($model->validate()){

            //Получаем расширение.
            $ext = $model->files->getExtensionName();
            //Получаем число, для имени картинки
            $rand = substr(md5(microtime()),rand(0,26),12);

            //Формируем новое имя картинки.
            $model->image = $rand.'.'.$ext;

            $result_data['img'] = $img_path . $rand . '.' . $ext;
            $result_data['error'] = 0;
            $result_data['filename'] = $rand . '.' . $ext;

            //Сохраняем картинку (оригинал).
            $save_img_result = $model->files->saveAs( $result_data['img'] );

            if ($save_img_result) {
                /** @var Image $img */
                $img = Yii::app()->image->load($result_data['img']);
                
                $w = $img->width;
                $h = $img->height;

                //Масштабируем и пересохранияем оригинал
                if ($w > GalleryImg::MAX_LINEAR_SIZE || $h > GalleryImg::MAX_LINEAR_SIZE){
                    $masterSize = $w > $h ? Image::WIDTH : Image::HEIGHT;
                    $img->resize(GalleryImg::MAX_LINEAR_SIZE, GalleryImg::MAX_LINEAR_SIZE, $masterSize);
                    $img->save($result_data['img']);
                    $img = Yii::app()->image->load($result_data['img']);
                }

                $masterSize = $w > $h ? Image::HEIGHT : Image::WIDTH;



                //Посылаем картинку на обрезку
                $img->resize(361, 211, $masterSize);
                $img->crop(360, 210, 1);

                //После манипуляций сохраняем превьюху
                $img->save($img_path . 'tmb_' . $rand . '.' . $ext);
                $result_data['img'] = $img_path . 'tmb_' . $rand . '.' . $ext;

                $img->resize(281, 161, $masterSize);
                $img->crop(280, 160, 1);
                $img->save($img_path . 'main_tmb_' . $rand . '.' . $ext);
            }

            //Сохраняем и отправляем назад.
            $model->save();
            $result_data['image_id'] = $model->id;
            echo json_encode($result_data);
        }
        else{
            if(isset($model->getErrors()['files'])){
                $result_data['error'] = 1;
                $result_data['errors'] = $model->getErrors()['files'];
                echo json_encode($result_data);//['files'][0];  
            }  
        }
    }

    //Отобржение изображений
    public function actionImages($id)
    {
        $this->render('gallery_image/index');
    }

    // Отображение альбомов
    public function actionIndex() {
        $albums = new CActiveDataProvider('Gallery',
            array(
                    'criteria'=>array(
                        'order'=>'ordering ASC, id DESC',
                    )));
        $this->render('index', compact('albums'));
    }


    //Обновляет привью альбома.
    public function actionUpdateAlbumPreview(){
        $image_id = Yii::app()->request->getQuery('image_id');
        $album_id = Yii::app()->request->getQuery('album_id');

        if(isset($image_id) && isset($album_id)) {
            $image = GalleryImg::model()->findByPk((int)$image_id)->image;
            $gallery_preview = Gallery::model()->findByPk((int)$album_id);
            $gallery_preview->preview_id = 'tmb_' . $image;
            $gallery_preview->save();
        }
    }

   	public function getGalleryHomeTitle()
	{
		return D::cms('gallery_title') ?: \Yii::t('AdminModule.gallery', 'title');
	}
}
