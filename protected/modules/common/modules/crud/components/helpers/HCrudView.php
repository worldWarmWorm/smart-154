<?php
/**
 * Класс-помощник для шаблонов отображения модуля "CRUD"
 */
namespace crud\components\helpers;

use common\components\helpers\HYii as Y;
use common\components\helpers\HArray as A;
use common\components\helpers\HRequest as R;
use common\components\helpers\HHtml;
use common\components\exceptions\ConfigException;
use crud\components\helpers\HCrud;
use common\components\helpers\HHash;

class HCrudView
{
	/**
	 * Получить идетификатор для \CGridView
     * @param string $cid идентифкатор настроек CRUD для модели.
	 * @param string $pagePath путь к настройкам текущей страницы в конфигурации CRUD для текущей модели.
	 * По умолчанию "crud.index".
	 */
	public static function getGridId($cid, $pagePath='crud.index')
	{
		return HCrud::param($cid, $pagePath.'.gridView.id', 'grid'.HHash::ucrc32(HCrud::param($cid, 'class')));		
	}
	
	/**
	 * Получить значение параметра "tabs" для виджета zii.widgets.jui.CJuiTabs
	 * @param string $cid идентифкатор настроек CRUD для модели.
	 * @param string $pagePath путь к настройкам текущей страницы в конфигурации CRUD для текущей модели.
	 * @param array $properties дополнительные параметры для параметра "tabs" (в формате параметров виджета).
	 * @param array $options дополнительные параметры для генерации вкладки. 
	 * Может содержать следующие параметры:
	 * "controller" - контроллер, для которого будет вызван renderPartial для генерации контента вкладки.
	 * По умолчанию \Yii::app()->getController();
	 * 
	 * "model" - объект модели, который будет передан в шаблон отображения вкладки.
	 * По умолчанию NULL.
	 * Может быть передано TRUE, в этом случае, будет создана автоматически новый объект модели;
	 * Может быть передан (string|integer) id (идентификатор модели) - будет произведена попытка 
	 * получить модель по идентификатору;
	 * Может быть передан массив [className] имя класса, будет создана модель данного класса; 
	 * Может быть передан массив [className, id] будет произведена попытка получить модель 
	 * класса className с идентификатором id;
	 * 
	 * "form" - объект формы \CActiveForm. По умолчанию будет создан новый объект \CActiveForm;
	 * 
	 * "formView" - шаблон отображения вкладки по умолчанию. По умолчанию 
	 * "crud.modules.admin.views.default._tabs_form".
	 * @return array
	 */
	public static function getTabs($cid, $pagePath, $properties=[], $options=[])
	{
		$tabs=[];
		
		$tabsConfig=A::m(
			$a1=HCrud::param($cid, 'crud.tabs', [], true),
			$a2=HCrud::param($cid, $pagePath.'.tabs', [], true)
		);
		if(is_callable($tabsConfig)) {
		    $tabsConfig=call_user_func($tabsConfig);
		}
		if(!empty($tabsConfig)) {
			foreach($tabsConfig as $idx=>$config) {
				$tabConfig=HCrud::param($cid, $config, [], true);
				if(!A::get($tabConfig, 'disabled', false)) {
					if($title=trim(A::get($tabConfig, 'title', $idx))) {
						$tabId=A::get($tabConfig, 'id', 'tab-'.$idx);
						if($ajax=A::get($tabConfig, 'ajax')) {
							$tabs[$title]=['id'=>$tabId, 'ajax'=>$ajax];
						}
						else {
							$formView=A::get($options, 'formView', 'crud.modules.admin.views.default._tabs_form');
							if($view=A::get($tabConfig, 'view', $formView)) {
								$controller=A::get($options, 'controller', \Yii::app()->getController());
								
								if(!($form=A::get($options, 'form'))) $form=new \CActiveForm();
															
								$model=A::get($options, 'model');
								if($model === true) $model=HCrud::getById($cid);
								elseif(is_array($model)) {
									if(!$className=A::get($model, 0)) {
										ConfigException::e();
									}
									if($pk=A::get($model, 1)) {
										$model=$className::model()->findByPk($pk);
									}
									else {
										$model=new $className();
									}
								}
								elseif(is_string($model) || is_integer($model)) {							
									$model=HCrud::getById($cid, $model);
								}
								
								$attributes=A::get($tabConfig, 'attributes', []);
								
								$render=true;
								if($onBeforeRender=A::get($tabConfig, 'onBeforeRender')) {
									$render=call_user_func_array($onBeforeRender, compact('cid', 'form', 'model', 'attributes'));
								}
								if($render) {
									$content=$controller->renderPartial(
										$view,
										compact('cid', 'form', 'model', 'attributes'), 
										true,
										A::get($tabConfig, 'processOutput', false)
									);
								
									$tabs[$title]=['id'=>$tabId, 'content'=>$content];
								}
							}
						}
					}	
				}
			}
		}
		
		return A::m($tabs, $properties); 
	}
	
