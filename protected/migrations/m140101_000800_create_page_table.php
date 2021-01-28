<?php

class m140101_000800_create_page_table extends CDbMigration
{
	public function safeUp()
	{
		if(!$this->getDbConnection()->createCommand("SHOW TABLES LIKE 'page'")->query()->rowCount) {
		$this->createTable('page', [
			'id'=>'pk',
			'parent_id'=>'integer',
			'blog_id'=>'integer',
			'alias'=>'string',
			'title'=>'string',
			'intro'=>'TEXT',
			'text'=>'LONGTEXT',
            'created'=>'TIMESTAMP',
			'modified'=>'TIMESTAMP',
			'update_time'=>'TIMESTAMP',
            'view_template'=>'string'
		]);
        
        $this->insert('page', [
            'alias'=>'index',
            'title'=>'Главная',
            'intro'=>'<p>Сайт находится в разработке</p>',
            'text'=>'<p>Сайт находится в разработке</p>',            
            'created'=>new \CDbExpression('NOW()'),
            'modified'=>new \CDbExpression('NOW()'),
            'update_time'=>new \CDbExpression('NOW()')
        ]);
		}
	}

	public function safeDown()
	{
		echo "m140101_000800_create_page_table does not support migration down.\n";
		//return false;
	}
}
