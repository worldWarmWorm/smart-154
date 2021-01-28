<?php

class m140101_000400_create_image_table extends CDbMigration
{
	public function safeUp()
	{
		if(!$this->getDbConnection()->createCommand("SHOW TABLES LIKE 'image'")->query()->rowCount) $this->createTable('image', [
			'id'=>'pk',
			'model'=>'string NOT NULL',
			'item_id'=>'integer NOT NULL',
			'filename'=>'string NOT NULL',
            'description'=>'VARCHAR(500)',
            'ordering'=>'integer NOT NULL DEFAULT 1',
		]);
	}

	public function safeDown()
	{
		echo "m140101_000400_create_image_table does not support migration down.\n";
		//return false;
	}
}
