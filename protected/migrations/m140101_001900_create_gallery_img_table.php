<?php

class m140101_001900_create_gallery_img_table extends CDbMigration
{
	public function safeUp()
	{
		if(!$this->getDbConnection()->createCommand("SHOW TABLES LIKE 'gallery_img'")->query()->rowCount) $this->createTable('gallery_img', [
			'id'=>'pk',
            'image_order'=>'integer DEFAULT 1',
            'gallery_id'=>'integer NOT NULL',
            'title'=>'VARCHAR(500)',
			'description'=>'LONGTEXT',
            'image'=>'string',
            'update_time'=>'TIMESTAMP',
		]);
	}

	public function safeDown()
	{
		echo "m140101_001900_create_gallery_img_table does not support migration down.\n";
		//return false;
	}
}