	/**
	 * Подготовить конфигурацию для \CGridView.
	 * @param string $cid идентифкатор настроек CRUD для модели.
	 * @param &array $gridViewConfig массив параметров \CGridView
	 */
	public static function prepareGridView($cid, &$gridViewConfig)
	{
		if(!isset($gridViewConfig['columns'])) return;

		$tbtn=Y::ct('CommonModule.btn', 'common');
		$tlbl=Y::ct('CommonModule.labels', 'common');
		
		$urlUpdate=HCrud::getConfigUrl(
			$cid,
			'crud.update.url', 
			'/common/crud/admin/default/update', 
			['cid'=>$cid, 'id'=>'php:$data->id'],
			's'
		);
		$urlDelete=HCrud::getConfigUrl(
			$cid,
			'crud.delete.url', 
			'/common/crud/admin/default/delete', 
			['cid'=>$cid, 'id'=>'php:$data->id'],
			's'
		);
				
		$buttons=[
			'class'=>'\CButtonColumn',
			'template'=>'{update}{delete}',
			'updateButtonImageUrl'=>false,
			'deleteButtonImageUrl'=>false,
			'buttons'=>[
				'update'=>[
					'label'=>'<span class="glyphicon glyphicon-pencil"></span> ',
					'url'=>'\Yii::app()->createUrl("'.$urlUpdate[0].'", '.$urlUpdate[1].')',
					'options'=>['title'=>$tbtn('edit')],
				],
				'delete' => [
					'label'=>'<span class="glyphicon glyphicon-remove"></span> ',
					'url'=>'\Yii::app()->createUrl("'.$urlDelete[0].'", '.$urlDelete[1].')',
					'options'=>['title'=>$tbtn('remove')],
				]
			]
		];

		if(!empty($gridViewConfig['filter']) && is_callable($gridViewConfig['filter'])) {
		    $gridViewConfig['filter']=call_user_func($gridViewConfig['filter']);
		}
		
		if($crudRelations = HCrud::param($cid, 'relations')) {
		    $relationButtonsCount = 0;
		    foreach($crudRelations as $relationCrudId=>$relationConfig) {
		        $relationType = A::get($relationConfig, 'type', HCrud::RELATION_DEFAULT);
		        if($relationType == HCrud::RELATION_HAS_MANY) {
    		        if($relationCrudConfig = HCrud::config($relationCrudId)) {
    		            $relationButtonsCount++;
    		            
    		            $urlRelationItems=HCrud::getConfigUrl(
            		        $cid,
            		        'crud.index.url',
            		        '/common/crud/admin/default/index',
    		                ['cid'=>$relationCrudId, $cid=>'php:$data->id'],
            		        's'
            		    );
    		            $buttons['buttons'][$relationCrudId . '_items']=[
            		        'label'=>'<span class="glyphicon glyphicon-list" style="margin-right:5px"></span>',
            		        'url'=>'\Yii::app()->createUrl("'.$urlRelationItems[0].'", '.$urlRelationItems[1].')',
            		        'options'=>['title'=>A::rget($relationCrudConfig, 'crud.index.title')],
            		    ];
    		            $buttons['buttons'][$relationCrudId . '_items'] = A::m(
    		                $buttons['buttons'][$relationCrudId . '_items'], 
    		                A::rget($relationConfig, 'config.crud.index.gridView.buttons.' . $relationCrudId . '_items', [])
    		            );
    		            $buttons['template'] = '{' . $relationCrudId . '_items}' . $buttons['template'];
    		            $buttons['htmlOptions']['style'] = 'width: '.(($relationButtonsCount * 20) + 50).'px';
    		        }
    		    }
    		    elseif($relationType == HCrud::RELATION_BELONGS_TO) {
    		        foreach($buttons['buttons'] as $buttonId=>$buttonConfig) {
    		            if(!empty($buttonConfig['url'])) {
    		                if($buttonId == 'update') {
    		                    $urlUpdate=HCrud::getConfigUrl(
    		                        $cid,
    		                        'crud.update.url',
    		                        '/common/crud/admin/default/update',
    		                        ['cid'=>$cid, 'id'=>'php:$data->id', $relationCrudId=>(int)R::get($relationCrudId)],
    		                        's'
    		                    );
    		                    $buttons['buttons'][$buttonId]['url'] = '\Yii::app()->createUrl("'.$urlUpdate[0].'", '.$urlUpdate[1].')';
    		                }
    		                elseif($buttonId == 'delete') {
    		                    $urlDelete=HCrud::getConfigUrl(
    		                        $cid,
    		                        'crud.delete.url',
    		                        '/common/crud/admin/default/delete',
    		                        ['cid'=>$cid, 'id'=>'php:$data->id', $relationCrudId=>(int)R::get($relationCrudId)],
    		                        's'
    		                    );
    		                    $buttons['buttons'][$buttonId]['url'] = '\Yii::app()->createUrl("'.$urlDelete[0].'", '.$urlDelete[1].')';
    		                }
    		            }
    		        }
    		    }
		    }
		}
		
		foreach($gridViewConfig['columns'] as $idx=>$column) {
			if($column=='crud.buttons') {
				$gridViewConfig['columns'][$idx]=$buttons;
				continue;
			}
			
			$relationCrudId = null;
			if($column=='column.id') {
			    $column = ['type'=>'column.id'];
			}
			elseif($column=='column.title') {
			    $column = ['type'=>'column.title'];
			}
			elseif($column=='common.ext.sort') {
                $column = ['type'=>'common.ext.sort'];
            }
			elseif(strpos($column, 'column.relation.') === 0) {
			    $relationCrudId = substr($column, 16);
			    $column = ['type'=>'column.relation'];
			}
			
			$attributeTitle = A::get($column, 'attributeTitle', 'title');
			if(!empty($column['attributeTitle'])) {
			    unset($column['attributeTitle']);
			}
			
			if(is_array($column)) {
				if($type=A::get($column, 'type')) {
					if(is_array($type)) {
						$typeKey=key($type);
						$typeParams=$type[$typeKey];
					}
					else {
						$typeKey=$type;
						$typeParams=[];
					}
					
					if(strpos($typeKey, 'column.relation.') === 0) {
					    $relationCrudId = substr($typeKey, 16);
					    $typeKey = 'column.relation';
					}
					
					switch($typeKey) {
					    case 'column.id':
					        $gridViewConfig['columns'][$idx] = A::m([
					           'name'=>'id',
					           'header'=>'#',
					           'headerHtmlOptions'=>['style'=>'width:5%']
					        ], A::get($column, 'params', []));
					        break;
					        
					    case 'column.title':
					        $columnTitle = [
								'name'=>A::get($column, 'name', $attributeTitle),
    					        'header'=>A::get($column, 'header', 'Наименование'),
    					        'type'=>'raw',
    					        'value'=>'"<strong>".CHtml::link(\CHtml::encode($data->'.A::get($column, 'attributePrintTitle', $attributeTitle).'),'.$buttons['buttons']['update']['url'].')."</strong>"',
								'headerHtmlOptions'=>A::get($column, 'headerHtmlOptions', []),
    					        'htmlOptions'=>A::get($column, 'htmlOptions', []),
					        ];
					        
					        $info = '';
					        if(!empty($column['info'])) {
					            if(is_callable($column['info'])) {
					                $column['info']=call_user_func($column['info']);
					            }
					            $i=1;
					            $info = ' . "<br/><small>" . ';
					            foreach($column['info'] as $infoLabel=>$infoValue) {
					                if(strpos($infoLabel, ':expr:') !== 0) $infoLabel="\"{$infoLabel}\"";
					                else $infoLabel=substr($infoLabel, 6);
					                $v='$v'.($i++);
					                $info .= "(({$v}={$infoValue}) ? (\"<b>\" . {$infoLabel} . \":</b> {{$v}}<br/>\") : \"\") .";
					            }
					            $info .= '"</small>"';
					            $columnTitle['value'] .= $info;
					            unset($column['info']);
					        }

							if(isset($column['attributePrintTitle'])) {
                                unset($column['attributePrintTitle']);
                            }
					        
					        $gridViewConfig['columns'][$idx] = A::m($columnTitle, A::get($column, 'params', []));
					        break;
					    
					    case 'column.relation':	
					        $columnRelation = [
    					        'name'=>$attributeTitle,
    					        'header'=>A::get($column, 'header', 'Наименование'),
    					        'type'=>'raw',
    					        'value'=>'"<strong>".CHtml::link(\CHtml::encode($data->'.$attributeTitle.'),'.$buttons['buttons']['update']['url'].')."</strong>"'
					        ];
					        
					        $info = '';
					        if(!empty($column['info'])) {
					            if(is_callable($column['info'])) {
					                $column['info']=call_user_func($column['info']);
					            }
					            $i=1;
					            $info = ' . "<br/><small>" . ';
					            foreach($column['info'] as $infoLabel=>$infoValue) {
					                if(strpos($infoLabel, ':expr:') !== 0) $infoLabel="\"{$infoLabel}\"";
					                else $infoLabel=substr($infoLabel, 6);
					                $v='$v'.($i++);
					                $info .= "(({$v}={$infoValue}) ? (\"<b>\" . {$infoLabel} . \":</b> {{$v}}<br/>\") : \"\") .";
					            }
					            $info .= '"</small>"';
					            $columnTitle['value'] .= $info;
					            unset($column['info']);
					        }
					        
					        if(!empty($relationCrudId)) {
				                $columnRelation['value']='"<strong>".CHtml::link($data->'.$attributeTitle.','.$buttons['buttons'][$relationCrudId . '_items']['url'].')."</strong>"';
				            }
					        $columnRelation['value'] .= $info;
				            $gridViewConfig['columns'][$idx] = A::m($columnRelation, A::get($column, 'params', []));
					        break;
					    
						case 'crud.buttons':
							$gridViewConfig['columns'][$idx]=A::m($buttons, A::get($column, 'params', []));
							break;
							
						case 'common.ext.active':
							$column['type']='raw';
							$behaviorName=A::get($typeParams, 'behaviorName', 'activeBehavior');
							$columnOptions=[
        						'headerHtmlOptions'=>['style'=>'width:10%'],
        						'htmlOptions'=>['style'=>'text-align:center'],
        						'value'=>'$this->grid->owner->widget("\common\\\\ext\active\widgets\InList", [
        							"behavior"=>$data->asa("'.$behaviorName.'"),
        							"changeUrl"=>"/common/crud/admin/default/changeActive?cid='.$cid.'&id={$data->id}&b='.$behaviorName.'",
					        		"cssMark"=>"unmarked",
					        		"cssUnmark"=>"marked",
					        		"wrapperOptions"=>["class"=>"mark"]
        						], true)'
         					];
							$gridViewConfig['columns'][$idx]=A::m($columnOptions, $column);      
							break;
					   
						case 'common.ext.published':
						    $column['type']='raw';
						    $behaviorName=A::get($typeParams, 'behaviorName', 'publishedBehavior');
						    $columnOptions=[
						        'headerHtmlOptions'=>['style'=>'width:10%'],
						        'htmlOptions'=>['style'=>'text-align:center'],
						        'value'=>'$this->grid->owner->widget("\common\\\\ext\active\widgets\InList", [
        							"behavior"=>$data->asa("'.$behaviorName.'"),
									"changeUrl"=>"/common/crud/admin/default/changeActive?cid='.$cid.'&id={$data->id}&b='.$behaviorName.'",
					        		"cssMark"=>"unmarked",
					        		"cssUnmark"=>"marked",
					        		"wrapperOptions"=>["class"=>"mark"]
        						], true)'
						    ];
						    $gridViewConfig['columns'][$idx]=A::m($columnOptions, $column);
						    break;
							
						case 'common.ext.file.image':
							$column['type']='raw';
							$behaviorName=A::get($typeParams, 'behaviorName', 'imageBehavior');
							$width=A::get($typeParams, 'width', 120);
							$height=A::get($typeParams, 'height', 120);
							$proportional=A::get($typeParams, 'proportional', true)?'true':'false';
							$htmlOptions=A::toPHPString(A::get($typeParams, 'htmlOptions', []));
							$default=A::get($typeParams, 'default', true);
							if($default === true) {
								$default=\CHtml::image(HHtml::pImage(['w'=>$width, 'h'=>$height, 't'=>$tlbl('nophoto'), 'sz'=>12]));
							}
							elseif($default === false) {
								$default='&nbsp;';
							}
							else {
								$default=\CHtml::image($default);
							}
							$columnOptions=[
        						'headerHtmlOptions'=>['style'=>'width:15%'],
        						'htmlOptions'=>['style'=>'text-align:center'],
        						'value'=>'$data->'.$behaviorName.'->img('.$width.','.$height.','.$proportional.','.$htmlOptions.')?:"'.HHtml::q($default).'"' 
         					];
							$gridViewConfig['columns'][$idx]=A::m($columnOptions, $column);      
							break;

						case 'common.ext.sort':
						    $column['type']='raw';
						    $attribute=A::get($column, 'name', 'sort');
						    
						    $jsId='btn_eav_groups_update_grid_after_save_sort_'.$attribute;
						    Y::jsCore('cookie');
						    Y::js($jsId,
						        ';window.commonExtSortCrudUpdateAfterSave_'.$attribute.'='.(A::get($typeParams,'updateAfterSave',!empty($_COOKIE[$jsId]))?'true':'false')
						        .';$(document).on("click","#'.$jsId.'",function(e){let checked=!window.commonExtSortCrudUpdateAfterSave_'.$attribute.';
if(checked){$(e.target).removeClass("btn-danger").addClass("btn-success").text("Обновлять")}else{$(e.target).removeClass("btn-success").addClass("btn-danger").text("Не обновлять");}
$.cookie("'.$jsId.'",checked?1:0,{path:"/"});window.commonExtSortCrudUpdateAfterSave_'.$attribute.'=checked;});',
						        \CClientScript::POS_READY
						        );
						    
						    $columnOptions=[
						        'header'=>A::get($column, 'header', 'Сорт.') . \CHtml::tag('button', [
						            'type'=>'button',
						            'id'=>$jsId,
						            'class'=>'btn btn-xs btn-' . (empty($_COOKIE[$jsId]) ? 'danger' : 'success'),
						            'data-toggle'=>'button',
						            'style'=>'margin-left:5px;font-size:11px',
						            'title'=>'Обновлять при сохранении сортировки',
						        ], (empty($_COOKIE[$jsId]) ? 'Не обновлять' : 'Обновлять')),
						        'headerHtmlOptions'=>['style'=>'width:20%;text-align:center'],
						        'htmlOptions'=>['style'=>'text-align:center'],
						        'value'=>function($data) use ($cid, $column, $typeParams, $attribute) {
						        $behaviorName=A::get($typeParams, 'behaviorName', 'sortFieldBehavior');
						        $html=\CHtml::tag('span', ['class'=>'js-common-ext-sort', 'style'=>'cursor:pointer'], $data->$attribute)
						        . \CHtml::tag(
						            'div',
						            ['class'=>'js-common-ext-sort-form', 'style'=>'display:none'],
						            \CHtml::numberField('sort', $data->$attribute, ['data-id'=>$data->id, 'type'=>'number', 'step'=>1, 'class'=>'form-control w100 js-common-ext-sort-input'])
						            );
						        Y::css('crud.common.ext.sort', '.js-common-ext-sort:hover{border:1px solid rgba(0,0,0,0.4);padding:1px 3px;border-radius:3px;background:rgba(0,0,0,0.05);}');
						        Y::js(
						            'crud.common.ext.sort',
						            ';$(document).on("click",".js-common-ext-sort",function(e){let el=$(e.target);el.hide();el.siblings(".js-common-ext-sort-form").show();});'
						            . ';$(document).on("blur mouseleave",".js-common-ext-sort-input",function(e){let el=$(e.target),frm=el.parent(),txt=frm.siblings(".js-common-ext-sort");
if(!isNaN(+el.val())){if(txt.text()!=el.val()){$.post("/common/crud/admin/default/saveSortField?cid='.$cid.'&b='.$behaviorName.'&id="+el.data("id"),{v:+el.val()},function(r){
if(r.success){let grid=el.parents(".grid-view:first");if(grid&&window.commonExtSortCrudUpdateAfterSave_'.$attribute.'){$.fn.yiiGridView.update(grid.attr("id"));}else{frm.removeClass("has-error").addClass("has-success");setTimeout(function(){frm.hide();txt.text(el.val());txt.show();},400);}}else{frm.removeClass("has-success").addClass("has-error");}},"json");}
else{frm.hide();txt.show();}}else{frm.removeClass("has-success").addClass("has-error");}e.preventDefault();return false;});',
						            \CClientScript::POS_READY
						            );
						        return $html;
						        }
						    ];
						    
						$gridViewConfig['columns'][$idx]=A::m($columnOptions, $column);
						break;
					}
				}
			}
		}
		
		if(array_key_exists('columns.sort', $gridViewConfig)) {
			if($columnsSort=A::get($gridViewConfig, 'columns.sort')) {
				$gridViewConfig['columns']=A::sort(
					$gridViewConfig['columns'], 
					$columnsSort, 
					!A::get($gridViewConfig, 'columns.sort.filter', false),
					A::get($gridViewConfig, 'columns.sort.reverse', false)
				);
			}
			unset($gridViewConfig['columns.sort']);
		}		
		if(array_key_exists('columns.sort.filter', $gridViewConfig)) {
			unset($gridViewConfig['columns.sort.filter']);			
		}		
		if(array_key_exists('columns.sort.reverse', $gridViewConfig)) {
			unset($gridViewConfig['columns.sort.reverse']);			
		}
	}
}
