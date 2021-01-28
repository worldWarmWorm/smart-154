<?php
namespace common\ext\sort\behaviors;

use common\components\helpers\HYii as Y;
use common\components\helpers\HDb;

class SortFieldBehavior extends \CBehavior
{
    /**
     * @var boolean добавить автоматически в таблицу базы данных поле для хранения данных
     * данного атрибута. По умолчанию (TRUE) добавить.
     */
    use \common\traits\models\AddColumn;
    
    /**
     * Имя атрибута сортировки.
     * По умолчанию "sort".
     * @var string
     */
    public $attribute='sort';
    
    /**
     * Подпись атрибута сортировки.
     * @var string|null
     */
    public $attributeLabel=null;
    
    /**
     * Направление сортировки по возрастанию.
     * Если передано false сортировка будет по убыванию.
     * По умолчанию true. 
     * @var bool
     */
    public $asc=true;
    
    /**
     * Для пустых значений уменьшать значение сортировки.
     * По умолчанию null будет определено относительно параметра $asc.
     * Если $asc установлен в true (по возрастанию), то значение сортировки
     * для пустых значенией будет увеличено, и, соотвественно, для $asc=false 
     * наоборот уменьшено. 
     * @var bool|null
     */
    public $dec=null;
    
    /**
     * Шаг увеличения/уменьшения значения сортировки для пустых значений.
     * По умолчанию 10.
     * @var integer
     */
    public $step=10;
    
    /**
     * Значение сортировки по умолчанию 
     * @var integer
     */
    public $default=0;
    
    /**
     * SQL выражение получения значения сортировки для записей с пустым значением
     * Может быть передано callable значение function($sortFieldBehavior){}
     * @var string|callable|null
     */
    public $query=null;
    
    /**
     * 
     * {@inheritDoc}
     * @see \CBehavior::events()
     */
    public function events()
    {
        return [
            'onBeforeSave'=>'beforeSave'
        ];
    }
    
    /**
     * {@inheritDoc}
     * @see \CBehavior::attach($owner)
     */
    public function attach($owner)
    {
        parent::attach($owner);
        
        if($this->attributeLabel === null) {
            $t=Y::ct('\common\ext\sort\Messages.common', 'common');
            $this->attributeLabel=$t('default.label');
        }
        
        $this->addColumn($this->owner->tableName(), $this->attribute, 'INT(11) NOT NULL DEFAULT ' . $this->getDefaultSort());
    }
    
    /**
     * @see \CActiveRecord::rules()
     * @return []
     */
    public function rules()
    {
        return [
            [$this->attribute, 'numerical', 'integerOnly'=>true],
        ];
    }
    
    /**
     * Scope: по атрибуту сортировки
     * @param boolean|null $asc по возрастанию.
     * По умолчанию null будет использовано значение 
     * параметра SortFieldBehavior::$asc
     * @param string $tableAlias алиас таблицы. 
     * По умолчанию "t"
     * @return \CActiveRecord
     */
    public function bySort($asc=null, $tableAlias='t')
    {
        if($asc === null) {
            $asc=(bool)$this->asc;
        }
        
        $c=HDb::criteria();
        $c->order=HDb::qt($tableAlias) . '.' . HDb::qc($this->attribute) . ' ' . ($asc ? 'ASC' : 'DESC');
        
        $this->owner->getDbCriteria()->mergeWith($c);
        
        return $this->owner;
    }
    
    /**
     * Получение значения сортировки по умолчанию
     * @return number
     */
    public function getDefaultSort()
    {
        return (int)$this->default;
    }
    
    /**
     * Event: beforeSave
     * @return boolean
     */
    public function beforeSave()
    {
        if($this->owner->isNewRecord && !$this->owner->sort) {
            if($this->query) {
                if(is_callable($this->query)) {
                    $query=call_user_func($this->query, $this);
                }
                else {
                    $query=$this->query;
                }
            }
            else {
                $dec=$this->dec;
                if($dec === null) {
                    $this->dec=!$this->asc;
                }
                
                $query='SELECT IF(ISNULL(MAX(' . HDb::qc($this->attribute) . ')),'
                    . $this->getDefaultSort().',MAX(' . HDb::qc($this->attribute) . ')' . ($dec ? '-' : '+') . $this->step 
                    . ') FROM ' . HDb::qt($this->owner->tableName()) . ' WHERE 1=1';
            }
            
            if(is_string($query)) {
                $this->owner->sort=(int)HDb::queryScalar($query);
            }
        }
        
        return true;
    }
}