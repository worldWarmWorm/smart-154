<?php

class m140101_002100_create_accordion_tables extends CDbMigration
{
	public function safeUp()
	{
		if(!$this->getDbConnection()->createCommand("SHOW TABLES LIKE 'accordion'")->query()->rowCount) {
		$this->createTable('accordion', [
			'id'=>'pk',
			'title'=>'string'
		]);

		$this->createTable('accordion_items', [
			'id'=>'pk',
			'title'=>'string',
			'description'=>'text',
			'accordion_id'=>'integer',
			'accordion_order'=>'integer DEFAULT 0'
		]);
		}
	}

	public function safeDown()
	{
		echo "m140101_002100_create_accordion_tables does not support migration down.\n";
	}
}
