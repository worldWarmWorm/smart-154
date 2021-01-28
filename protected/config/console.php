<?php
$dir=dirname(__FILE__);
return array(
	'basePath'=>"{$dir}/..",
	'components'=>array(
		'db'=>include(is_file("{$dir}/local.db.php") ? "{$dir}/local.db.php" : "{$dir}/db.php"),
	),
	'commandMap'=>array(
		'migrate'=>array(
			'class'=>'system.cli.commands.MigrateCommand',
			'migrationPath'=>'application.migrations',
			'migrationTable'=>'tbl_migration',
			'connectionID'=>'db',
			// 'templateFile'=>'application.migrations.template',
		)
	)
);
