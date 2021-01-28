<?php

class m140101_001600_create_eav_attribute_table extends CDbMigration
{
	public function safeUp()
	{
		if(!$this->getDbConnection()->createCommand("SHOW TABLES LIKE 'eav_attribute'")->query()->rowCount) $this->createTable('eav_attribute', [
			'id'=>'pk',
			'name'=>'string',
            'type'=>'SMALLINT(6)',
			'fixed'=>'boolean NOT NULL DEFAULT 0',
			'filter'=>'boolean NOT NULL DEFAULT 0',
		]);
	}

	public function safeDown()
	{
		echo "m140101_001600_create_eav_attribute_table does not support migration down.\n";
		//return false;
	}
}
