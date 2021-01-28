<?php

class m160415_115947_create_reviews_table extends CDbMigration
{
	public function up()
	{
		$this->createTable('reviews', array(
			'id'=>'pk',
			'alias'=>'string',
			'author'=>'string',
			'image'=>'string',
			'image_enable'=>'boolean',
			'preview_text'=>'text',
			'detail_text'=>'text',
			'publish_date'=>'date',
			'published'=>'boolean',
			'create_time'=>'TIMESTAMP DEFAULT CURRENT_TIMESTAMP',
			'comment'=>'text'
		));		
		$this->createIndex('published', 'reviews', 'published');
	}

	public function down()
	{
		$this->dropTable('reviews');
	}
}