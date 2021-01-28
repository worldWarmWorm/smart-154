<?php

class m140101_000100_create_blog_table extends CDbMigration
{
	public function safeUp()
	{
	    if(!$this->getDbConnection()->createCommand("SHOW TABLES LIKE 'blog'")->query()->rowCount) $this->createTable('blog', [
			'id'=>'pk',
			'title'=>'string',
			'alias'=>'string',
			'ordering'=>'INT(11) NOT NULL DEFAULT 1',
			'params'=>'LONGTEXT',
			'update_time'=>'TIMESTAMP'
		]);
	}

	public function safeDown()
	{
		echo "m140101_000100_create_blog_table does not support migration down.\n";
		//return false;
	}
}
