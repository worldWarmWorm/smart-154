<?php
namespace common\components\events;

use common\components\helpers\HArray as A;

/**
 * Расширенный класс \CEvent
 *
 */
class Event extends \CEvent
{
    /**
     * Получить значение параметра события
     * @param string $name имя параметра
     * @param mixed $default значение по умолчанию, если параметра не найден.
     * @return mixed
     */
    public function getParam($name, $default=null)
    {
        return A::get($this->params, $name, $default);
    }
    
    /**
     * Установка значения параметра события
     * @param string $name имя параметра
     * @param mixed $value значение
     */
    public function setParam($name, $value)
    {
        $this->params[$name]=$value;
    }
}