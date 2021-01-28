<?php
/**
 * Created by PhpStorm.
 * User: Sergey
 * Date: 05.06.2017
 * Time: 9:43
 */

namespace iblock\components;
use iblock\models\InfoBlockElement as IBE;
use iblock\models\InfoBlockElement;
use iblock\models\InfoBlockProp;
use iblock\models\InfoBlock;

//todo:: need optimisation
class InfoBlockHelper
{
	/**
	 * Получить инфоблок по идентфикатору
	 * @param integer $id идентифкатор инфорблока
	 * @param boolean $onlyActive возвращать только активные.
	 * @return CActiveRecord
	 */
	public static function getIblockByPk($id, $onlyActive=false)
	{
		$criteria='';
		if($onlyActive) {
			$criteria=['condition'=>'active=1'];
		}
		
		return InfoBlock::model()->findByPk($id, $criteria);
	}

    /**
     * возвращает ассоциативный массив элемента со свойствами.
     * @param int $id
     * @param bool $onlyActive
     * @return array
     */
    public static function getElementByPk($id, $onlyActive = false)
    {
        $f_a = [
            'id' => $id
        ];
        if ($onlyActive) {
            $f_a['active'] = 1;
        }
        if($element = InfoBlockElement::model()->findByAttributes($f_a)) {
	        $elementItem = $element->attributes;
	        $elementItem['model']=$element;
	        $elementItem['properties'] = static::getElementProperties($element);
	
	        return $elementItem;
        }
        
        return false;
    }

    /**
     * возвращает ассоциативный массив свойств элемента.
     * @param int|InfoBlockElement $element
     * @return array
     */
    public static function getElementProperties($element)
    {
        if (is_int($element)) {
            $element = IBE::model()->findByPk($element);
        }
        $res = [];
        $element->load_fields('');

        //TODO: Придумать как вывести полный путь до файла/изображения
        $elementBehaviorImagePath = '/images/iblock_models_infoblockelement/';
        $elementBehaviorFilePath = '/files/iblock_models_infoblockelement/';

        foreach ($element->getFields() as $f) {
            if ($f['type'] == InfoBlockProp::TYPE_IMAGE) {
                $res[$f['name']] = $f['value'] ? $elementBehaviorImagePath . $f['value'] : '';
            } elseif($f['type'] == InfoBlockProp::TYPE_FILE) {
                $res[$f['name']] = $f['value'] ? $elementBehaviorFilePath . $f['value'] : '';
            } else {
                $res[$f['name']] = $f['value'];
            }
        }

        return $res;
    }

    /**
     * возвращает массив элементов по заданному ID инфоблока
     * по умолчанию активность элемента не проверяется и свойства элемента не запрашиваются
     * @param $infoBlockID
     * @param $with_prop - получать ли свойства для элементов
     * @param array $mergeCriteria – дополнительные критерия при выборке элементов.
     * P.S. Переделано под критерию CDbCriteria
     * @param array $default – возвращаемое значение если выборка пуста.
     * @return array|mixed
     */
    public static function getElements($infoBlockID, $mergeCriteria = [], $with_prop = true, $default = [])
    {
        $criteria = new \CDbCriteria();
        $criteria->addCondition('t.info_block_id = :info_block_id');
        $criteria->addCondition('t.active = 1');
        $criteria->order = 't.sort';

        $criteria->params[':info_block_id'] = $infoBlockID;

        if ($mergeCriteria) {
            $criteria->mergeWith($mergeCriteria);
        }

        $list = IBE::model()->findAll($criteria);

        if (empty($list)) {
            return $default;
        }

        $res = [];

        foreach ($list as $e) {
            $element = $e->attributes;
            $element['model']=$e;
            $element['preview'] = $e->imageBehavior->getSrc();

            if ($with_prop) {
                $element['props'] = static::getElementProperties($e);
            }

            $res[$e->id] = $element;
        }

        return $res;

    }

	/**
     * Получить список элементов в формате id=>title
     * @param integer $id идентификатор инфоблока
     * @param string $title имя атрибута для заголовка. 
     * Чтобы указать имя свойства необходимо указать префикс @.
     * @param array|\CDbCriteria $criteria дополнительный критерий выборки
     * @return array
     */
    public static function listData($id, $title, $criteria=['order'=>'`sort` ASC'])
    {
        $listData=[];

        $isProperty=(strpos($title, '@') === 0);
        $title=ltrim($title, '@');
        if($elements=static::getElements($id, $criteria, $isProperty)) {
            foreach($elements as $item) {
                if($isProperty) {
                    if(isset($item['props'][$title])) {
                        $listData[$item['id']]=$item['props'][$title];
                    }
                }
                else {
                    $listData[$item['id']]=$item[$title];
                }
            }
        }
        
        return $listData;
    }
}
