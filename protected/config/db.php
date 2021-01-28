<?php
/**
 * DB connection config
 */
return array(
    'connectionString' => 'mysql:host=localhost;dbname=smart_db',
    'username' => 'root',
    'password' => '',
	'initSQLs'=>[
		"SET sql_mode='';"
	]
);
