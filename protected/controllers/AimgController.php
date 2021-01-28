<?php

class AimgController extends Controller
{
	public function actionAjaxcrop()
	{
		Yii::import('application.extensions.jcrop.EJCropper');
		$jcropper = new EJCropper();

		// get the image cropping coordinates (or implement your own method)
		$product_id  = Yii::app()->request->getPost('id');
		$image  = Yii::app()->request->getPost('image');

		$coords = array(
		'x'=> (int)Yii::app()->request->getPost('x'),
		'y'=> (int)Yii::app()->request->getPost('y'),
		'w'=> (int)Yii::app()->request->getPost('w'),
		'h'=> (int)Yii::app()->request->getPost('h'),
		);
		$jcropper->cropImage( $product_id, $coords, $image);
	}
}