<?php
use common\components\helpers\HYii as Y;
use common\components\helpers\HArray as A;
use common\components\helpers\HDb;

class SearchController extends Controller
{
    public function actionAutoComplete() 
    {
        $result = '';
        
        $q = Y::config('search', 'queryname');
        if (isset($_GET[$q])) {
            $query = Yii::app()->request->getQuery($q);
            $phrases=$this->getPhrases($query);

            $autocompletes = Y::config('search', 'autocomplete.models');
            foreach($autocompletes as $modelClass=>$config) {
                $criteria = HDb::criteria(A::get($config, 'criteria', []));
                foreach(A::get($config, 'attributes', []) as $attribute) {
                    $this->addSearchInCondition($criteria, $attribute, $phrases);
                }
                $criteria->limit = Y::config('search', 'autocomplete.limit', 10);
                $models = $modelClass::model()->findAll($criteria);
                if(!empty($models)) {
                    foreach($models as $model) {
                        $titleAttribute = A::get($config, 'titleAttribute', 'title');
                        if(is_callable($titleAttribute)) {
                            $title = call_user_func($titleAttribute, $model);
                        }
                        else {
                            $title = $model->$titleAttribute;
                        }
                        $result .= $title . "\n";
                    }
                }
            }
        }
        
        echo $result;
        
        Y::end();
    }
    
	public function actionIndex()
	{
	    $this->prepareSeo('Результаты поиска');
	    $this->breadcrumbs->add('Результаты поиска');
	    
	    $q = Y::config('search', 'queryname');
	    
	    $query = Yii::app()->request->getQuery($q);
		
	    if (mb_strlen($query, 'UTF-8') < Y::config('search', 'minlength', 3)) {
			$this->prepareSeo('Слишком короткий запрос');
			$this->render('index_empty');
			return;
		}

    	$phrases=$this->getPhrases($query);
		
    	$dataProviders = [];
    	
    	$searches = Y::config('search', 'search.models');
    	foreach($searches as $modelClass=>$config) {
    	    $criteria = HDb::criteria(A::get($config, 'criteria', []));
			$attributes=A::get($config, 'attributes', []);
			foreach($attributes as $attribute) {
    	        $this->addSearchInCondition($criteria, $attribute, $phrases);
    	    }

			if(!empty($attributes) && !empty($phrases)) {
				$strongRelevanceMultiplier=(int)A::get($config, 'strong_relevance_multiplier', 0);
	            $maxRelevance=count($attributes) * count($phrases) * 20;
	            $relevance=$maxRelevance;
	            $select='';
            	foreach($phrases as $phrase) {
            		$select.=!$select ? '(' : '+';
            		foreach($attributes as $attribute) {
						if($strongRelevanceMultiplier) {
							$select.='IF('.HDb::qc($attribute).' REGEXP '.HDb::qv("[[:<:]]{$phrase}[[:>:]]").','.($strongRelevanceMultiplier*$relevance).', IF('.HDb::qc($attribute).' LIKE '.HDb::qv("%{$phrase}%").', '.$relevance.',';
						}
						else {
                			$select.='IF('.HDb::qc($attribute).' LIKE '.HDb::qv("%{$phrase}%").', '.$relevance.',';
						}
            			$relevance-=20;
            		}
            		$select.='0' . str_repeat(')', ($strongRelevanceMultiplier ? 2 : 1) * count($attributes));
            	}
	            $select.=') AS `relevance`';
	            $criteria->select=$select;
	            $criteria->order='`relevance` DESC';
        	}
    	    
    	    $pagination = new \CPagination();
    	    $pagination->pageSize = A::get($config, 'limit', 10);;
    	    
    	    $dataProviders[] = [
    	        'modelClass' => $modelClass,
    	        'title' => A::get($config, 'title'),
    	        'view' =>  A::get($config, 'view'),
    	        'wrapperOpen' => A::get($config, 'wrapperOpen'),
    	        'wrapperClose' => A::get($config, 'wrapperClose'),
    	        'listView' => A::get($config, 'listView', []),
    	        'item' => A::get($config, 'item', []),
    	        'dataProvider' => new \CActiveDataProvider($modelClass, [
    	            'criteria'=>$criteria,
    	            'pagination' => $pagination
    	        ])
    	    ];
    	}
    			
		$this->render('index', compact('dataProviders', 'query'));
	}
    
    protected function getPhrases($q) 
    {
        $q=preg_replace('/ +/', ' ', $q);
        return array_filter(explode(' ', $q), function($v) { return (strlen($v) > 2); });
    }
    
    protected function addSearchInCondition(&$criteria, $attribute, $phrases, $operator='OR') {
        $c=new CDbCriteria();
        if(!empty($phrases)) {
            foreach($phrases as $p) {
                $c->addSearchCondition($attribute, $p, true, 'AND');
            }
        }
        $criteria->mergeWith($c, $operator);
    }
}
