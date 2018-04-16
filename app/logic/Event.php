<?php

namespace app\logic;

/**
 * 模块模块事件执行器
 * Class Event
 * @package logic\user
 */
class Event extends \core\Event\EventInjectable implements \core\Event\EventInterface
{


    private static $_instance = null;#单例对象
    protected $module_name = 'user';

    private function __construct()
    {
        $this->config = new EventConfig();
        $this->p_module = \logic\Common\Event::getInstance();
    }


    public static function getInstance()
    {
        if (is_null(self::$_instance)) {
            self::$_instance = new self();
        }
        return self::$_instance;
    }


}