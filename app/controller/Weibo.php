<?php

namespace app\controller;

use app\Controller;

/**
 * 消息 message
 * Class MessageController
 * @package app\controller
 * @property \logic\user\Message $MessageService
 */
class Weibo extends Controller
{
    public function initialize()
    {
        parent::initialize(); //TODO: Change the autogenerated stub
    }

    /**
     * 修改用户信息
     *
     */
    public function edit()
    {
        $data = $this->getData([
            'weibo' => ['post', 'weibo', 'string', 0],
        ]);
        $service = new \logic\user\Weibo();
        $re = $service->edit($this->user_id, $data);
        return $this->send($re);
    }

    public function info()
    {
        $service = new \logic\user\Weibo();
        $re = $service->info_user($this->user_id);
        return $this->send($re);
    }

}