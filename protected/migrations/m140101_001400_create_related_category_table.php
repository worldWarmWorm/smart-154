<?php

class m140101_001400_create_related_category_table extends CDbMigration 
{
	public function safeUp()
	{
		if(!$this->getDbConnection()->createCommand("SHOW TABLES LIKE 'related_category'")->query()->rowCount) {
		$this->createTable('related_category', [
			'id'=>'pk',
			'category_id'=>'integer',
			'product_id'=>'integer'
		]);
		$this->createIndex('idx_cp', 'related_category', 'category_id, product_id', true);
		}
	}

	public function safeDown()
	{
        echo "m140101_001400_create_related_category_table does not support migration down.\n";
	}
}
