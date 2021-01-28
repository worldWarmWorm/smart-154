<?php

class m140101_000900_create_settings_table extends CDbMigration
{
	public function safeUp()
	{
		if(!$this->getDbConnection()->createCommand("SHOW TABLES LIKE 'settings'")->query()->rowCount) {
		$this->createTable('settings', [
			'id'=>'pk',
			'category'=>'VARCHAR(64) NOT NULL DEFAULT \'system\'',
			'key'=>'string',
			'value'=>'LONGTEXT'
		]);
        
        $this->createIndex('idx_ukey', 'settings', ['category', 'key']);
		}
	}

	public function safeDown()
	{
		echo "m140101_000900_create_settings_table does not support migration down.\n";
		//return false;
	}
}
