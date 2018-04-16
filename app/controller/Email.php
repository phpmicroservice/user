<?php

namespace app\controller;

use app\Controller;

/**
 * 邮件相关控制器 email
 * Class EmailController
 * @package apps\home\controllers
 */
class Email extends Controller
{
    /**
     * 发送激活码
     * @return \Phalcon\Http\Response|\Phalcon\Http\ResponseInterface
     */
    public function send_security()
    {
        $email = $this->request->get('email', 'string', '');
        $service = new \logic\user\Email($this->user_id);
        $re = $service->send_security($email);
        return $this->send($re);
    }

    /**
     * 发送激活码
     * @return \Phalcon\Http\Response|\Phalcon\Http\ResponseInterface
     */
    public function send_relieve()
    {
        $service = new \logic\user\Email($this->user_id);
        $re = $service->send_relieve();
        return $this->send($re);
    }

    /**
     * 验证和解绑
     * @return \Phalcon\Http\Response|\Phalcon\Http\ResponseInterface
     */
    public function security_relieve()
    {
        $security = $this->request->get('security', 'string', uniqid());
        $service = new \logic\user\Email($this->user_id);
        $re = $service->security_relieve($security);
        return $this->send($re);
    }

    /**
     * 验证并绑定
     */
    public function security_check()
    {
        $security = $this->request->get('security', 'string', uniqid());
        $service = new \logic\user\Email($this->user_id);
        $re = $service->security_check($security);
        return $this->send($re);
    }

    /**
     * 获取邮件绑定信息
     * @return \Phalcon\Http\Response|\Phalcon\Http\ResponseInterface
     */
    public function info()
    {
        $service = new \logic\user\Email($this->user_id);
        $re = $service->info();
        return $this->send($re);
    }
}