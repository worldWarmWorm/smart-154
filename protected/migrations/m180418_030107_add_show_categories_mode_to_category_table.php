<?php

class m180418_030107_add_show_categories_mode_to_category_table extends CDbMigration
{
	public function up()
	{
        $this->addColumn('category', 'show_categories_mode', 'TINYINT(4) DEFAULT 0');
	}

	public function down()
	{
		echo "m180418_030107_add_show_categories_mode_to_category_table does not support migration down.\n";
		// return false;
	}
}
