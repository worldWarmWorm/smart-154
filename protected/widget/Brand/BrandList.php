<?php

class BrandList extends CWidget
{
    public $listClass = 'shop-menu';
    public $view = 'brand_list';
    public $select = 'title, alias';

    public function run()
    {
        $brands = Brand::model()->actived()->findAll(['select'=>$this->select, 'order'=>'title']);

        if(count($brands) < 1) return false;

        $this->render($this->view, compact('brands'));
    }
}
