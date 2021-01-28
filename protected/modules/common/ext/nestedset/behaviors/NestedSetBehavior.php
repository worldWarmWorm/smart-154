<?php
namespace common\ext\nestedset\behaviors;

\Yii::import('common.vendors.nested-set-behavior.NestedSetBehavior');

class NestedSetBehavior extends \NestedSetBehavior
{
	public $hasManyRoots=true;
    public $rootAttribute='nset_root';
    public $leftAttribute='nset_lft';
    public $rightAttribute='nset_rgt';
    public $levelAttribute='nset_level';
	public $orderingAttribute='nset_ordering';
	
	public function getCriteriaSelect()
	{
	    $select = implode(',', [
	        $this->rootAttribute,
	        $this->leftAttribute,
	        $this->rightAttribute,
	        $this->levelAttribute
	    ]);
	    
	    if($this->hasManyRoots) {
	        $select .= ',' . $this->orderingAttribute;
	    }
	    
	    return $select;
	}
}
	
