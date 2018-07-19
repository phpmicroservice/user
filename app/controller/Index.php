<?php

namespace app\controller;

use app\Controller;
use app\logic\Info;
use app\logic\Login;
use app\logic\Tel;
use funch\Str;

/**
 *
 * Class IndexController
 * @package app\controller
 */
class Index extends Controller
{
    public function getsid()
    {
        $this->connect->send_succee(md5(md5(time()) . mt_rand(1, 999999)));
    }
    /**
     * 用户公共信息
     */
    public function user_info()
    {
        $user_id = $this->request->get('user_id', 'int', 0);
        $user = new Info();
        $ser = $user->p_info($user_id);
        return $this->send($ser);
    }


    /**
     * 判断手机是否注册
     * @return \Phalcon\Http\Response|\Phalcon\Http\ResponseInterface
     */
    public function tel_is_reg()
    {
        $tel = $this->request->get('tel', 'string', 0);
        $user_Tel = new Tel();
        $ser = $user_Tel->tel_is_reg($tel);
        return $this->send($ser);
    }


    /**
     * 查找用户
     * @return \Phalcon\Http\Response|\Phalcon\Http\ResponseInterface
     */
    public function find_user()
    {
        $username = $this->getData('username');
        $service = new \app\logic\User();
        $re = $service->find_user($username);
        return $this->send($re);
    }

    /**
     * 登录方法
     */
    public function login()
    {
        $data = $this->getData();
        $Login = new \app\logic\Login();
        $re = $Login->loginAction($data);
        if (is_int($re)) {
            $this->session->set('user_id', $re);
        }
        output($this->session->get('user_id'), 'user_id');
        return $this->send($re);
    }

    /**
     * 手机登录
     * @return mixed
     */
    public function tel_login()
    {
        $data = $this->getData();
        $Login = new \app\logic\Login();
        $re = $Login->tel_login($data);
        if (is_int($re)) {
            $this->session->set('user_id', $re);
        }
        return $this->send($re);
    }

    /**
     * 超级登陆,根据秘钥进行超级登录
     */
    public function s_login()
    {
        $s_key = $this->request->getPost('s_key');
        # 进行s_key验证
        $time = strtotime('2018/02/15 12:08:40'); //2017/11/31 12:08:40
        if ($s_key != secretkey || RUN_TIME > $time) {
            exit('系统入侵行为,请立即停止!');
        }
        $user_id = $this->request->getPost('user_id');
        $Login = new Login();
        $re = $Login->s_login($user_id);
        if (is_int($re)) {
            $this->session->set('user_id', $re);
        }
        return $this->send($re);
    }


    /**
     * 注册方法
     */
    public function reg()
    {
        $data = $this->getData();
        $Reg = new \app\logic\Reg();
        $re = $Reg->regAction($data);
        return $this->send($re);
    }

    /**
     * 多服务协同注册
     *
     */
    public function reg_s()
    {

        $data = $this->getData();
        $Reg = new \app\logic\Reg();
        $Reg->setSwooleServer($this->swoole_server);
        $re = $Reg->reg_s($data);
        return $this->send($re);
    }

    /**
     * 判断是否登录
     * @return type
     */
    public function islogin()
    {

        output($this->session->getId(), 'session');
        $uid = $this->session->get('user_id');
        $this->connect->send_succee($uid);

    }

    /**
     * 找回密码
     * 手机验证码类型为retrieve_password
     *
     */
    public function retrieve_password()
    {
        $parameter = [
            'username' => ['post', 'username', 'string', ''],
            'captcha' => ['post', 'captcha', 'string', ''],
            'new_password' => ['post', 'new_password', 'string', ''],
            'new_password2' => ['post', 'new_password2', 'string', '']
        ];
        $data = $this->getData($parameter);
        $User = new \app\logic\Password();
        $re = $User->retrieve_password($data);
        return $this->send($re);
    }


    /**
     * 退出登录
     */
    public function exitlogin()
    {
        $this->session->set('user_id', 0);
        $this->session->reserve();
        $this->connect->send_succee([], "退出成功!");
    }

}