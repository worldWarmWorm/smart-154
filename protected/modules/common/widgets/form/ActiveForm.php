<?php
namespace common\widgets\form;

use common\components\helpers\HYii as Y;
use common\components\helpers\HArray as A;
use common\components\helpers\HHash;

class ActiveForm extends \common\components\base\Widget
{
    /**
     * Модель
     * @var \CModel
     */
    public $model;
    
    /**
     * Идентификатор формы.
     * По умолчанию (NULL) будет сгенерирован автоматически.
     * @var string|null
     */
    public $id=null;
    
    /**
     * Имя атрибута идентфикатора формы. 
     * По умолчанию "afid". Если передано false, 
     * поле отображено не будет.
     * @var string|false 
     */
    public $attributeFormId='afid';
    
    /**
     * Атрибуты модели, для отображения в форме.
     * В формате [attribute], где attribute - имя атрибута;
     * @var []
     */
    public $attributes=[];
    
    /**
     * Типы полей ввода для атрибутов модели, отображаемых в форме.
     * По умолчанию тип поля ввода атрибута "textField".
     * Может быть передано в форматах:
     * - [attribute=>callable], где callable это function($widget, $form, $attribute).
     * - [attribute=>type], где type может принимать значения
     * соотвествующего метода отбражения поля \CActiveForm. 
     * Доступны дополнительные типы:
     *  "phone" - номер телефона по маске "+7 ( 999 ) 999 - 99 - 99"
     *  "masked" - поле с маской, маска передается в параметрах 
     *  ActiveForm::$htmlOptions['attributeOptions'][attribute]['input']['mask']=маска для виджета \CMaskedTextField
     *  ActiveForm::$htmlOptions['attributeOptions'][attribute]['input']['jmask']=маска для плагина jQuery.mask
     * @var []
     */
    public $types=[];
    
    /**
     * Группы полей.
     * Если передано, отображение полей будет выполнено из данный конфигурации.
     * Каждый элемент группы является массивом вида:
     * [
     *  "htmlOptions"=>[] дополнительные HTML атрибуты для тэга <fieldset>
     *  "legend"=>string заголовок группы
     *  "legendOptions"=>[] дополнительные HTML атрибуты для заголовка группы <lenend>
     *  "attributes"=>[]|callable массив атрибутов, отображаемых в группе, либо callable
     *  функция генерации HTML кода группы function($widget, $form, $fieldset).
     *  "render"=>callable функция отрисовки группы function($widget, $form, $fieldset).
     * ]
     * @var [][]
     */
    public $fieldsets=[];
    
    /**
     * Имя атрибута модели согласия на обработку персональных данных
     * @var string
     */
    public $privacyAttribute='privacy';
    
    /**
     * Подпись подтверждения согласия на обработку персональных данных
     * Может быть передано callable значение function($widget, $form)
     * По умолчанию (FALSE) блок согласия на обработку персональных данных
     * отображен не будет.
     * @var string|callable|false 
     */
    public $privacyLabel=false;
    
    /**
     * Дополнительные параметры для виджета \CActiveForm
     * @var array
     */
    public $formOptions=[];
    
    /**
     * Отображать общую сводку ошибок.
     * По умолчанию (TRUE) отобржать
     * @var boolean
     */
    public $errorSummary=true;
	public $errorSummaryHeader=null;
    public $errorSummaryFooter=null;
    public $errorSummaryOptions=[];
    
    /**
     * Дополнительные HTML атрибуты для формы
     * {@inheritDoc}
     * @see \common\components\base\Widget::$htmlOptions
     * Разрешены дополнительные атриубты:
     * "rowTag" - тэг обертки поля
     * "rowOptions" - дополнительные HTML атрибуты для тэга обертки поля
     * "submitTag" - тэг обертки кнопки отправки формы
     * "submitOptions" - дополнительные HTML атрибуты для тэга обертки кнопки отправки формы
     * "attributeOptions" - дополнительные HTML атрибуты для атрибутов модели,
     * имеет вид:
     *  attribute=>[ // "attribute" - имя атрибута модели
     *      "label"=>[] дополнительные HTML атрибуты для <label>
     *      "input"=>[] дополнительные HTML атрибуты для поля ввода
     *      "error"=>[] дополнительные HTML атрибуты для ошибки
     *      "render"=>callback обработчик вывода function($widget, $form, $attribute)
     *  ]
     */
    public $htmlOptions=[];    
    
