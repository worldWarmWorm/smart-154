<?php
namespace reviews\models;

use common\components\helpers\HYii as Y;
use common\components\helpers\HArray as A;
use common\components\helpers\HHtml;
use reviews\models\Settings;

class Review extends \common\components\base\ActiveRecord
{
	/**
	 * @var int|NULL запись имеет детальный текст.
	 */
	public $has_detail_text=null;
	public $privacy_policy;

	/**
	 * (non-PHPdoc)
	 * @see \CActiveRecord::tableName()
	 */
	public function tableName()
	{
		return 'reviews';
	}
	
	/**
	 * (non-PHPdoc)
	 * @see \CModel::behaviors()
	 */
	public function behaviors()
	{
		$t=Y::ct('ReviewsModule.models/review');
		
		return A::m(parent::behaviors(), [
			'activeBehavior'=>[
				'class'=>'\common\ext\active\behaviors\ActiveBehavior',
				'attribute'=>'published',
				'attributeLabel'=>$t('label.published')
			],
			'aliasBehavior'=>['class'=>'\DAliasBehavior'],
			'metaBehavior'=>['class'=>'\MetadataBehavior'],
			'imageBehavior'=>[
				'class'=>'\ext\D\image\components\behaviors\ImageBehavior',
				'attribute'=>'image',
				'attributeLabel'=>$t('label.image'),
 				'attributeEnable'=>'image_enable',
 				'attributeEnableLabel'=>$t('label.imageEnable'),
 				'tmbWidth'=>Settings::model()->tmb_width,
 				'tmbHeight'=>Settings::model()->tmb_height
			]	
		]);
	}
	
	/**
	 * (non-PHPdoc)
	 * @see \DActiveRecord::rules()
	 */
	public function rules()
	{
		return $this->getRules([
			['author, detail_text', 'required', 'on'=>'frontend_insert'],
			['preview_text', 'required', 'except'=>'frontend_insert'],
			['author', 'length', 'max'=>255],
			['detail_text, publish_date, comment', 'safe'],
			['privacy_policy', 'required'],
			['privacy_policy', 'safe'],
		]);
	}
	
	public function requiredPrivacyPolicy($attribute)
	{
		if($this->$attribute != 1) {
			$this->addError($attribute, 'Вы не подтвердили свое согласие');
		}
	}
	
	/**
	 * {@inheritDoc}
	 * @see DActiveRecord::relations()
	 */
	public function relations()
	{
		return $this->getRelations([
				
		]);
	}
	
	/**
	 * (non-PHPdoc)
	 * @see \DActiveRecord::scopes()
	 */
	public function scopes()
	{
		return $this->getScopes([
			'hasDetailTextColumn'=>[
				'select'=>'IF(LENGTH(`t`.`detail_text`) > 0, 1, 0) AS `has_detail_text`'
			],
			'listingColumns'=>[
				'select'=>'`t`.*, NULL AS `detail_text`'
			],
			'byCreateDateDesc'=>[
				'order'=>'`create_time` DESC'
			],
			'byPublishDateDesc'=>[
				'order'=>'`publish_date` DESC'
			]
		]);
	}
	
	/**
	 * (non-PHPdoc)
	 * @see \DActiveRecord::attributeLabels()
	 */
	public function attributeLabels()
	{
		$t=Y::ct('ReviewsModule.models/review');
		return $this->getAttributeLabels([
			'author'=>$t('label.author'),
			'preview_text'=>$t('label.preview_text'),
			'detail_text'=>$t('label.detail_text'),
			'publish_date'=>$t('label.publish_date'),
			'create_time'=>$t('label.create_time'),
			'comment'=>$t('label.comment'),
			'privacy_policy'=>'Подтверждаю свое согласие с ' . \CHtml::link('Политикой обработки данных', '/privacy-policy', ['target'=>'_blank']),
		]);
	}

	/**
	 * (non-PHPdoc)
	 * @see \CActiveRecord::beforeSave()
	 */
	public function beforeSave()
	{
		if(($this->scenario=='frontend_insert') && !empty(Settings::model()->auto_generate_preview_text)) {
			$this->detail_text = \CHtml::encode($this->detail_text);
			$this->author = \CHtml::encode($this->author);
			$this->preview_text=HHtml::getIntro($this->detail_text, Settings::model()->preview_text_length);
		}

		if($this->isNewRecord) {
            $this->publish_date=new \CDbExpression('NOW()');
        }

		return parent::beforeSave();
	}
	
	/**
	 * (non-PHPdoc)
	 * @see \CActiveRecord::afterDelete()
	 */
	public function afterDelete()
	{
		parent::afterDelete();
		
		$params=[
			'model'=>strtolower(\CHtml::modelName($this)),
			'item_id'=>$this->id
		];		
		$items=array_merge(
			\CImage::model()->findAllByAttributes($params),
			\File::model()->findAllByAttributes($params)
		);		
		foreach($items as $item) {
			$item->delete();
		}
		
		return true;
	}
}
