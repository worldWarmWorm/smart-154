<?php
/**
 * Модель Заказ
 */
namespace ecommerce\modules\order\models;

use common\components\helpers\HArray as A;

class Order extends \DOrder\models\DOrder
{
    public $filter_create_time_from;    
    public $filter_create_time_to;
    
    public function rules()
    {
        return A::m(parent::rules(), [
            ['filter_create_time_from, filter_create_time_to, hash', 'safe', 'on'=>'search']
        ]);
    }
    
	public function search()
	{
		// @todo Please modify the following code to remove attributes that should not be searched.
	
		$criteria=new \CDbCriteria;
	
		$criteria->compare('id',$this->id, true);
		$criteria->compare('hash',$this->hash, true);
		
        //$criteria->compare('customer_data',$this->customer_data,true);
		//$criteria->compare('order_data',$this->order_data,true);
		//$criteria->compare('comment',$this->comment,true);
        
        if($this->filter_create_time_from && preg_match('/^(\d{2})\.(\d{2})\.(\d{4})$/', $this->filter_create_time_from, $m)) {
            $criteria->addCondition('create_time >=:createTimeFrom');
            $criteria->params['createTimeFrom']="{$m[3]}-{$m[2]}-{$m[1]}";
        }
        if($this->filter_create_time_to && preg_match('/^(\d{2})\.(\d{2})\.(\d{4})$/', $this->filter_create_time_to, $m)) {
            $criteria->addCondition('create_time <=:createTimeTo');
            $criteria->params['createTimeTo']="{$m[3]}-{$m[2]}-{$m[1]}";
        }
		
        $criteria->compare('completed',$this->completed);
        $criteria->compare('paid',$this->paid);
	
		return new \CActiveDataProvider($this, array(
            'criteria'=>$criteria,
            'pagination'=>[
                'pageVar'=>'p'
            ],
            'sort'=>[
                'sortVar'=>'s',
                'defaultOrder'=>'`create_time` DESC'
            ]
		));
	}
}
