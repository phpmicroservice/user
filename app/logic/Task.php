<?php
/**
 * Created by PhpStorm.
 * User: dongasai
 * Date: 17-9-16
 * Time: 下午6:15
 */

namespace logic\user;


use core\Sundry\Trace;

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