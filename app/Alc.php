<?php

namespace app;

use pms\Dispatcher;

/**
 * Class Alc
 * @package app
 */
class Alc extends Base
{
    public $user_id;
    public $serverTask = [
        'server'
    ];
    public $publicTask = [
        'demo', 'index'
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

        if (in_array($dispatcher->getTaskName(), $this->publicTask)) {
            # 公告权限
            return true;
        }


        if (in_array($dispatcher->getTaskName(), $this->serverTask)) {
            # 进行服务间鉴权
            return $this->server_auth($dispatcher);
        }


        if (empty($dispatcher->session)) {
            $dispatcher->connect->send_error('没有初始化session!!', [], 500);
            return false;
        }
        # 进行rbac鉴权
        return true;
    }


    /**
     * 服务间的鉴权
     * @return bool
     */
    private function server_auth(Dispatcher $dispatcher)
    {
        $key = $dispatcher->connect->accessKey??'';
        output([APP_SECRET_KEY, $dispatcher->connect->getData(), $dispatcher->connect->f], 'verify_access');
        if (!verify_access($key, APP_SECRET_KEY, $dispatcher->connect->getData(), $dispatcher->connect->f)) {
            $dispatcher->connect->send_error('accessKey-error', [], 412);
            return false;
        }
        return true;
    }


}