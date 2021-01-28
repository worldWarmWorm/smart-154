<?php

class m140101_001700_create_eav_value_table extends CDbMigration
{
	public function safeUp()
	{
		if(!$this->getDbConnection()->createCommand("SHOW TABLES LIKE 'eav_value'")->query()->rowCount) $this->createTable('eav_value', [
			'id'=>'pk',
			'id_attrs'=>'integer',
			'id_product'=>'integer',
			'value'=>'string'
		]);
	}

	public function safeDown()
	{
		echo "m140101_001700_create_eav_value_table does not support migration down.\n";
		//return false;
	}
}