    /**
     * Подпись кнопки отправки формы.
     * Если передано FALSE, кнопка отображатся не будет.
     * Может быть передано значение типа callable вида
     * function($widget, $form)
     * @var string|false|callable
     */
    public $submitLabel='Отправить';
    
    /**
     * дополнительные HTML атрибуты для кнопки отправки формы
     * @var array
     */
    public $submitOptions=[];

	/**
     * {@inheritDoc}
     * @see \common\components\base\Widget::$tag
     */
    public $tag=null;
    
    /**
     * {@inheritDoc}
     * @see \common\components\base\Widget::$tagOptions
     */
    public $tagOptions=[];
    
    /**
     * {@inheritDoc}
     * @see \common\components\base\Widget::$view
     */
    public $view='active_form';

	/**
     * Шорткод тела формы
     * @var string
     */
    public $formBodyShortCode='#FORM#';
    
    /**
     * Шорткод тэга открытия формы
     * @var string
     */
    public $formBeginShortCode='#BEGINFORM#';
    
    /**
     * Шорткод тэга закрытия формы
     * @var string
     */
    public $formEndShortCode='#ENDFORM#';    
    
    /**
     * Индекс формы, требуется для автогенерации идентификатора формы.
     * @var integer
     */
    private static $index=0;

    /**
     * 
     * {@inheritDoc}
     * @see \CWidget::init()
     */
    public function init()
    {
        ob_start();
        
        parent::init();
    }
    
    /**
     * {@inheritDoc}
     * @see \common\components\base\Widget::run()
     */
    public function run()
    {
        
        if(!$this->model) {
            return false;
        }
        
        static::$index++;

		$widgetContent=ob_get_clean();
        
        $viewContent=$this->render($this->view, $this->params, true);
        
		$isOnlyViewContent=true;
        if(preg_match("/(?={$this->formBeginShortCode})/", $widgetContent)) {
            if(preg_match('/^(.*?)(<form[^>]+?>)(.*?)$/Dsxi', $viewContent, $m)) {
                $widgetContent=preg_replace("/{$this->formBeginShortCode}/", $m[2], $widgetContent);
                $viewContent=$m[1].$m[3];
				$isOnlyViewContent=false;
            }
        }
        
        if(preg_match("/(?={$this->formEndShortCode})/", $widgetContent)) {
            if(preg_match('#^(.*?)(</form>)(.*?)$#Dsxi', $viewContent, $m)) {
                $widgetContent=preg_replace("/{$this->formEndShortCode}/", $m[2], $widgetContent);
                $viewContent=$m[1].$m[3];
				$isOnlyViewContent=false;
            }
        }
        
        if(preg_match("/(?={$this->formBodyShortCode})/", $widgetContent)) {
            $widgetContent=preg_replace("/{$this->formBodyShortCode}/", $viewContent, $widgetContent);
        }
		elseif($isOnlyViewContent) {
            $widgetContent=$viewContent;
        }
        
        echo $widgetContent;
    }
    
    /**
     * Получить индекс формы
     * @return integer
     */
    public function getIndex()
    {
        return static::$index;
    }
    
    /**
     * Получить идентфикатор формы
     * @return string|NULL
     */
    public function getFormId()
    {
        if(!$this->id) {
            $this->id=\CHtml::modelName($this->model) . '__form-' . $this->getIndex();            
        }
        return $this->id;
    }
    
