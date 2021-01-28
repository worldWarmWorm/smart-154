<?php
/**
 * Класс-помощник для форм модуля "CRUD"
 */
namespace crud\components\helpers;

use common\components\helpers\HArray as A;
use common\components\helpers\HRequest as R;
use common\components\exceptions\ParamException;
use common\components\exceptions\ConfigException;
use crud\components\helpers\HCrud;

class HCrudForm
{
	/**
	 * Получить параметры для виджета \CActiveForm
	 * @param string $cid идентифкатор настроек CRUD для модели.
	 * @param string $pagePath путь к настройкам текущей страницы в конфигурации CRUD для текущей модели.
	 * @param array $properties дополнительные параметры формы.
	 * @return array
	 */
	public static function getFormProperties($cid, $pagePath, $properties=[])
	{
		$properties=A::m(
			HCrud::param($cid, 'crud.form', [], true), 
			A::m(
				HCrud::param($cid, $pagePath.'.form', [], true),
				$properties
			)
		);
        
        if(isset($properties['attributes'])) {
            unset($properties['attributes']);
        }
        if(isset($properties['buttons'])) {
            unset($properties['buttons']);
        }
                                       
        return $properties;
	}
	
	/**
	 * Получить HTML код полей формы
	 * @param string $cid идентифкатор настроек CRUD для модели.
	 * @param array $attributes массив конфигурации полей(атрибутов)
	 * @param \CActiveRecord|NULL $model объект модели. 
	 * По умолчанию (NULL) будет получена из настроек. 
	 * @param \CActiveForm|NULL $form объект формы. 
	 * По умолчанию (NULL) будет создан новый объект \CActiveForm. 
	 * @param \CController|NULL $controller объект контроллера.
	 * По умолчанию (NULL) будет получен из \Yii::app()->getController() 
	 */
	public static function getHtmlFields($cid, $attributes=[], $model=null, $form=null, $controller=null)
	{
	    if(is_callable($attributes)) {
	        $attributes=call_user_func_array($attributes, [&$model]);
	    }
	    
		if(!is_array($attributes)) ParamException::e();
		
		if(!$controller) $controller=\Yii::app()->getController();
				
		if(array_key_exists('attributes.sort', $attributes)) {
			$attributesSort=A::get($attributes, 'attributes.sort', []);
			unset($attributes['attributes.sort']);
			$attributes=A::sort(
				$attributes, 
				$attributesSort, 
				!A::get($attributes, 'attributes.sort.filter', false),
				A::get($attributes, 'attributes.sort.reverse', false)
			);
		}
		if(array_key_exists('attributes.sort.filter', $attributes)) {
			unset($attributes['attributes.sort.filter']);
		}
		if(array_key_exists('attributes.sort.reverse', $attributes)) {
			unset($attributes['attributes.sort.reverse']);
		}
		
		$html='';
		foreach($attributes as $attribute=>$config)
		{
		    $type=null;			
			if(!is_string($attribute)) {
				if(!is_string($config)) ConfigException::e();
				
				$attribute=$config;
				$type='text';
			}
			else {
			    if(!is_string($config) && is_callable($config)) {
			        $config=call_user_func_array($config, [&$model]);
			    }
			    
				if(strpos($attribute, 'code.html') === 0) {
			        $type = 'code.html';
			    }
			    elseif(is_string($config)) $type=$config;
				elseif(is_array($config)) {
					if($phpCode=A::get($config, 'php')) {
						$html.=htmlspecialchars($phpCode);
					}
					elseif(!($type=A::get($config, 'type'))) {
						ConfigException::e();
					}
				}
				else {
					ConfigException::e();
				}
			}
			
			if($modelRelationName = A::get($config, 'relation')) {
			    $modelRelations=$model->relations();
			    if(!empty($modelRelations[$modelRelationName])) {
    		        $modelRelation=$model->getRelated($modelRelationName);
    			    if(empty($modelRelation)) {
    			        $modelRelation=new $modelRelations[$modelRelationName][1];
    			    }
    			    $model=$modelRelation;
			    }
			}
			
			if(is_string($type)) {
			    if(strpos($type, 'foreign.dropdownlist.') === 0) {
			        if(is_string($config)) $config = [];
			        $config['crud'] = substr($type, 21);
			        $type = 'foreign.dropdownlist.crud';
			    }
			    
				switch(strtolower($type)) {
				    case 'foreign.dropdownlist.crud':
				        $typeClass='\common\widgets\form\\DropDownListField';
				        $params=A::get($config, 'params', []);
				        
				        if($relationCrudId = A::get($config, 'crud')) {
				            $relationCrudConfig = HCrud::config($relationCrudId);
	                        $relationClassName = A::get($relationCrudConfig, 'class');
							$paramsDataCriteria=A::get($config, 'criteria');
	                        if(is_callable($paramsDataCriteria)) {
	                            $paramsDataCriteria=call_user_func($paramsDataCriteria, $model);
	                        }
	                        $params['data'] = $relationClassName::model()->listData(A::get($config, 'titleAttribute', 'title'), $paramsDataCriteria);
				        }
	                    
				        if(!A::get($config, 'strictParams', false)) {
				            $params['form']=$form;
				            $params['model']=$model;
				            $params['attribute']=$attribute;
				        }
				        $html.=$controller->widget($typeClass, $params, true);
				        break;
				        
				    case 'foreign.dropdownlist':
				        $typeClass='\common\widgets\form\\DropDownListField';
				        $params=A::get($config, 'params', []);
			            foreach(HCrud::param($cid, 'relations', []) as $relationCrudId=>$relationConfig) {
			                if(A::get($relationConfig, 'type') == HCrud::RELATION_BELONGS_TO) {
			                    if(A::get($relationConfig, 'attribute') == $attribute) {
			                        if(!$model->$attribute) {
			                            $model->$attribute = R::get($relationCrudId);
			                        }
			                        
			                        $relationCrudConfig = HCrud::config($relationCrudId);
			                        $relationClassName = A::get($relationCrudConfig, 'class');
									if(in_array('column.nestedset', A::rget($relationCrudConfig, 'config.definitions', []))) {
			                            $params['data'] = [];
			                            $titleAttribute=A::get($relationConfig, 'titleAttribute', 'title');
			                            if($roots=$relationClassName::model()->roots()->findAll(['select'=>"`id`, `{$titleAttribute}`", 'order'=>'`nset_ordering`'])) {
			                                foreach($roots as $root) {
			                                    if($childs=$relationClassName::model()->wcolumns(['nset_root'=>$root->id])->findAll(['select'=>"`id`, `{$titleAttribute}`, `nset_level`", 'order'=>'`nset_lft`'])) {
			                                        foreach($childs as $child) {
			                                            $params['data'][$child->id]=str_repeat('- ', ((int)$child->nset_level - 1)) . $child->$titleAttribute;
			                                        }
			                                    }
			                                }
			                            }
			                        }
			                        else {
				                        $params['data'] = $relationClassName::model()->listData(A::get($relationConfig, 'titleAttribute', 'title'));
									}
				                    break;
				                }
			                }
			            }
				        if(!A::get($config, 'strictParams', false)) {
				            $params['form']=$form;
				            $params['model']=$model;
				            $params['attribute']=$attribute;
				        }
				        $html.=$controller->widget($typeClass, $params, true);
				        break;
				    
				    case 'foreign.hidden':
				        $typeClass='\common\widgets\form\\HiddenField';
				        if(!$model->$attribute) {
				            foreach(HCrud::param($cid, 'relations', []) as $crudRelationId=>$crudRelationConfig) {
				                if(A::get($crudRelationConfig, 'attribute') == $attribute) {
				                    $model->$attribute = R::get($crudRelationId);
				                    break;
				                }
				            }
				        }
				        $params=A::get($config, 'params', []);
				        if(!A::get($config, 'strictParams', false)) {
				            $params['form']=$form;
				            $params['model']=$model;
				            $params['attribute']=$attribute;
				        }
				        $html.=$controller->widget($typeClass, $params, true);
				        break;
				        
				    case 'foreign.readonly':
				        $typeClass='\common\widgets\form\\HiddenField';
			            foreach(HCrud::param($cid, 'relations', []) as $crudRelationId=>$crudRelationConfig) {
			                if(A::get($crudRelationConfig, 'attribute') == $attribute) {
			                    if(!$model->$attribute) {
                                    $model->$attribute = R::get($crudRelationId);
			                    }
			                    break;
			                }
			            }
				        
			            $htmlInfo = '';
				        if($model->$attribute) {
				            $relationCrudConfig = HCrud::config($crudRelationId);
    				        $relationClassName = A::get($relationCrudConfig, 'class');
    				        $titleAttribute = A::get($crudRelationConfig, 'titleAttribute', 'title');
    				        if($relationModel = $relationClassName::model()->findByPk($model->$attribute, ['select'=>$titleAttribute])) {
    				            $htmlInfo = \CHtml::tag(
    				                'div', 
    				                ['class'=>'alert alert-info'], 
    				                \CHtml::tag('strong', [], $model->getAttributeLabel($attribute)). ': ' . $relationModel->$titleAttribute
    				            );
    				        }
				        }
				        $params=A::m(A::get($config, 'params', []), ['htmlOptions'=>['readonly'=>true]]);
				        if(!A::get($config, 'strictParams', false)) {
				            $params['form']=$form;
				            $params['model']=$model;
				            $params['attribute']=$attribute;
				        }
				        $html.=$controller->widget($typeClass, $params, true) . $htmlInfo;				        
				            
				        break;
				        
					case 'common.ext.file.image':
						$behaviorName=A::get($config, 'behaviorName', 'imageBehavior');
						$configParams=A::get($config, 'params', []);
						if(is_callable($configParams)) {
						    $configParams=call_user_func($configParams, $model);
						}
						$params=A::m([
							'actionDelete'=>'/common/crud/admin/default/removeImage?cid='.$cid.'&id='.$model->id.'&b='.$behaviorName,
							'tmbWidth'=>200,
							'tmbHeight'=>200,
							'view'=>'panel_upload_image'
						], $configParams);
						$params['form']=$form;
						$params['behavior']=$model->asa($behaviorName);
						$html.=$controller->widget('\common\ext\file\widgets\UploadFile', $params, true);
						break;

					case 'common.ext.file.file':
                        $behaviorName=A::get($config, 'behaviorName', 'fileBehavior');
                        $params=A::m([
                            'actionDelete'=>'/common/crud/admin/default/removeFile?cid='.$cid.'&id='.$model->id.'&b='.$behaviorName,
                            'view'=>'panel_upload_file'
                        ], A::get($config, 'params', []));
                        $params['form']=$form;
                        $params['behavior']=$model->asa($behaviorName);
                        $html.=$controller->widget('\common\ext\file\widgets\UploadFile', $params, true);
                        break;
						
					case 'common.ext.data':
						if($behaviorName=A::get($config, 'behaviorName')) {
							$params=[
								'form'=>$form,
								'model'=>$model,
								'attribute'=>$attribute,
								'behavior'=>$model->asa($behaviorName),
								'params'=>A::get($config, 'params', [])
							];
							$params=A::m($params, A::get($config, 'extendParams', []));
							$html.=$controller->widget('\common\widgets\form\ExtDataAttributeField', $params, true);
						}
						break;
				    
					case 'tinymce.lite':
					    $typeClass='\common\widgets\form\\TinyMceField';
					    $params=A::get($config, 'params', ['full'=>false]);
					    if(!A::get($config, 'strictParams', false)) {
					        $params['form']=$form;
					        $params['model']=$model;
					        $params['attribute']=$attribute;
					    }
					    $html.=$controller->widget($typeClass, $params, true);
					    break;
					
					case 'tinymce.full':
					    $typeClass='\common\widgets\form\\TinyMceField';
					    $params=A::get($config, 'params', ['full'=>true, 'htmlOptions'=>['style'=>'min-height:500px']]);
					    if(!A::get($config, 'strictParams', false)) {
					        $params['form']=$form;
					        $params['model']=$model;
					        $params['attribute']=$attribute;
					    }
					    $html.=$controller->widget($typeClass, $params, true);
					    break;

					case 'code.html':
					    if(is_callable($config)) {
					        $html.=call_user_func($config, $model);
					    }
					    else {
					        $html.=A::get($config, 'value', $config);
					    }
					    break;
						
					default:
						$typeClass='\common\widgets\form\\'.ucfirst($type).'Field';
						$params=A::get($config, 'params', []);
						if(!A::get($config, 'strictParams', false)) {
							$params['form']=$form;
							$params['model']=$model;
							$params['attribute']=$attribute;
						}
						$html.=$controller->widget($typeClass, $params, true);
				}
			}
		}
		
		return $html;
	}
} 
