<?php

class m140101_002400_create_order_customer_fields_table extends CDbMigration
{
	private function _tableName()
	{
		return 'order_customer_fields';
	}

	public function safeUp()
	{
		if(!$this->getDbConnection()->createCommand("SHOW TABLES LIKE '".$this->_tableName()."'")->query()->rowCount) {
		$this->createTable($this->_tableName(), [
			'id' => 'pk',
			'name' => 'varchar(25)',
			'label' => 'varchar(100)',
			'placeholder' => 'varchar(50)',
			'type' => 'varchar(25)',
			'required' => 'boolean DEFAULT 0',
			'sort' => 'tinyint(2)',
			'default_value' => 'varchar(255)',
			'values' => 'text',
			'mask' => 'varchar(50) NULL',
		]);
		
		$this->insertMultiple($this->_tableName(), [
			['name'=>'name', 'label'=>'Ваше имя', 'type'=> 'text',	'required' => 1,'sort' =>1],
			['name'=>'email', 'label'=>'E-mail', 'type'=> 'email',	'required' => 0,'sort' =>2],
			['name'=>'phone', 'label'=>'Телефон', 'type'=> 'phone', 'required' => 1,'sort' =>3],
			['name'=>'address', 'label'=>'Адрес доставки', 'type'=> 'textarea', 'required' => 0,'sort' =>4],
			['name'=>'comment', 'label'=>'Комментарий к заказу', 'type'=> 'textarea', 'required' => 0,'sort' =>5],
		]);
		}
	}

	public function safeDown()
	{
		echo "m140101_002400_create_order_customer_fields_table does not support migration down.\n";
	}
}