    /**
     * Получить дополнительные HTML атрибуты для формы
     * без дополнительных атрибутов виджета.
     * @return []
     */
    public function getFormHtmlOptions()
    {
        $htmlOptions=$this->htmlOptions;
        foreach(['rowTag', 'rowOptions', 'attributeOptions', 'submitTag', 'submitOptions'] as $attribute) {
            if(A::existsKey($htmlOptions, $attribute)) {
                unset($htmlOptions[$attribute]);
            }
        }
        return $htmlOptions;
    }
    
    /**
     * Отрисовать тэг открытия блока поля формы
     * @param array $htmlOptions дополнительные HTML атрибуты для тэга открытия
     * @param string|null атрибут модели, для которого отрисовывается тэг открытия
     */
    public function renderRowOpenTag($htmlOptions=[], $attribute=null)
    {
		if($tag=A::get($this->htmlOptions, 'rowTag')) {
            if(is_callable($tag)) {
                call_user_func_array($tag, [A::m(A::get($this->htmlOptions, 'rowOptions', []), $htmlOptions), $attribute]);
            }
            else {
                $rowOptions=A::m(A::get($this->htmlOptions, 'rowOptions', []), $htmlOptions);
                if($attribute) {
                    $rowOptions['class']=trim(A::get($rowOptions, 'class', '') . ' row__' . $attribute);
                }   
                echo \CHtml::openTag($tag, $rowOptions);
            }
        }
    }
    
    /**
     * Отрисовать тэг закрытия блока поля формы
     */
    public function renderRowCloseTag()
    {
        if($tag=A::get($this->htmlOptions, 'rowTag')) {
            echo \CHtml::closeTag($tag);
        }
    }
    
    /**
     * Отрисовать тэг открытия блока кнопки отправки формы
     * @param array $htmlOptions дополнительные HTML атрибуты для тэга открытия
     */
    public function renderSubmitOpenTag($htmlOptions=[])
    {
        if($tag=A::get($this->htmlOptions, 'submitTag')) {
            echo \CHtml::openTag($tag, A::m(A::get($this->htmlOptions, 'submitOptions', []), $htmlOptions));
        }
    }
    
    /**
     * Отрисовать тэг закрытия блока кнопки отправки формы
     */
    public function renderSubmitCloseTag()
    {
        if($tag=A::get($this->htmlOptions, 'submitTag')) {
            echo \CHtml::closeTag($tag);
        }
    }
    
    /**
     * Отрисовать тэг <label> для атрибута
     * @param \CActiveForm $form объект формы
     * @param string $attribute имя атрибута
     */
    public function renderAttributeLabel($form, $attribute)
    {
        if(!A::get($this->getAttributeOptions($attribute), 'render')) {
			$htmlOptions=A::get($this->getAttributeOptions($attribute), 'label', []);
            if($htmlOptions !== false) {
                echo $form->labelEx($this->model, $attribute, A::get($this->getAttributeOptions($attribute), 'label', []));
            }
        }
    }
    
    /**
     * Отрисовать тэг ошибки атрибута 
     * @param \CActiveForm $form объект формы
     * @param string $attribute имя аттрибута
     */
    public function renderAttributeError($form, $attribute)
    {
        if(!A::get($this->getAttributeOptions($attribute), 'render')) {
            echo $form->error($this->model, $attribute, A::get($this->getAttributeOptions($attribute), 'error', []));
        }
    }
    
