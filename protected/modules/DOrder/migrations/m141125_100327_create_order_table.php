<?php

class m141125_100327_create_order_table extends CDbMigration
{
	private function _tableName() 
	{
		return \Yii::app()->getModule('DOrder')->tableName;
	}
	
	public function up()
	{
		$this->createTable($this->_tableName(), array(
			'id' => 'pk',
			'customer_data' => 'LONGTEXT',
			'order_data' => 'LONGTEXT',
			'comment' => 'TEXT',
			'create_time' => 'TIMESTAMP',
			'completed' => 'boolean DEFAULT 0',
			'paid' => 'boolean DEFAULT 0',
			'hash' => 'string'
		));
	}

	public function down()
	{
		$this->dropTable($this->_tableName());
		return true;
	}

	/*
	// Use safeUp/safeDown to do migration with transaction
	public function safeUp()
	{
	}

	public function safeDown()
	{
	}
	*/
}
