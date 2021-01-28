<?php
namespace crud\components;

use crud\components\factory;

class ClassLoader
{
    public static function register()
    {
        spl_autoload_register(['\crud\components\ClassLoader', 'autoload'], null, true);
    }
    
    public static function autoload($class)
    {
        // @TODO на данный момент, только для модели ActiveRecord 
        if(strpos(trim($class, '\\'), 'crud\models\\ar\\') === 0) {
            factory\ActiveRecord::load($class);
        }
    }
}