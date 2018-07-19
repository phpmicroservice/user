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
    /**
     * 用户是否存在
     */
    public function user_exist()
    {
        $user_id = $this->getData('user_id');
        $model = \app\model\user::findFirstById((int)$user_id);
        $this->connect->send_succee($model instanceof \app\model\user);
    }

    /**
     * 用户是否可以注册
     */
    public function user_vareg()
    {
        $data = $this->getData();
        $ser = new \app\logic\Reg();
        $re = $ser->validation($data);
        $this->connect->send_succee($re);
    }

}