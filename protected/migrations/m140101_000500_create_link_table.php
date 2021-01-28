<?php

class m140101_000500_create_link_table extends CDbMigration
{
	public function safeUp()
	{
		if(!$this->getDbConnection()->createCommand("SHOW TABLES LIKE 'link'")->query()->rowCount) $this->createTable('link', [
			'id'=>'pk',
			'title'=>'string NOT NULL',
			'url'=>'string NOT NULL'
		]);
	}

	public function safeDown()
	{
		echo "m140101_000500_create_link_table does not support migration down.\n";
		//return false;
	}
}
