<?php

namespace app\logic;
/**
 * 任务
 * Class Task
 * @package app\logic
 */
class Task
{

    public function demo()
    {
        Trace::add('info', func_get_args());
    }

    public function timing($data)
    {
        # 计划任务
        if ($data['interval'] == 0) {
            #进行用户初始阿虎啊
            $User = new User();
            $User->init_user();

        }

    }

}