    /**
     * Отрисовать поле формы атрибута.
     * Тип атрибута по умолчанию "textField"
     * @param \CActiveForm $form объект формы
     * @param string $attribute имя атрибута
     * @param string|callable|null $type переопределение типа атрибута. 
     *  (string) имя метода отбражения поля \CActiveForm. Доступны дополнительные типы:
     *  "phone" - номер телефона по маске "+7 ( 999 ) 999 - 99 - 99"
     *  (callable) фукнция произвольной отрисовки вида 
     *  function($widget, $form, $attribute).
     *  (null, по умолчанию) тип не переопределен.
     */
    public function renderAttribute($form, $attribute, $type=null)
    {
        if($type === null) {
            if(isset($this->types[$attribute])) {
                $type=$this->types[$attribute];
            }
            else {
                $type='textField';
            }
        }
        
        if(is_callable($type)) {
            echo call_user_func_array($type, [$this, $form, $attribute]);
        }
        else {
            $this->renderRowOpenTag([], $attribute);
            if($render=A::get($this->getAttributeOptions($attribute), 'render')) {
                echo call_user_func_array($render, [$this, $form, $attribute]);
            }
            else {
                switch($type) {
                    case 'checkBox':
                        echo $form->$type($this->model, $attribute, $this->getAttributeHtmlOptions($attribute));
                        $this->renderAttributeLabel($form, $attribute);
                        break;
                    case 'phone':
                        $this->renderAttributeLabel($form, $attribute);
                        $this->renderPhoneField($form, $attribute);
                        break;
                    case 'masked':
                        $this->renderAttributeLabel($form, $attribute);
                        $this->renderMaskedField($form, $attribute);
                        break;
                    default:
                        $this->renderAttributeLabel($form, $attribute);
                        echo $form->$type($this->model, $attribute, $this->getAttributeHtmlOptions($attribute));
                }
                $this->renderAttributeError($form, $attribute);
            }
            $this->renderRowCloseTag();
        }
    }
    
    /**
     * Получить дополнительные HTML атрибуты для поля ввода.
     * @param string $attribute имя атрибута
     * @return []
     */
    public function getAttributeHtmlOptions($attribute)
    {
        return  A::get($this->getAttributeOptions($attribute), 'input', []);
    }
    
    /**
     * Отрисовать тип поля "Номер телефона"
     * @param \CActiveForm $form объект формы
     * @param string $attribute
     */
    public function renderPhoneField($form, $attribute)
    {
        $this->model->$attribute=preg_replace('/^(\d)(\d{3})(\d{3})(\d{2})(\d{2})$/', '+$1 ( $2 ) $3 - $4 - $5', $this->model->$attribute);
        
        $this->htmlOptions['attributeOptions'][$attribute]['input']['mask']='+7 ( ___ ) ___ - __ - __';
        $this->htmlOptions['attributeOptions'][$attribute]['input']['jmask']='+7 ( 999 ) 999 - 99 - 99';
        
        $this->renderMaskedField($form, $attribute);
    }
    
	/**
     * Отрисовать тип поля "Маска"
     * @param \CActiveForm $form объект формы
     * @param string $attribute
     */
    public function renderMaskedField($form, $attribute)
    {
        $jsId=HHash::ujs();
        
        $htmlOptions=$this->getAttributeHtmlOptions($attribute);
        $htmlOptions['data-js']=$jsId;
        
        $mask=$htmlOptions['mask']=A::get($htmlOptions, 'mask');
        $jmask=$htmlOptions['jmask']=A::get($htmlOptions, 'jmask');
        unset($htmlOptions['mask']);
        unset($htmlOptions['jmask']);
        
        if(!empty($mask) && !empty($jmask)) {
            $this->owner->widget('\CMaskedTextField', [
                'model'=>$this->model,
                'attribute'=>$attribute,
                'mask'=>$mask,
                'htmlOptions'=>$htmlOptions
            ]);
            
            Y::js($jsId, ";(function(){var p=jQuery('[data-js={$jsId}]');p.mask('{$jmask}');p.val(p.attr('value'));})();", \CClientScript::POS_READY);
        }
        else {
            echo $form->textField($this->model, $attribute, $htmlOptions);
        }
    }
    
    /**
     * Получить дополнительно заданные параметры для атрибута
     * в ActiveForm::$htmlOptions['attributeOptions']
     * @access protected
     * @param string $attribute имя атрибута
     * @return []
     */
    public function getAttributeOptions($attribute)
    {
        return A::get(A::get($this->htmlOptions, 'attributeOptions', []), $attribute, []);
    }
}
