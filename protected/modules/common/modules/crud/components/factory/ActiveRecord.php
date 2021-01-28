<?php
namespace crud\components\factory;

use common\components\helpers\HArray as A;
use common\components\helpers\HDb;
use common\components\helpers\HFile;
use crud\components\helpers\HCrud;

class ActiveRecord
{
    public static function load($class)
    {
        $configs = HCrud::config();
        if(!empty($configs)) {
            $class = trim($class, '\\');
            foreach($configs as $crudConfigId=>$config) {
                if($classConfig = A::get($config, 'config')) {
                    if ($class === trim(A::get($config, 'class', ''), '\\')) {
                        if(!HCrud::isCacheFileModified($crudConfigId)) {
                            include(HCrud::getCacheFilename($crudConfigId));
                            return true;
                        }
                        
                        static::prepareConfig($crudConfigId, $classConfig);
                        
                        // @TODO на данный момент, только для модели ActiveRecord
                        
                        $namespace = preg_replace('#^(.*)\\\\[^\\\\]+$#', '\1', $class);
                        $className = preg_replace('#^.*\\\\([^\\\\]+)$#', '\1', $class);
                        $tableName = A::get($classConfig, 'tablename', strtolower(preg_replace('#\\\\+#', '_', $class)));
                        $tableSchema = HDb::getTable($tableName, true);
                        if(!$tableSchema) {
                            static::createTable($tableName, A::get($classConfig, 'definitions', []));
                            if($onAfterCreateTable=A::rget($classConfig, 'events.onAfterCreateTable')) {
                                if(is_callable($onAfterCreateTable)) {
                                    call_user_func($onAfterCreateTable);
                                }
                            }
                        }
                        elseif(count(A::get($classConfig, 'definitions', [])) != count($tableSchema->columns)) {
                            $isConfigPrepared = static::prepareConfig($crudConfigId, $classConfig);
                            $definitions = A::get($classConfig, 'definitions', []);
                            foreach($definitions as $columnName=>$columnConfig) {
                                if(!$tableSchema->getColumn($columnName)) {
                                    HDb::query("ALTER TABLE `{$tableName}` ADD COLUMN `{$columnName}` {$columnConfig['type']}");
                                }
                            }
                        }
                        
                        ob_start();
                        @include(dirname(__FILE__) . '/templates/ar.php');
                        $code = ob_get_clean();
                        
                        eval($code);
                        
                        HCrud::saveCacheFile($crudConfigId, '<?php '."\r\n".$code);
                    }
                }
            }
        }
    }
    
