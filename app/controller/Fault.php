<?php

namespace app\controller;

use app\logic\Proxy;
use app\logic\Server;

/**
 * Empty 不合法的请求而的处理
 */
class Fault extends \pms\Controller
{

    public function onInitialize($connect)
    {

    }

    /**
     * 不合法的控制器名字
     * @param $data
     */
    public function controller()
    {
        $this->connect->send_error('不存在的内容!', [], 404);
    }




}