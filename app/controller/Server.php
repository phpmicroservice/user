<?php

namespace app\controller;

use app\Controller;
use app\validator\user_exist;


/**
 * Server 服务控制器,提供可供其他服务调用的接口
 * Class CollectController
 * @package app\controller
 */
class Server extends Controller
{
    public function user_exit()
    {
        $user_id = $this->getData('user_id');
        $model = \app\model\user::findFirstById($user_id);
        $this->connect->send_succee($model instanceof user);
    }
}