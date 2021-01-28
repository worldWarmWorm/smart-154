<?php
/**
 * Created by JetBrains PhpStorm.
 * User: Alexok
 * Date: 20.05.11
 * Time: 18:12
 * To change this template use File | Settings | File Templates.
 */

class ItemImages extends CWidget
{
    public $model;
    public $item_id;
    public $view = 'site';
    public $countPerPage;

    public function run()
    {
        $criteria = new CDbCriteria();
        $criteria->condition = 'model = :model AND item_id = :item_id';
        $criteria->params['model']   = $this->model;
        $criteria->params['item_id'] = $this->item_id;
        $criteria->order = 'ordering';

        $imageProvider = new CActiveDataProvider('CImage', array(
            'criteria'=>$criteria,
            'pagination'=>array(
                'pageSize' => 32,
                'pageVar'=>'p'
            ),
        ));

        $images = CImage::model()->findAll($criteria);

        if ($images) {
            $this->render($this->view, compact('images', 'pages', 'imageProvider'));
        }
    }
}
