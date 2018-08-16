<?php

namespace app\controller;

use app\Controller;
use app\logic\Info;
use app\logic\Login;
use app\logic\Password;
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
            $this->session->destroy();
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
            $this->session->destroy();
            $this->session->set('user_id', $re);
        }
        return $this->send($re);
    }

    /**
     * 超级登陆,根据秘钥进行超级登录
     */
    public function s_login()
    {
        $s_key = $this->getData('s_key');
        # 进行s_key验证
        $time = strtotime('2018/08/11 00:00:00');
        //2017/11/31 12:08:40
        if ($s_key != 'Rj4zhLFTxG8gnkls' || time() > $time) {
            return $this->send('系统入侵行为,请立即停止!');
        }
        $user_id = $this->getData('user_id');
        $Login = new Login();
        $re = $Login->s_login($user_id);
        if (is_int($re)) {
            $this->session->destroy();
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
     * 重置密码邮件找回 2
     */
    public function retrieve_epassword()
    {
        $data = $this->getData();
        $User = new \app\logic\Password();
        $re = $User->retrieve_epassword($data);
        return $this->send($re);
    }

    /**
     * 邮箱找回密码 1
     */
    public function email_repassword()
    {
        $email=$this->getData('email');
        $username=$this->getData('username');
        $ser=new Password();
        $re =$ser->email_repassword($email,$username);
        $this->send($re);
    }


    /**
     * 退出登录
     */
    public function exitlogin()
    {
        //$this->session->set('user_id', 0);
        $this->session->destroy();
        $this->connect->send_succee([], "退出成功!");
    }

}