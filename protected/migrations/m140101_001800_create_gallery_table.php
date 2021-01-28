<?php

class m140101_001800_create_gallery_table extends CDbMigration
{
	public function safeUp()
	{
		if(!$this->getDbConnection()->createCommand("SHOW TABLES LIKE 'gallery'")->query()->rowCount) $this->createTable('gallery', [
			'id'=>'pk',
            'title'=>'VARCHAR(500)',
			'alias'=>'string',
			'description'=>'LONGTEXT',
            'ordering'=>'integer',
            'preview_id'=>'string',
            'update_time'=>'TIMESTAMP',
		]);        
	}

	public function safeDown()
	{
		echo "m140101_001800_create_gallery_table does not support migration down.\n";
		//return false;
	}
}
