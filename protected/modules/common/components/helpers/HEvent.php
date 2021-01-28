<?php
/**
 * Класс-помощник событий
 * 
 */
namespace common\components\helpers;

use common\components\helpers\HArray as A;
use common\components\Event;

class HEvent
{
	/**
     * Остановить дальнейшее выполнение события
     * @var string
     */
    const STOP=false;

    /**
     * Получить компонент события
     * @param \CComponent|null $component объект компонента.
     * По умолчанию (NULL) будет возвращен объект компонента 
     * \common\components\Event::i()
     * @return \CComponent|\common\components\Event
     */
    public static function getComponent($component=null)
    {
        if($component === null) {
            $component=Event::i();
        }
        return $component;
    }
    
    /**
     * Регистрация событий из конфигурации
     * @param array $config конфигурация событий, вида 
     * [eventName=>(array)eventHandlers] или [eventName=>callable]
     * @param \CComponent|null $component объект компонента, для которого 
     * будут зарегистрированы события. По умолчанию (NULL), будет использован
     * объект компонента \common\components\Event::i() 
     */
    public static function registerByConfig($config, $component=null)
    {
        foreach($config as $name=>$handlers) {
            $handlers=A::toa($handlers);
            foreach($handlers as $handler) {
                static::register($name, $handler, null, $component);
            }
        }
    }
    
    /**
     * Регистрация событий по псевдониму пути к файлу событий
     * @param string $alias псевдониму пути к файлу событий
     * @param boolean $includeExtraEvents регистрировать дополнительные события
     * из папки указанной в параметре $extraEventsDir. 
     * По умолчанию (false) не регистрировать.
     * @param string $extraEventsDir имя директории с дополнительными событиями.
     * По умолчанию "events".
     */
    public static function registerByAlias($alias, $includeExtraEvents=false, $extraEventsDir='events')
    {
        $eventsFile=\Yii::getPathOfAlias($alias) . '.php';
        if(is_file($eventsFile)) {
            static::registerByConfig(HFile::includeFile($eventsFile, []));
        }
        
        if($includeExtraEvents) {
            static::registerExtra(dirname($eventsFile), $extraEventsDir);
        }
    }
    
    /**
     * Регистрация событий по полному пути к файлу событий
     * @param string $filename полный путь к файлу событий
     * @param boolean $includeExtraEvents регистрировать дополнительные события
     * из папки указанной в параметре $extraEventsDir. 
     * По умолчанию (false) не регистрировать.
     * @param string $extraEventsDir имя директории с дополнительными событиями.
     * По умолчанию "events".
     */
    public static function registerByFile($filename, $includeExtraEvents=false, $extraEventsDir='events')
    {
        if(is_file($filename)) {
            static::registerByConfig(HFile::includeFile($filename, []));
        }
        
        if($includeExtraEvents) {
            static::registerExtra(dirname($filename), $extraEventsDir);
        }
    }
    
    /**
     * Регистрация дополнительных событий
     * @param string $basePath путь к директории, в которой находится
     * директория с дополнительными событиями.
     * @param string $extraEventsDir имя директории с дополнительными событиями.
     * По умолчанию "events".
     */
    public static function registerExtra($basePath, $extraEventsDir='events')
    {
        $extraEvents=HFile::getFiles(HFile::path([$basePath, $extraEventsDir]), true);
        foreach($extraEvents as $extraEventFile) {
            $eventName=basename($extraEventFile, '.php');
            if(strpos($eventName, 'on') === 0) {
                static::register($eventName, HFile::includeFile($extraEventFile));
            }
        }
    }
    
    /**
     * Регистрация события
     * @param string $name имя события
     * @param callable $handler обработчик события
     * @param integer $priority приоритет события. Чем выше, тем раньше 
     * будет вызван обработчик. По умолчанию (null) будет произведена попытка
     * получения приоритета из конфигурации обработчика события. 
     * @param \CComponent|null $component объект компонента.
     * По умолчанию (NULL) будет возвращен объект компонента 
     * \common\components\Event::i()
     */
    public static function register($name, $handler, $priority=null, $component=null)
    {
        if($priority === null) {
            if(is_array($handler)) {
                if(is_numeric(reset($handler))) {
                    $priority=array_shift($handler);
                    $handler=array_shift($handler);
                }
            }
        }
        
        static::getComponent($component)->attachEventHandler($name, $handler, (int)$priority);
    }
    
    /**
     * Запуск события
     * @param string $name имя события
     * @param array $params параметры для обработчиков события
     * @param \CComponent|null $component объект компонента для
     * которого будет инициализирован вызов событий. 
     * По умолчанию (NULL) будет возвращен объект компонента 
     * \common\components\Event::i()
     * @return \CEvent
     */
    public static function raise($name, $params=[], $component=null)
    {
        $event=new \common\components\events\Event;
        $event->params=$params;
        
        static::getComponent($component)->raiseEvent($name, $event);
        
        return $event;
    }
}
