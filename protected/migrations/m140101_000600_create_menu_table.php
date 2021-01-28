<?php

class m140101_000600_create_menu_table extends CDbMigration
{
	public function safeUp()
	{
		if(!$this->getDbConnection()->createCommand("SHOW TABLES LIKE 'menu'")->query()->rowCount) {
		$this->createTable('menu', [
			'id'=>'pk',
			'title'=>'string NOT NULL',
			'type'=>'string NOT NULL DEFAULT \'model\'',
            'options'=>'string',
            'seo_a_title'=>'string',
            'ordering'=>'integer NOT NULL DEFAULT 1',
            'default'=>'boolean NOT NULL DEFAULT 0',
            'hidden'=>'boolean NOT NULL DEFAULT 0',
            'system'=>'boolean NOT NULL DEFAULT 0',
            'parent_id'=>'integer'
		]);
        
         $this->insert('menu', [
            'title'=>'Главная',
            'type'=>'model',
            'options'=>'{"model":"page","id":"1"}',
            'ordering'=>1,
            'default'=>1,
            'hidden'=>1
        ]);
        
        $this->insert('menu', [
            'title'=>'Новости',
            'type'=>'model',
            'options'=>'{"model":"event"}',
            'ordering'=>2,
            'default'=>0,
            'hidden'=>0
        ]);
		}
	}

	public function safeDown()
	{
		echo "m140101_000600_create_menu_table does not support migration down.\n";
		//return false;
	}
}
