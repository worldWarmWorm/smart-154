<?php

class m140101_001200_create_category_table extends CDbMigration
{
	public function safeUp()
	{
		if(!$this->getDbConnection()->createCommand("SHOW TABLES LIKE 'category'")->query()->rowCount) {
		$this->createTable('category', [
			'id'=>'pk',
			'title'=>'string',
			'alias'=>'string',
			'ordering'=>'INT(11) NOT NULL DEFAULT 1',
			'update_time'=>'TIMESTAMP DEFAULT CURRENT_TIMESTAMP',
			'description'=>'LONGTEXT',
			'main_image'=>'string',
			'main_image_alt'=>'string',
			'main_image_enable'=>'boolean',
			'root'=>'INT(11) NOT NULL',
			'lft'=>'INT(11) NOT NULL',
			'rgt'=>'INT(11) NOT NULL',
			'level'=>'SMALLINT(5) NOT NULL',
			'update_time'=>'TIMESTAMP'
		]);
		$this->createIndex('root', 'category', 'root');
		$this->createIndex('lft', 'category', 'lft');
		$this->createIndex('rgt', 'category', 'rgt');
		$this->createIndex('level', 'category', 'level');
		}
	}

	public function safeDown()
	{
		echo "m140101_001200_create_category_table does not support migration down.\n";
		//return false;
	}
}
