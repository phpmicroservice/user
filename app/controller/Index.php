<?php

namespace apps\user\controllers;

use app\Controller;
use logic\user\Info;
use logic\user\service\Login;
use logic\user\Tel;

/**
 * Class IndexController
 * @package apps\user\controllers
 */
class Index extends Controller
{
    public function initialize()
    {
        parent::initialize(); // TODO: Change the autogenerated stub
    }

    /**
     * 用户公共信息
     */
    public function user_info()
    {
        $user_id = $this->request->get('user_id', 'int', 0);
        $user = new Info();
        $ser = $user->p_info($user_id);
        return $this->restful_return($ser);
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
        return $this->restful_return($ser);
    }


    /**
     * 查找用户
     * @return \Phalcon\Http\Response|\Phalcon\Http\ResponseInterface
     */
    public function find_user()
    {
        $username = $this->request->get('username', 'string', '');
        $service = new \logic\user\User();
        $re = $service->find_user($username);
        return $this->restful_return($re);
    }

    /**
     * 登录方法
     */
    public function login()
    {
        if ($this->request->isPost()) {
            $data['username'] = $this->request->getPost('username');
            $data['password'] = $this->request->getPost('password');
            $data['captcha'] = $this->request->getPost('img_captcha');
            $Login = new \logic\user\service\Login();
            $re = $Login->loginAction($data);
            return $this->restful_return($re);
        } else {
            return $this->restful_error('error');
        }
    }

    public function tel_login()
    {
        if ($this->request->isPost()) {
            $data['tel'] = $this->request->getPost('tel');
            $data['password'] = $this->request->getPost('password');
            $data['captcha'] = $this->request->getPost('img_captcha');
            $Login = new \logic\user\service\Login();
            $re = $Login->tel_login($data);
            return $this->restful_return($re);
        } else {
            return $this->restful_error('error');
        }

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
        return $this->restful_return($re);
    }


    /**
     * 注册方法
     */
    public function reg()
    {
        $pa = [
            'nickname' => ['post', 'nickname', 'string', 'nickname'],
            'username' => ['post', 'username', 'string', ''],
            'tel' => ['post', 'tel', 'string', ''],
            'password' => ['post', 'password', 'string', ''],
            'password2' => ['post', 'password2', 'string', ''],
            'email' => ['post', 'email', 'string', ''],
            'phone_captcha' => ['post', 'captcha', 'string', ''],
        ];
        $data = $this->getData($pa);
        $Reg = new \logic\user\service\Reg();
        $re = $Reg->regAction($data);
        return $this->restful_return($re);
    }

    /**
     * 判断是否登录
     * @return type
     */
    public function is_login()
    {
//        $this->request->getHTTPReferer()
        $uid = \logic\user\User::is_login();
        if ($uid) {
            //成功
            return $this->restful(ReturnMsg::create(200, '_success', ['user_id' => $uid, 'role' => \logic\user\User::role()]));
        } else {
            return $this->restful(ReturnMsg::create(401, '_no login'));
        }
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
        $User = new \logic\user\Password();
        $re = $User->retrieve_password($data);
        return $this->restful_return($re);
    }


    /**
     * 退出登录
     */
    public function exit_login()
    {
        $this->session->set('user_id', 0);
        $session_id = $this->session->getId();
        $cachekey = \tool\Arr::array_md5([
            $session_id, 'userid'
        ]);
        //写
        $this->RCache->delete($cachekey);
        return $this->restful(ReturnMsg::create());
    }

}