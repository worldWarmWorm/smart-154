<?php

class ResizeHelper
{
    /**
     * Resize image
     * @static
     * @param $fullPath
     * @param $width
     * @param $height
     * @return mixed
     */
    public static function resize($fullPath, $width, $height, $top = false, $resize = false)
    {
        if (!$fullPath) {
            return $fullPath;
        }
        
        $filename = basename($fullPath);
        $path = dirname($fullPath);

        $fullPath = Yii::getPathOfAlias('webroot') . $path;

        if(!is_file($fullPath . DS . $filename)) return null;

        $salt = hash_file('md5', $fullPath . DS . $filename);

        $resizeFilename = $salt . '_' . $width . '_' . $height . '_' . $filename;

        $ih = new CImageHandler();

        if(!file_exists($fullPath . DS . $resizeFilename)) {
            $ih
                ->load($fullPath . DS . $filename);

            #if ($ih->getWidth() > $width && $ih->getHeight() > $height) {
                if(!$width || !$height || $resize) {
                    $ih->resize($width, $height);
                } else {
                    $ih->adaptiveThumb($width, $height, $top);
                }
            #}

            $ih->save($fullPath . DS . $resizeFilename, false, 95);
        }

        return $path . DS . $resizeFilename;

    }

    /**
     * Resize image
     * @static
     * @param $fullPath
     * @param $width
     * @param $height
     * @return mixed
     */
    public static function watermark($fullPath, $zoom = false, $force = false)
    {
        if (!$fullPath) {
            return $fullPath;
        }

        if (strpos($fullPath, "?")) {
            $fullPath = substr($fullPath, 0, strpos($fullPath, "?"));
        }

		$filename = basename($fullPath); 
        $path = dirname($fullPath);

        $fullPath = Yii::getPathOfAlias('webroot') . $path;

        if(!is_file($fullPath . DS . $filename)) return null;

        $salt = hash_file('md5', $fullPath . DS . $filename);

        $fileParts = explode('.', $filename);

        $resizeFilename = 'water_' . $salt . '.' . end($fileParts);

        $ih = new CImageHandler();

        if(!file_exists($fullPath . DS . $resizeFilename) || $force) {
            $ih
                ->load($fullPath . DS . $filename);

            $ih->watermark($_SERVER['DOCUMENT_ROOT'] . '/images/watermark.png', 0, 0, CImageHandler::CORNER_CENTER, $zoom);

            $ih->save($fullPath . DS . $resizeFilename, false, 95);
        }

        return $path . DS . $resizeFilename;
    }

	/**
     * Resize with watermark
     */
    public static function wresize($fullPath, $width, $height, $zoom=0.75, $force=true)
    {
        return static::watermark(static::resize($fullPath, $width, $height), $zoom, $force);
    }

    public static function watermarkModifier($fullPath, $force = false)
    {
        $originalPath = $fullPath;

        $watermark_file = Yii::getPathOfAlias('webroot.images').DS.'watermark.png';

        if (!$fullPath) {
            return $fullPath;
        }

        if (strpos($fullPath, "?")) {
            $fullPath = substr($fullPath, 0, strpos($fullPath, "?"));
        }

		$filename = basename($fullPath); 
        $path = dirname($fullPath);

        $fullPath = Yii::getPathOfAlias('webroot') . $path;

        if(!is_file($fullPath . DS . $filename)) return null;

        $salt = hash_file('md5', $fullPath . DS . $filename);

        $fileParts = explode('.', $filename);

        $resizeFilename = 'w2_' . $salt . '.' . end($fileParts);

        $ih = new CImageHandler();

        if (!file_exists($fullPath . DS . $resizeFilename) || $force) {

            if (!is_file($watermark_file) || !is_file($fullPath . DS . $filename)) {
                return $originalPath;
            }

            Yii::app()->setComponents(array(
                'imagemod'=>array('class'=>'application.extensions.imagemodifier.CImageModifier')
            ));

            $test = copy($fullPath . DS . $filename, $fullPath . DS . $resizeFilename);

            $image = Yii::app()->imagemod->load($fullPath . DS . $resizeFilename);
            $image->image_watermark = $watermark_file;
            $image->image_watermark_position = 'CC';
            $image->jpeg_quality = 100;
            $image->file_new_name_body = $image->file_src_name_body;
            $image->file_overwrite = true;
            $image->image_watermark_no_zoom_in=false;
            $image->image_watermark_no_zoom_out=false;

            $image->process($fullPath);
        }

        return $path . DS . $resizeFilename;
    }
}
