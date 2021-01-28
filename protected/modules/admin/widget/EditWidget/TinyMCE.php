<?php
class TinyMCE extends CInputWidget {

    public $editorSelector='mceEditorArea';
    public $editorSelectorId=null;
    public $model;
    public $attribute   = null;
    public $full        = true;
    public $htmlOptions = array();
    public $height = null;
    public $registerJs=true;
    public $disableToolbarCode=false;
    public $initInstanceCallback=null;

	/**
     * Включить полный режим панели инструментов визуального редактора
     * @var string
     */
    public $enableClassicFull=false;

    public function run()
    {
        
        if ($this->model instanceof CModel == false)
            throw new CException('Model not valid');

        if($this->height===null) $this->height=$this->full ? 400 : 250;

        $class = 'mceEditor ' . $this->editorSelector;

        if (isset($this->htmlOptions['class']))
            $class .= ' '.$this->htmlOptions['class'];

        if(empty($this->editorSelectorId)) $this->editorSelectorId=uniqid('id');
        echo CHtml::activeTextArea($this->model, $this->attribute, array('class'=>$class,'id'=>$this->editorSelectorId));

        $this->_registerJs();
        $this->_submitScript();
    }


    private function _registerJs()
    {
        $assetsUrl = Yii::app()->getAssetManager()->publish(Yii::getPathOfAlias('admin.widget.EditWidget.assets'));
        $cs        = Yii::app()->clientScript;

		if($this->registerJs) {
        	$cs->registerScriptFile($assetsUrl . '/tinymce.min.js', CClientScript::POS_BEGIN);
/*        $cs->registerCssFile($assetsUrl . '/css/editor-ui.css');*/
        }
	        $type = $this->full ? ((D::cms('tinymce_full_toolbars')||$this->enableClassicFull)?'classic-full':'full') : 'lite';
    	    $id   = 'tiny_mce_init_'. uniqid($type);
        
        	$cs->registerScript($id, $this->_initScript($type, $assetsUrl), CClientScript::POS_BEGIN);
		
    }

	private function _initScript($type, $assets)
	{
        $file = dirname(__FILE__).DS.'types'.DS.$type.'.js';
	    return $this->renderFile($file, array(
	    	'assets'=>$assets, 
	    	'ymapDialog'=>Yii::app()->createUrl('admin/default/ymapDialog'),
	    	'editorSelector'=>$this->editorSelector,
	    	'editorSelectorId'=>$this->editorSelectorId,
	    	'height'=>(int)$this->height
	    ), true);
	}
	
    private function _submitScript()
    {
        //$field_name = CHtml::resolveName($this->model, $this->attribute);
        //$field_id   = CHtml::getIdByName($field_name);
		$field_id=$this->editorSelectorId;

        $js = "$('#$field_id').parents('form').submit(function(){tinyMCE.get('$field_id').save();})";

        Yii::app()->clientScript->registerScript('tiny_mce_submit_'. $field_id, $js);
    }

}