    protected static function prepareConfig($crudConfigId, &$classConfig)
    {
        $behaviors = A::toa(A::get($classConfig, 'behaviors', []));
        // $relations = A::toa(A::get($classConfig, 'relations', []));
        // $scopes = A::toa(A::get($classConfig, 'scopes', []));
        $rules = A::toa(A::get($classConfig, 'rules', []));
        $attributeLabels = A::toa(A::get($classConfig, 'attributeLabels', []));
            
        $classConfig['imports']=A::toa(A::get($classConfig, 'imports', []));
        $crudConfigPathAlias=HCrud::getConfigAlias($crudConfigId);
        $crudConfigPathAlias=substr($crudConfigPathAlias, 0, strrpos($crudConfigPathAlias, ".{$crudConfigId}"));
        foreach($behaviors as $name=>$behavior) {
            if(is_numeric($name) && is_string($behavior)) $behaviorClass=$behavior;
            else $behaviorClass=A::get($behavior, 'class');
            
            if(strpos($behaviorClass, '.') === 0) {
                $behaviorClass=trim($behaviorClass, '.');
                $classConfig['imports'][]="{$crudConfigPathAlias}.behaviors." . preg_replace('#^.*\\\\([^\\\\]+)$#', '\1', $behaviorClass);
                $behaviorClass=HCrud::param($crudConfigId, 'class')  . "\behaviors\\" . trim($behaviorClass, '\\');
                if(is_numeric($name) && is_string($behavior)) $behaviors[$name]=$behaviorClass;
                else $behaviors[$name]['class']=$behaviorClass;
            }
        }
        
        $definitions = A::toa(A::get($classConfig, 'definitions', []));
        $classConfig['definitions'] = [];
        
        $addRulesSafeForcy = false;
        foreach($rules as $idx=>$rule) {
            if($rule === 'safe') {
                $addRulesSafeForcy = true;
                unset($rules[$idx]);
            }
        }
        
        $rulesSafe = [];
        foreach($definitions as $name=>$definition) {
            if(is_numeric($name)) {
				if(is_array($definition)) {
                    $name=A::get($definition, 'type');
                }
                else {
                    $name = $definition;
                    $definition = ['type'=>'string'];
                }
            }
            
            if(!is_array($definition)) {
                $definition = ['type'=>$definition];
            }
            
            if(!A::get($definition, 'type')) {
                $definition['type']='string';
            }
            
            preg_match('#^([^\s]+)?\s?(.*)$#', $definition['type'], $typeDefinitionParts);
            switch($typeDefinitionParts[1]) {
                case 'pk':
                    $definition['type'] = 'INT(11) PRIMARY KEY AUTO_INCREMENT';
                    break;
                case 'string':
                    $definition['type'] = 'VARCHAR(255)';
                    break;
                case 'integer':
                    $definition['type'] = 'INT(11)';
                    break;
                case 'boolean':
                    $definition['type'] = 'TINYINT(1)';
                    break;
                default:
                    $definition['type'] = $typeDefinitionParts[1];
            }
            $definition['type'] .= ' ' . $typeDefinitionParts[2];
            
            switch($name) {
                case 'column.pk':
                    $name = 'id';
                    // $rulesSafe[] = $name;
                    $attributeLabels[$name] = $definition['label'] = A::get($definition, 'label', 'ID');
                    $definition['type'] = 'INT(11) PRIMARY KEY AUTO_INCREMENT';
                    break;
                    
                case 'column.title':
                    $name = A::get($definition, 'name', 'title');
                    $rulesSafe[] = $name;
                    $attributeLabels[$name] = $definition['label'] = A::get($definition, 'label', 'Наименование');
                    $definition['type'] = 'VARCHAR(255) NOT NULL';
                    break;
                
                case 'column.text':
                    $name = A::get($definition, 'name', 'text');
                    $rulesSafe[] = $name;
                    $attributeLabels[$name] = $definition['label'] = A::get($definition, 'label', 'Текст');
                    $definition['type'] = 'LONGTEXT';
                    break;
                    
                case 'column.sef':
                    $name = A::get($definition, 'name', 'sef');
                    $attributeLabels[$name] = $definition['label'] = A::get($definition, 'label', 'URL');
                    $definition['type'] = 'VARCHAR(255) NOT NULL DEFAULT \'\'';
                    if(empty($behaviors['sefBehavior'])) {
                        $behaviors['sefBehavior'] = [
                            'class'=>'\seo\ext\sef\behaviors\SefBehavior',
                            'attribute'=>$name,
                            'attributeLabel'=>$attributeLabels[$name],                            
                            'unique'=>true,
                            'uniqueWith'=>[],
                            'addColumn'=>false
                        ];
                    }
                    break;
                    
                case 'column.image':
                    $name = A::get($definition, 'name', 'image');
                    $attributeLabels[$name] = $definition['label'] = A::get($definition, 'label', 'Изображение');
                    $definition['type'] = 'VARCHAR(255) NOT NULL DEFAULT \'\'';
					if($imageAltName=A::get($definition, 'name_alt', 'image_alt')) {
	                    $classConfig['definitions'][$imageAltName] = ['type'=>'VARCHAR(255) NOT NULL DEFAULT \'\'', 'label'=>A::get($definition, 'label_alt', 'ALT/TITLE изображения')];
					}
                    $behaviorName = A::get($definition, 'behaviorName', 'imageBehavior');
                    if(empty($behaviors[$behaviorName])) {
                        $behaviors[$behaviorName] = [
                            'class'=>'\common\ext\file\behaviors\FileBehavior',
                            'attribute'=>$name,
                            'attributeLabel'=>$attributeLabels[$name],                            
                            'attributeAlt'=>$imageAltName,
                            'attributeAltLabel'=>$imageAltName ? $classConfig['definitions'][$imageAltName]['label'] : '',
                            'attributeFileLabel'=>A::get($definition, 'fileLabel', false),
                            // 'allowEmpty'=>A::get($definition, 'allowEmpty', true),
                            'imageMode'=>true
                        ];
                    }
                    break;
                    
                case 'column.file':
                    $name = A::get($definition, 'name', 'file');
                    $attributeLabels[$name] = $definition['label'] = A::get($definition, 'label', 'Файл');
                    $definition['type'] = 'VARCHAR(255) NOT NULL DEFAULT \'\'';
                    $behaviorName = A::get($definition, 'behaviorName', 'fileBehavior');
                    if(empty($behaviors[$behaviorName])) {
                        $behaviors[$behaviorName] = [
                            'class'=>'\common\ext\file\behaviors\FileBehavior',
                            'attribute'=>$name,
                            'attributeLabel'=>$attributeLabels[$name],
                            'types'=>A::get($definition, 'types', 'doc,docx,xls,xlsx,pdf'),
                            'maxSize'=>(int)A::get($definition, 'maxSize', 10485760),
                            'attributeFileLabel'=>A::get($definition, 'fileLabel', false),
                            // 'allowEmpty'=>A::get($definition, 'allowEmpty', true),
                        ];
                    }
                    break;
                    
                case 'column.create_time':
                    $name = 'create_time';
                    $rulesSafe[] = $name;
                    $attributeLabels[$name] = $definition['label'] = A::get($definition, 'label', 'Время создания');
                    $definition['type'] = 'TIMESTAMP DEFAULT CURRENT_TIMESTAMP';
                    break;
                    
                case 'column.update_time':
                    $name = 'update_time';
                    $attributeLabels[$name] = $definition['label'] = A::get($definition, 'label', 'Время обновления');
                    $definition['type'] = 'TIMESTAMP';
                    if(empty($behaviors['updateTimeBehavior'])) {
                        $behaviors['updateTimeBehavior'] = [
                            'class'=>'\common\ext\updateTime\behaviors\UpdateTimeBehavior',
                            'addColumn'=>false
                        ];
                    }
                    break;
                    
                case 'column.published':                    
                    $name = A::get($definition, 'name', 'published');
                    $rulesSafe[] = $name;
                    $attributeLabels[$name] = $definition['label'] = A::get($definition, 'label', 'Опубликовать на сайте');
                    $definition['type'] = 'TINYINT(1) NOT NULL DEFAULT 0';
                    $behaviorName = A::get($definition, 'behaviorName', 'publishedBehavior');
                    if(empty($behaviors[$behaviorName])) {
                        $behaviors[$behaviorName] = [
                            'class'=>'\common\ext\active\behaviors\PublishedBehavior',
                            'attribute'=>$name,
                            'addColumn'=>false
                        ];
                    }
                    break;
                    
                case 'column.nestedset':
                    $name = A::get($definition, 'rootAttribute', 'nset_root');
                    $definition['type'] = 'INT(11)';
                    $definition['foreign'] = true;
                    
                    $hasManyRoots = A::get($definition, 'hasManyRoots', true);
                    
                    $leftAttribute = A::get($definition, 'leftAttribute', 'nset_lft');
                    $classConfig['definitions'][$leftAttribute] = ['type'=>'INT(11)', 'foreign'=>true];
                    $rightAttribute = A::get($definition, 'rightAttribute', 'nset_rgt');
                    $classConfig['definitions'][$rightAttribute] = ['type'=>'INT(11)', 'foreign'=>true];
                    $levelAttribute = A::get($definition, 'levelAttribute', 'nset_level');
                    $classConfig['definitions'][$levelAttribute] = ['type'=>'INT(11)', 'foreign'=>true];
                    $orderingAttribute = A::get($definition, 'orderingAttribute', 'nset_ordering');
                    if($hasManyRoots) {
                        $classConfig['definitions'][$orderingAttribute] = ['type'=>'INT(11) NOT NULL DEFAULT 0'];
                    }
                    
                    $behaviorName = A::get($definition, 'behaviorName', 'nestedSetBehavior');
                    if(empty($behaviors[$behaviorName])) {
                        $behaviors[$behaviorName] = [
                            'class'=>'\common\ext\nestedset\behaviors\NestedSetBehavior',
                            'hasManyRoots'=>$hasManyRoots,
                            'rootAttribute'=>$name,
                            'leftAttribute'=>$leftAttribute,
                            'rightAttribute'=>$rightAttribute,
                            'levelAttribute'=>$levelAttribute,
                        ];
                        if($hasManyRoots) {
                            $behaviors[$behaviorName]['orderingAttribute'] = $orderingAttribute;
                        }
                    }
                    break;
                
                case 'column.price':
                    $name = A::get($definition, 'name', 'price');
                    $rulesSafe[] = $name;
                    $attributeLabels[$name] = $definition['label'] = A::get($definition, 'label', 'Цена');
                    $definition['type'] = 'DECIMAL(15,2) NOT NULL DEFAULT 0';
                    break;
                    
                case 'column.sort':
                    $name = A::get($definition, 'name', 'sort');
                    $attributeLabels[$name] = $definition['label'] = A::get($definition, 'label', 'Сортировка');
                    $definition['type'] = 'INT(11) NOT NULL DEFAULT ' . (int)A::get($definition, 'defaultSort', 0);
                    $behaviorName = A::get($definition, 'behaviorName', 'sortFieldBehavior');
                    if(empty($behaviors[$behaviorName])) {
                        $behaviors[$behaviorName] = [
                            'class'=>'\common\ext\sort\behaviors\SortFieldBehavior',
                            'addColumn'=>false,
                            'attribute'=>$name,
                            'attributeLabel'=>$attributeLabels[$name],
                            'asc'=>(bool)A::get($definition, 'asc', true),
                            'step'=>A::get($definition, 'step', 10),
                            'default'=>A::get($definition, 'default', 0),
                        ];
                        if(($dec=A::get($definition, 'dec')) !== null) {
                            $behaviors[$behaviorName]['dec']=$dec;
                        }
                        if(is_string($query=A::get($definition, 'query'))) {
                            $behaviors[$behaviorName]['query']=$query;
                        }
                        elseif(is_callable($query)) {
                            throw new \CException('Тип callable для параметра query типа column.sort не поддерживается!');
                        }
                    }                
                    break;    
                    
                default:
                    if(strpos($name, 'foreign.') === 0) {
                        $name = substr($name, 8);
                        $rulesSafe[] = $name;
                        $definition['type'] = 'INT(11)';
                        $attributeLabels[$name] = $definition['label'] = A::get($definition, 'label', $name);
                        $definition['foreign'] = true;
                    }
            }
            
            if(empty($attributeLabels[$name])) {
                $attributeLabels[$name] = A::get($definition, 'label', $name);
            }
            
            $classConfig['definitions'][$name] = $definition;
        }
        
        if((empty($rules) || $addRulesSafeForcy) && !empty($rulesSafe)) {
            $rules[] = [implode(',', $rulesSafe), 'safe'];
        }
        
        $classConfig['behaviors'] = $behaviors;
        $classConfig['rules'] = $rules;
        $classConfig['attributeLabels'] = $attributeLabels;
        
        return true;
    }
    
    protected static function createTable($tableName, $definitions)
    {
        if(empty($definitions) || !is_array($definitions)) {
            return false;
        }
        
        $columns = [];
        foreach($definitions as $name=>$definition) {
            $columns[] = "`{$name}` " . $definition['type'];
            if(!empty($definition['foreign'])) {
                $columns[] = "INDEX `{$name}` (`{$name}`)";
            }
        }
        
        if(!empty($columns)) {
            HDb::execute('CREATE TABLE IF NOT EXISTS `' . $tableName . '` ('.implode(',', $columns).')');
        }
    }
}
