<?php

class m140101_000700_create_metadata_table extends CDbMigration
{
	public function safeUp()
	{
		if(!$this->getDbConnection()->createCommand("SHOW TABLES LIKE 'metadata'")->query()->rowCount) $this->createTable('metadata', [
			'id'=>'pk',
			'owner_name'=>'string NOT NULL',
			'owner_id'=>'integer NOT NULL',
			'meta_h1'=>'string',
			'meta_title'=>'string',
			'meta_key'=>'string',
			'meta_desc'=>'string',
			'a_title'=>'string',
			'priority'=>'float',
			'lastmod'=>'string',
			'changefreq'=>'string',            
            'update_time'=>'TIMESTAMP'
		]);
	}

	public function safeDown()
	{
		echo "m140101_000700_create_metadata_table does not support migration down.\n";
		//return false;
	}
}
