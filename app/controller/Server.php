<?php

namespace app\controller;

use app\Controller;
use app\validator\user_exist;
use pms\Dispatcher;


/**
 * Server 服务控制器,提供可供其他服务调用的接口
 * Class CollectController
 * @package app\controller
 */
class Server extends Controller
{
    public function userExit()
    {
        $user_id = $this->getData('user_id');
        $model = \app\model\user::findFirstById((int)$user_id);
        $this->connect->send_succee($model instanceof \app\model\user);
    }


    /**
     * 在执行路由之前
     * @return bool|void
     */
    public function beforeExecuteRoute(Dispatcher $dispatch)
    {
        $key = $this->connect->accessKey;
        output([APP_SECRET_KEY, $this->connect->getData(), $this->connect->f], 'verify_access');
        if (!verify_access($key, APP_SECRET_KEY, $this->connect->getData(), $this->connect->f)) {
            $this->connect->send_error('accessKey-error', [], 412);
            return false;
        }
    }

}