<?php

namespace app\controller;

use app\Controller;

/**
 *  实名认证 realauth
 * Class EmailController
 * @package apps\home\controllers
 */
class Realauth extends Controller
{
    /**
     * 提交认证信息
     * @return \Phalcon\Http\Response|\Phalcon\Http\ResponseInterface
     */
    public function submit()
    {
        $data = $this->getData([
            'name' => ['post', 'name', 'string', '姓名'],
            'id_card' => ['post', 'id_card', 'string', '3702222222'],
            'img1' => ['post', 'img1', 'int', 0],
            'img2' => ['post', 'img2', 'int', 0],
            'img3' => ['post', 'img3', 'int', 0],
        ]);
        $service = new \logic\user\Realauth($this->user_id);
        $re = $service->submit($data);
        return $this->send($re);
    }

    /**
     * 信息
     * @return \Phalcon\Http\Response|\Phalcon\Http\ResponseInterface
     */
    public function info()
    {
        $service = new \logic\user\Realauth($this->user_id);
        $re = $service->info();
        return $this->send($re);
    }

    public function p_info()
    {
        $user_id = $this->request->get('user_id', 'int', 0);
        $service = new \logic\user\Realauth($user_id);
        $re = $service->p_info($user_id);
        return $this->send($re);
    }

}