<?php

class m140101_000200_create_event_table extends CDbMigration
{
	public function safeUp()
	{
	    if(!$this->getDbConnection()->createCommand("SHOW TABLES LIKE 'event'")->query()->rowCount) {
		$this->createTable('event', [
			'id'=>'pk',
			'title'=>'string',
			'alias'=>'string',
			'intro'=>'TEXT',
            'text'=>'LONGTEXT',
            'preview'=>'string',
            'enable_preview'=>'boolean DEFAULT 1',
            'publish'=>'boolean DEFAULT 1',
			'created'=>'DATETIME',
            'update_time'=>'TIMESTAMP',
		]);
        $this->createIndex('publish', 'event', 'publish');
        
        $this->insert('event', [
            'title'=>'Создали сайт',
            'intro'=>'Мы создали сайт!',
            'text'=>'<p>Мы создали сайт!</p>',
            'publish'=>1,
            'created'=>new \CDbExpression('NOW()'),
            'update_time'=>new \CDbExpression('NOW()')
        ]);
		}
	}

	public function safeDown()
	{
		echo "m140101_000200_create_event_table does not support migration down.\n";
		//return false;
	}
}
