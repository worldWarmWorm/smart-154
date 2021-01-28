<?php

class m140101_001500_create_product_review_table extends CDbMigration
{
	public function safeUp()
	{
		if(!$this->getDbConnection()->createCommand("SHOW TABLES LIKE 'product_review'")->query()->rowCount) $this->createTable('product_review', [
			'id'=>'pk',
			'product_id'=>'integer',
			'mark'=>'integer',
			'username'=>'string',
            'ip'=>'integer',
			'text'=>'LONGTEXT',
			'published'=>'boolean NOT NULL DEFAULT 0',
            'ts'=>'TIMESTAMP'
		]);
	}

	public function safeDown()
	{
		echo "m140101_001500_create_product_review_table does not support migration down.\n";
		//return false;
	}
}
