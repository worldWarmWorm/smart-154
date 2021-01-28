<?php
namespace common\components;

/**
 * Компонент "Событие"
 * 
 */
class Event
{
    use \common\traits\Singleton;
   
    /**
     * Список зарегистированных событий
     * вида [eventName=>callable[]]
     * @var array
     */
    private $events=[];
    
    /**
     * Добавить обработчик события
     * @param string $name имя события
     * @param callable $handler обработчик события
     * @param integer $priority приоритет события. Чем выше, тем раньше 
     * будет вызван обработчик. По умолчанию приоритет равен 0 (нуль). 
     *  
     */
    public function attachEventHandler($name, $handler, $priority=0)
    {
        if(!isset($this->events[$name])) {
            $this->events[$name]=[];
        }
        
        $this->events[$name][]=[(int)$priority, $handler];
        
        usort($this->events[$name], function($a, $b) {
            if($a[0] > $b[0]) return -1;
            if($a[0] < $b[0]) return 1;
            return 0;
        });
    }
    
    /**
     * Запуск события
     * Если обработчик события возвращает строгое false,
     * дальнейшие обработчики не запускаются.
     * @param string $name имя события
     * @param \CEvent &$event объект события
     */
    public function raiseEvent($name, \CEvent &$event)
    {
        if(isset($this->events[$name])) {
            foreach($this->events[$name] as $config) {
                if(call_user_func_array($config[1], [&$event]) === false) {
                    break;
                }
            }
        }
    }
}
