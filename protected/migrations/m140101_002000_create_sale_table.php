<?php

class m140101_002000_create_sale_table extends CDbMigration
{
	public function up()
	{
		if(!$this->getDbConnection()->createCommand("SHOW TABLES LIKE 'sale'")->query()->rowCount) $this->createTable('sale', [
			'id'=>'pk',
			'alias'=>'string',
			'title'=>'string',
			'active'=>'boolean',
			'preview'=>'VARCHAR(32)',
			'enable_preview'=>'boolean',
			'preview_text'=>'text',
			'text'=>'text',
			'create_time'=>'TIMESTAMP DEFAULT CURRENT_TIMESTAMP'
		]);
	}

	public function down()
	{
		echo "m140101_002000_create_sale_table does not support migration down.\n";
	}
}
