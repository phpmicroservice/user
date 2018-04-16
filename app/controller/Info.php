<?php

namespace apps\user\controllers;

use app\Controller;

use core\Sundry\Trace;


/**
 * 用户信息控制器 Info
 * Class InfoController
 * @package apps\user\controllers
 */
class Info extends Controller
{
    public function initialize()
    {
        parent::initialize(); // TODO: Change the autogenerated stub
    }

    /**
     * 修改用户信息
     *
     */
    public function edit()
    {
        $data = $this->getData([
            'nickname' => ['post', 'nickname', 'string', ''],
            'headimg' => ['post', 'headimg', 'int', 0],
            'gender' => ['post', 'gender', 'int', 0],
            'birthday' => ['post', 'birthday', 'string', '1994-06-09'],
            'personalized' => ['post', 'personalized', 'string', '签名'],
            'area' => ['post', 'area', 'int', 0],
        ]);
        $service = new \logic\user\Info();
        $re = $service->edit($this->user_id, $data);
        return $this->restful_return($re);
    }

    /**
     * 用户信息
     * @return \Phalcon\Http\Response|\Phalcon\Http\ResponseInterface
     */
    public function info()
    {
        $service = new \logic\user\Info();
        $re = $service->info_user($this->user_id);
        Trace::add('info', $re);
        return $this->restful_return($re);
    }

    public function p_info()
    {
        $user_id = $this->request->get('user_id', 'int', 0);
        $service = new \logic\user\Info();
        $re = $service->info_user($user_id);
        return $this->restful_return($re);
    }

}