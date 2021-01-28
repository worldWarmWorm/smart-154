<?php

class m170920_073717_add_privacy_policy_page extends CDbMigration
{
	public function up()
	{
		if(!$this->getDbConnection()->createCommand("SHOW TABLES LIKE 'page'")->query()->rowCount) {
		$this->insert('page', [
			'id'=>10,
			'title'=>'Политика обработки данных',
			'alias'=>'privacy-policy',
			'text'=>'<p>Страница находится в разработке</p>'
		]);
		$id=(int)$this->getDbConnection()
			->createCommand('SELECT id FROM page WHERE alias=\'privacy-policy\'')
			->queryScalar();
		if($id) {
			$this->insert('menu', [
				'id'=>10,
				'title'=>'Политика обработки данных',
				'type'=>'model',
				'options'=>'{"model":"page","id":"'.$id.'"}'
			]);
		}
		}
	}

	public function down()
	{
		echo "m170920_073717_add_privacy_policy_page does not support migration down.\n";
//		return false;
	}
}
