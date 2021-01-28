<?php

/**
 * This is the model class for table "image".
 *
 * The followings are the available columns in table 'image':
 * @property integer $id
 * @property string $model
 * @property integer $item_id
 * @property string $filename
 * @property string $description
 * @property integer $ordering
 */
class CImage extends CActiveRecord
{
	/**
     * @static
     * @param $className
     * @return CActiveRecord
     */
	public static function model($className=__CLASS__)
	{
		return parent::model($className);
	}

	/**
	 * @return string the associated database table name
	 */
	public function tableName()
	{
		return 'image';
	}

	/**
	 * @return array validation rules for model attributes.
	 */
	public function rules()
	{
		// NOTE: you should only define rules for those attributes that
		// will receive user inputs.
		return array(
			array('model, item_id, filename', 'required'),
			array('item_id', 'numerical', 'integerOnly'=>true),
			array('model', 'length', 'max'=>255),
			array('filename', 'length', 'max'=>100),
			array('description', 'length', 'max'=>500),
            array('ordering', 'safe')
		);
	}

	/**
	 * @return array relational rules.
	 */
	public function relations()
	{
		// NOTE: you may need to adjust the relation name and the related
		// class name for the relations automatically generated below.
		return array(
		);
	}

	/**
	 * @return array customized attribute labels (name=>label)
	 */
	public function attributeLabels()
	{
		return array(
			'id' => 'ID',
			'model' => 'Модель',
			'item_id'=>'Id записи',
			'filename'=>'Имя файла',
			'description'=>'Описание',
            'ordering'=>'Порядок'
		);
	}

    protected function afterDelete()
    {
        $path = YiiBase::getPathOfAlias('webroot').DS.'images'.DS.$this->model;

        $files = array($this->filename, 'tmb_'.$this->filename);

        foreach($files as $f) {
            if (!is_file($path .DS. $f) && defined('YII_DEBUG')) {
                throw new CException('Файл не найден');
            }

            if (is_file($path .DS. $f)) {
                unlink($path .DS. $f);
            }
        }

        return true;
    }

    public function getUrl()
    {
        return '/images/'.$this->model.'/'.$this->filename;
    }

    public function getTmbUrl($regenerate=false)
    {
        $path = YiiBase::getPathOfAlias('webroot') .DS. 'images' .DS. $this->model;

		$filename=$path .DS. $this->filename;
		$tmb=$path .DS. 'tmb_'.$this->filename;
        if (is_file($filename) && ($regenerate || !is_file($tmb))) {
            if(in_array(exif_imagetype($filename), [IMAGETYPE_PNG, IMAGETYPE_JPEG])) {
	            $upload = new UploadHelper;
    	        $upload->createThumbnails([
        	        (object) ['path'=>$path, 'filename'=>$this->filename]
            	]);
			}
        }

        return '/images/'.$this->model.'/' . (is_file($tmb) ? 'tmb_' : '') . $this->filename;
    }

    public function removeTmb()
    {
        $file = YiiBase::getPathOfAlias('webroot') .DS. 'images' .DS. $this->model .DS. 'tmb_'.$this->filename;

        if (is_file($file))
            return unlink($file) ? true : false;

        return false;
    }

    public function getPath($full = true)
    {
        $base = YiiBase::getPathOfAlias('webroot') .DS. 'images';
        return $full ? $base .DS. $this->model : $base;
    }

    private function urlToPath($url) {
        return $_SERVER['DOCUMENT_ROOT'].$url;
    }

    public function getWidth($image) {
    	$width=null;
    	$filename=$this->urlToPath($image);

    	if(is_file($filename) && exif_imagetype($filename)) {
	        $imageObject = Yii::app()->image->load($filename);
	        $width=$imageObject->width;
	    }

        return $width;
    }

    public function getHeight($image) {
    	$height=null;
    	$filename=$this->urlToPath($image);

    	if(is_file($filename) && exif_imagetype($filename)) {
	        $imageObject = Yii::app()->image->load($filename);
	        $height=$imageObject->height;
    	}

        return $height;
    }
}
