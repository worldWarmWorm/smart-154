<?php

class m140101_001000_create_question_table extends CDbMigration
{
	public function safeUp()
	{
		if(!$this->getDbConnection()->createCommand("SHOW TABLES LIKE 'question'")->query()->rowCount) $this->createTable('question', [
			'id'=>'pk',
			'username'=>'string',
			'question'=>'LONGTEXT',
			'answer'=>'LONGTEXT',
			'published'=>'boolean NOT NULL DEFAULT 0',
            'created'=>'TIMESTAMP'
		]);
	}

	public function safeDown()
	{
		echo "m140101_001000_create_question_table does not support migration down.\n";
		//return false;
	}
}
