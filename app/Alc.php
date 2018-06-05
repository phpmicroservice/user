<?php

namespace app;

use app\logic\Alc as alcLogic;

/**
 * Class Alc
 * @package app
 */
class Alc extends Base
{
    public $user_id;
    public $serverTask = [
        'demo', 'server', 'index'
    ];

    /**
     *
     * beforeDispatch 在调度之前
     * @param \Phalcon\Events\Event $Event
     * @param \Phalcon\Mvc\Dispatcher $Dispatcher
     * @return
     */
    public function beforeDispatch(\Phalcon\Events\Event $Event, \pms\Dispatcher $dispatcher)
    {
        if (in_array($dispatcher->getTaskName(), $this->serverTask)) {
            # 进行服务间鉴权
            return true;
        }
        if (empty($dispatcher->session)) {
            $dispatcher->connect->send_error('没有初始化session!!', [], 500);
            return false;
        }
        # 进行rbac鉴权
        return true;
    }

}