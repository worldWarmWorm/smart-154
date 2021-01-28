<?php

class m140101_002500_create_iblock_tables extends CDbMigration
{
    public function safeUp()
    {
		if(!$this->getDbConnection()->createCommand("SHOW TABLES LIKE 'info_block'")->query()->rowCount) {
        $this->createTable('info_block', array(
                'id' => 'pk',
                'title' => 'VARCHAR(255) NOT NULL',
                'code' => 'VARCHAR(255) NULL DEFAULT NULL',
                'sort' => 'INT(11) NOT NULL DEFAULT 500',
                'active' => 'TINYINT(1) NULL DEFAULT 1',
                'use_preview' => 'TINYINT(1) NULL DEFAULT 1',
                'use_description' => 'TINYINT(1) NULL DEFAULT 1',
            )
        );
        $this->createIndex('uniq', 'info_block', 'title', true);

        $this->createTable('info_block_prop', array(
                'id' => 'pk',
                'title' => 'VARCHAR(255) NOT NULL',
                'active' => 'TINYINT(1) NULL DEFAULT 1',
                'type' => 'CHAR(1) NOT NULL',
                'multiple' => 'TINYINT(1) NULL',
                'code' => 'VARCHAR(255) NOT NULL',
                'sort' => 'INT(11) NOT NULL DEFAULT 500',
                'info_block_id' => 'INT NOT NULL',
                'default' => 'VARCHAR(255) DEFAULT NULL',
                'options' => 'TEXT DEFAULT NULL',
                'required' => 'TINYINT(1) DEFAULT NULL',
            )
        );
        $this->createIndex('uniq', 'info_block_prop', array('info_block_id', 'code'), true);

        $this->createTable('info_block_prop_value', array(
                'id' => 'pk',
                'prop_id' => 'INT(11) NOT NULL',
                'value_key' => 'VARCHAR(255) NOT NULL',
                'value_text' => 'VARCHAR(255) NOT NULL',
            )
        );
        $this->createIndex('uniq', 'info_block_prop_value', array('prop_id', 'value_key'), true);

        $this->createTable('info_block_element', array(
                'id' => 'pk',
                'code' => 'VARCHAR(255) NULL DEFAULT NULL',
                'active' => 'TINYINT(1) NULL DEFAULT 1',
                'title' => 'VARCHAR(255) NOT NULL',
                'preview' => 'VARCHAR(255) NULL',
                'description' => 'TEXT NULL',
                'created_at' => 'TIMESTAMP NULL DEFAULT CURRENT_TIMESTAMP',
                'updated_at' => 'TIMESTAMP NULL DEFAULT NULL',
                'sort' => 'INT(11) NOT NULL DEFAULT 500',
                'info_block_id' => 'INT(11) NOT NULL',
            )
        );

        $this->createTable('info_block_element_prop', array(
                'id' => 'pk',
                'element_id' => 'INT(11) NOT NULL',
                'prop_id' => 'INT(11) NOT NULL',
                'value' => 'LONGTEXT NOT NULL',
            )
        );

        $this->addForeignKey(
            'block_property',
            'info_block_prop',
            'info_block_id',
            'info_block',
            'id',
            'CASCADE',
            'CASCADE'
        );

        $this->addForeignKey(
            'block_element',
            'info_block_element',
            'info_block_id',
            'info_block',
            'id',
            'CASCADE',
            'CASCADE'
        );

        $this->addForeignKey(
            'element_property_property',
            'info_block_element_prop',
            'prop_id',
            'info_block_prop',
            'id',
            'CASCADE',
            'CASCADE'
        );

        $this->addForeignKey(
            'element_property_element',
            'info_block_element_prop',
            'element_id',
            'info_block_element',
            'id',
            'CASCADE',
            'CASCADE'
        );
		}
    }

    public function safeDown()
    {
       echo "m140101_002500_create_iblock_tables does not support migration down.\n";

    }
}
