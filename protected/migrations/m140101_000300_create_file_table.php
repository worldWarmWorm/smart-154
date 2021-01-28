<?php

class m140101_000300_create_file_table extends CDbMigration
{
	public function safeUp()
	{
		if(!$this->getDbConnection()->createCommand("SHOW TABLES LIKE 'file'")->query()->rowCount) $this->createTable('file', [
			'id'=>'pk',
			'model'=>'string NOT NULL',
			'item_id'=>'integer NOT NULL',
			'filename'=>'string NOT NULL',
            'description'=>'VARCHAR(500)',
		]);
	}

	public function safeDown()
	{
		echo "m140101_000300_create_file_table does not support migration down.\n";
		//return false;
	}
}
