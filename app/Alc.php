<?php

namespace app;

use pms\Dispatcher;


/**
 * 权限验证类
 * Class Alc
 * @package app
 */
class Alc extends Base
{
    public $user_id;
    public $serverTask = [
        'server', 'transaction', 'service'
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
        # 公共权限
        if (in_array($dispatcher->getTaskName(), $this->publicTask)) {
            # 公共权限
            return true;
        }
        if (in_array($dispatcher->getTaskName(), $this->serverTask)) {
            # 进行服务间鉴权
            return $this->server_auth($dispatcher);
        }

        # 进行rbac鉴权
        if ($this->rbac_auth($dispatcher)) {
            # 登录即可访问
            return true;
        }

        $dispatcher->connect->send_error('没有权限!!', [$dispatcher->session->get('user_id')], 401);
        return false;
    }

    /**
     * 服务间的鉴权
     * @return bool
     */
    private function server_auth(Dispatcher $dispatcher)
    {
        $key = $dispatcher->connect->accessKey??'';
        if (!\pms\verify_access($key, APP_SECRET_KEY, $dispatcher->connect->getData(), $dispatcher->connect->f)) {
            $dispatcher->connect->send_error('accessKey-error', [], 412);
            return false;
        }
        return true;
    }

    /**
     * Rbac鉴权
     * @param Dispatcher $dispatcher
     * @return bool
     */
    private function rbac_auth(Dispatcher $dispatcher)
    {
        $dispatcher->connect->f;
        $u=$dispatcher->session->get('user_id',0);
        $c=$dispatcher->getTaskName();
        $a=$dispatcher->getActionName();
        $re =$this->proxyCS->request_return('rbac','/index/alc',['u'=>$u,'c'=>$c,'a'=>$a]);
        if($re['e']){
            return false;
        }
        return $re['d'];
    }


}