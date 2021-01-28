<?php
/**
 * Поведение для модели DActiveRecord: атрибут файла изображения.
 *
 */
namespace ext\D\image\components\behaviors;

class ImageBehavior extends \CBehavior
{
	public $attribute;
	
	public $attributeId='id';
	
	public $attributeEnable;
	
	public $attributeFile='imagefile';
	
	public $attributeLabel;
	
	public $attributeEnableLabel;
	
	public $attributeFileLabel;
	
	public $tmbHeight=100;
	
	public $tmbWidth=100;
	
	public $srcPath;
	
	public $prefix='';
	
	public function events()
	{
		return array(
			'onBeforeSave'=>'beforeSave',
			'onAfterDelete'=>'afterDelete'
		);
	}
	
	public function attach($owner)
	{
		$owner->addDynamicAttribute($this->attributeFile);
		
		parent::attach($owner);
		
		if($this->srcPath === null) 
			$this->srcPath='/images/'.$this->_resolveOwner();
		
		$t=\YiiHelper::createT('ext\D\image\components\behaviors\ImageBehavior.image');
		if($this->attributeLabel === null)
			$this->attributeLabel=$t('attributeLabel');
		if($this->attributeEnableLabel === null)
			$this->attributeEnableLabel=$t('attributeEnableLabel');
		
	}
	
	public function rules()
	{
		return array(
			array($this->attribute, 'length', 'max'=>32),
			array($this->attributeFile, 'DImageValidator', 'allowEmpty'=>true, 'types'=>'jpg,jpeg,gif,png', 'maxSize'=>10485760),
			array($this->attribute.','.$this->attributeEnable, 'safe')
		);
	}
	
	public function attributeLabels()
	{
		return array(
			$this->attribute=>$this->attributeLabel,
			$this->attributeEnable=>$this->attributeEnableLabel,
			$this->attributeFile=>$this->attributeFileLabel ?: $this->attributeLabel,
		);
	}
	
	public function hasEnabled()
	{
		return ($this->attributeEnable !== null);
	}
	
	public function isEnabled()
	{
		return ($this->attributeEnable == 1);
	}
	
	public function getPath()
	{
		return $this->owner->{$this->attribute} ? ($this->getDirPath() . DS . $this->owner->{$this->attribute}) : null;
	}
	
	public function getSrc()
	{
		return is_file($this->getPath()) ? ($this->srcPath . '/' . $this->owner->{$this->attribute}) : null;
	}
	
	public function getDirPath()
	{
		return \Yii::getPathOfAlias('webroot') . str_replace('/', DS, $this->srcPath);
	}
	
	public function getFilenameWithoutExt()
	{
		$id=$this->owner->{$this->attributeId};
		return "{$this->prefix}{$id}_" . \HashHelper::generateHash($this->_resolveOwner() . $this->attribute . $id, 12);
	}
	
	public function beforeSave()
	{
		$file=\CUploadedFile::getInstance($this->owner, $this->attributeFile);
		if(!empty($file)) {
			$this->owner->{$this->attribute} = $this->getFilenameWithoutExt().'.'.$file->getExtensionName();
			// удаляем предыдущий файл
			$this->delete();
			
			if(!is_dir(dirname($this->getPath()))) 
				mkdir(dirname($this->getPath()), '0755', true);
			
			$file->saveAs($this->getPath());
			// генерим мини-изображение, только если это не GIF.
			if($file->type != 'image/gif') {
				$img = \Yii::app()->image->load($this->getPath());
				if(($img->width > $this->tmbWidth) || ($img->height > $this->tmbHeight)) {
					$p=$this->tmbWidth / $this->tmbHeight;
					$pImage=$img->width / $img->height;
					// если $pImage > $p значит высота изображения в пропорции будет меньше нужной,
					// масштабируем по высоте, иначе по ширине
					$master=($pImage > $p) ? \Image::HEIGHT : \Image::WIDTH;
					$img->resize($this->tmbWidth, $this->tmbHeight, $master)
						->crop($this->tmbWidth, $this->tmbHeight)
						->save();
				}
			}
		}
		
		return true;
	}
	
	public function afterDelete()
	{
		$this->delete();
		
		return true;
	}
	
	public function delete()
	{
		$files=glob($this->getDirPath() . DS . $this->getFilenameWithoutExt().'.*');
		if(is_array($files)) {
			array_map('unlink', $files);
		}
	}
	
	private function _resolveOwner()
	{
		return strtolower(trim(str_replace('\\', '_', get_class($this->owner)), '\\'));
	}
}
