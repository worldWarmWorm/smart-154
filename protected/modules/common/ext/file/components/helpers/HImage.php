<?php
namespace common\ext\file\components\helpers;

use common\components\helpers\HArray as A;
use common\components\helpers\HHtml;
use common\components\helpers\HFile;

class HImage
{
    const RESIZE_DIR='webroot.upload.image_resize';
    
    /**
     * Получить ссылку на превью изображение
     * @param $src string URL орининального изображения.
     * Может быть передан путь на физический файл изображения на сервере.
     * @param $width integer ширина превью-изображения.
     * @param $height integer ширина превью-изображения.
     * @param $options array дополнительные параметры изображения.
     * Доступы следующие параметры:
     * "proportional" => true - пропорциональное преобразование
     * "adaptive" => false - адаптивное изображение
     * "adaptive_top" => true - адаптивное изображение обрезать снизу
     * "absolute" => false - возвратить абсолютный путь
     * "schema" => "" - HTTP схема для абсолютного пути 
     * "path" => HImage::RESIZE_DIR - алиас пути, куда сохранить превью-изображение.
     * "forcy" => false - принудительно обновить превью изображение.
     * "default" => false - изображение по умолчанию, если оригинальное изображение не найдено.
     * Может быть передан массив параметров для метода HHtml::phSrc()
     * Если передано true, то будет возвращено изображение, генерируемое методом HHtml::phSrc().
     * 
     * @return string|null URL превью изображения, либо null если файл оригинального изображения 
     * не найден, либо передано пустое значение ширины или высоты.
     */
    public static function tmb($src, $width, $height, $options=[])
    {
        if(!is_file($src)) {
            $src=HFile::path([\Yii::getPathOfAlias('webroot'), $src]);
        }
        
        if(is_file($src)) {
            if(!empty($width) && !empty($height)) {
                $path=\Yii::getPathOfAlias(A::get($options, 'path', HImage::RESIZE_DIR));
                HFile::mkDir($path, 0755, true);
                if(is_dir($path)) {
                    $ext=pathinfo($src, PATHINFO_EXTENSION);
                    $hash=hash_file('md5', $src);
                    $tmb="{$width}_{$height}_{$hash}.{$ext}";
                    $tmbfile=HFile::path([$path, $tmb]);
                    if(!is_file($tmbfile) || A::get($options, 'forcy', false)) {
                        $image=\Yii::app()->ih->load($src);
                        if(A::get($options, 'adaptive', false)) {
                            $image=$image->adaptiveThumb($width, $height, A::get($options, 'adaptive_top', true));
                        }
                        else {
                            $image=$image->thumb($width, $height, A::get($options, 'proportional', true));
                        }
                        $image->save($tmbfile);
                    }
                    
                    $url=HFile::pathToUrl($tmbfile);
                    
                    if(A::get($options, 'absolute', false)) {
                        return \Yii::app()->createAbsoluteUrl($url, [], A::get($options, 'schema', ''));
                    }
                    
                    return $url;
                }
            }
        }
        else {
            $default=A::get($options, 'default', false);
            if($default !== false) {
                if(is_string($default)) {
                    return $default;
                }
                
                return HHtml::phSrc(A::m(
                    ['w'=>$width, 'h'=>$height], 
                    A::toa($default)
                ));
            }
        }
            
        return null;
    }
    
    /**
     * Получить <img> превью-изображения
     * @param $src string URL орининального изображения.
     * Может быть передан путь на физический файл изображения на сервере.
     * @param $htmlOptions array дополнительные атрибуты для <img>.
     * Если переданы атрибуты "width" и "height" будет произведена попытка 
     * создания файла превью-изображения.
     * Также может быть передан атрибут "options"=>array для дополнительных 
     * параметров превью-изображения (подробнее в методе HImage::tmb()).
     * @param $time bool добавить временную метку к URL превью-изображения.
     * @return string
     */
    public static function img($src, $htmlOptions=[], $time=false)
    {
        $width=A::get($htmlOptions, 'width');
        $height=A::get($htmlOptions, 'height');
        $options=[];
        if(A::existsKey($htmlOptions, 'options')) {
            $options=A::get($htmlOptions, 'options', []);
            unset($htmlOptions['options']);
        }
        
        if(!empty($width) && !empty($height)) {
            if($tmb=static::tmb($src, $width, $height, $options)) {
                $src=$tmb;
            }
        }
        
        return \CHtml::image($src . ($time ? ('?'.time()) : ''), '', $htmlOptions);
    }
}