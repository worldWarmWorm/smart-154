<form id="search" action="<?=$this->createUrl('search/index')?>" role="search">
    <div class="input-group">
        <?$this->widget('CAutoComplete',
            array(
                'model'=>'Search',
                'name'=>'q',
                'id'=>'search_2',
                'url'=>array('search/autocomplete'),
                'minChars'=>2,
                'max'=>20,
                'value'=>Yii::app()->request->getParam('q'),
                'htmlOptions'=>array('class'=>'form-control', 'placeholder'=>"Поиск по товарам")
            )
        )?>
        <span class="input-group-btn">
            <input type="submit" class="btn btn-default" value="Найти">
        </span>
    </div>
</form>
