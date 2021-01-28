<?php

class m140101_002300_create_brand_table extends CDbMigration
{
	public function up()
	{
		if(!$this->getDbConnection()->createCommand("SHOW TABLES LIKE 'brand'")->query()->rowCount) $this->createTable('brand', [
			'id'=>'pk',
			'alias'=>'string',
			'title'=>'string',
			'logo'=>'string',
			'preview_text'=>'text',
			'detail_text'=>'text',
			'active'=>'boolean',
			'update_time'=>'TIMESTAMP'
		]);
	}

	public function down()
	{
        echo "m140101_002300_create_brand_table does not support migration down.\n";
	}
}
