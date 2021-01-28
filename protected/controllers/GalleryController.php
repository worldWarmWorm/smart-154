<?php
/**
 * Фотогалерея
 */
class GalleryController extends Controller
{
	/**
	 * (non-PHPdoc)
	 * @see CController::filters()
	 */
	public function filters()
	{
		return CMap::mergeArray(parent::filters(), array(
			array('DModuleFilter', 'name'=>'gallery')
		));
	}
	
	/**
	 * Action: главная страница
	 */
    public function actionIndex()
    {
    	// return $this->actionAlbums();    	
    	$albums = Gallery::model()->published()->findAll(array('order'=>'ordering ASC, id DESC'));
    	
        $this->prepareSeo($this->getGalleryHomeTitle());    	
    	$this->breadcrumbs->add($this->getGalleryHomeTitle());
    	
    	$this->render('index', compact('albums'));
    }

    /**
     * Action: страница альбома
     * @param integer $id идентификатор альбома.
     */
    public function actionAlbum($id)
    {
        $album = $this->loadModel('\Gallery', $id, true, ['scopes'=>'published']);
        
        $this->prepareSeo('Альбом - '.$album->title);
    	
    	$this->breadcrumbs->add($this->getGalleryHomeTitle(), '/gallery');
    	$this->breadcrumbs->add($album->title);
    	
    	$this->render('album', compact('album'));
    }
    
    /**
     * Action: список всех альбомов.
     */
    public function actionAlbums()
    {
    	$albums = Gallery::model()->published()->findAll(array('order'=>'ordering ASC'));
    	
    	$this->prepareSeo($this->getGalleryHomeTitle());
    	$this->breadcrumbs->add($this->getGalleryHomeTitle());
    	 
    	$this->render('albums', compact('albums'));
    }
    
    /**
     * Заголовок модуля Фотогалерея.
     * @return string
     */
    public function getGalleryHomeTitle()
    {
    	return D::cms('gallery_title',Yii::t('gallery','gallery_title'));
    }
}
