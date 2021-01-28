<?php

define('MAX_IMAGE_WIDTH',500);
define('MAX_IMAGE_HEIGHT',500);
/**
 * Base class.
 */
class EJCropper
{
	/**
	 * @var integer JPEG image quality
	 */
	public $jpeg_quality = 100;
	/**
	 * @var integer PNG compression level (0 = no compression).
	 */
	public $png_compression = 5;
	/**
	 * @var integer The thumbnail width
	 */
	public $targ_w = 100;
	/**
	 * @var integer The thumbnail height
	 */
	public $targ_h = 100;
	/**
	 * @var string The path for saving thumbnails
	 */
	public $thumbPath;

	/**
	 * Get the cropping coordinates from post.
	 * 
	 * @param type $attribute The model attribute name used.
	 * @return array Cropping coordinates indexed by : x, y, h, w
	 */
	public function getCoordsFromPost($attribute)
	{
		$coords = array('x' => null, 'y' => null, 'h' => null, 'w' => null);
		foreach ($coords as $key => $value) {
			$coords[$key] = $_POST[$attribute . '_' . $key];
		}
		return $coords;
	}

	/**
	 * Crop an image and save the thumbnail.
	 * 
	 * @param string $src Source image's full path.
	 * @param array $coords Cropping coordinates indexed by : x, y, h, w
	 * @return string $thumbName Path of thumbnail.
	 */


	public function cropImage($product_id, array $cropCoordinates, $image)
	{

		#var_dump($cropCoordinates); die;
		$image = '.'.$image;
		
		$image = Yii::app()->image->load($image);

		$image->crop($cropCoordinates['w'], $cropCoordinates['h'], $cropCoordinates['y'], $cropCoordinates['x']); #->rotate(-45)->quality(75)->sharpen(20);
		$image->save( 'images/product/'. $product_id . '_s.' . $image->ext ); // or $image->save('images/small.jpg');
	}




}