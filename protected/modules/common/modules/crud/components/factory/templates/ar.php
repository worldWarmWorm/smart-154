<?php
/** @var $crudConfigId string идентификатор CRUD-конфигурации */
/** @var $classConfig array параметры класса */
/** @var $namespace string простанство имен */
/** @var $className string имя класса */
/** @var $tableName string имя таблицы */
use common\components\helpers\HArray as A;
?>
namespace <?= $namespace; ?>;
use common\components\helpers\HArray as A;
<?= array_reduce(A::get($classConfig, 'imports', []), function($code, $alias) { return $code . "\Yii::import('{$alias}', true);\n"; }); ?>
class <?= $className; ?> extends \common\components\base\ActiveRecord
{
	<? foreach(A::get($classConfig, 'consts', []) as $nm=>$v) echo "const {$nm}=".(is_integer($v) ? $v : "'{$v}'").";\n"; ?>
	public function tableName()
	{
		return '<?= $tableName; ?>';
	}
	
	public function behaviors()
	{
		return A::m(parent::behaviors(), <?= A::toPHPString(A::toa(A::get($classConfig, 'behaviors', []))); ?>);
	}
	
	public function relations()
	{
		return $this->getRelations(<?= A::toPHPString(A::toa(A::get($classConfig, 'relations', []))); ?>);
	}
	
	public function scopes()
	{
		return $this->getScopes(<?= A::toPHPString(A::toa(A::get($classConfig, 'scopes', []))); ?>);
	}
	
	public function rules()
	{
		return $this->getRules(<?= A::toPHPString(A::toa(A::get($classConfig, 'rules', []))); ?>);
	}
	
	public function attributeLabels()
	{
		return $this->getAttributeLabels(<?= A::toPHPString(A::toa(A::get($classConfig, 'attributeLabels', [])), false); ?>);
	}
	<?php
	$methods = A::get($classConfig, 'methods', []);
	foreach($methods as $code) {
		if(!is_string($code) && is_callable($code)) {
	        $code=call_user_func($code);
	    }
	    echo "\r\n" . $code . "\r\n"; 
	}
	?>
}
