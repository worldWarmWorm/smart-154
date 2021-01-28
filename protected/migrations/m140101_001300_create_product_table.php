<?php

class m140101_001300_create_product_table extends CDbMigration
{
	public function safeUp()
	{
		if(!$this->getDbConnection()->createCommand("SHOW TABLES LIKE 'product'")->query()->rowCount) {
		$this->createTable('product', [
			'id'=>'pk',
			'category_id'=>'integer',
			'brand_id'=>'integer',
			'title'=>'string',
			'alias'=>'string',
			'code'=>'string',
			'price'=>'DECIMAL(15,2)',
			'old_price'=>'DECIMAL(15,2)',
			'sale'=>'boolean',
			'new'=>'boolean',
			'hit'=>'boolean',
			'notexist'=>'boolean',
			'in_carousel'=>'boolean',
			'hidden'=>'boolean',
			'on_shop_index'=>'boolean DEFAULT 1',
			'link_title'=>'string',
			'update_time'=>'TIMESTAMP DEFAULT CURRENT_TIMESTAMP',
			'description'=>'LONGTEXT',
			'main_image'=>'string',
			'main_image_alt'=>'string',
			'main_image_enable'=>'boolean'
		]);
		$this->createIndex('alias', 'product', 'alias');
		}
	}

	public function safeDown()
	{
		echo "m140101_001300_create_product_table does not support migration down.\n";
		//return false;
	}
}
