<?php

namespace app\controller;
/**
 * 配置处理
 */
class Demo extends \pms\Controller
{

    public function index($data)
    {
        $this->connect->send_succee([
            $data,"我是用户分组"
        ]);
    }
}