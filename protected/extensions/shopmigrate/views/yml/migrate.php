<?php
/** @var \CController $this */
/** @var \ext\shopmigrate\models\YmlExportForm $modelExport */
/** @var \ext\shopmigrate\models\YmlImportForm $modelImport */
/** @var \ext\shopmigrate\models\SettingsForm $modelSettings */
use common\components\helpers\HYii as Y;

$t=Y::ct('\ext\shopmigrate\Messages.actions/ymlmigrate');
?>
<h1><?=$t('page.title')?></h1>

<?php $this->renderPartial('ext.shopmigrate.views.yml._migrate_tabs', compact('modelExport', 'modelImport', 'modelSettings')); ?>

