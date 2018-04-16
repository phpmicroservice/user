<?php

namespace apps\user\controllers;

use app\Controller;
use core\ReturnMsg;
use core\Sundry\Trace;


/**
 * QQ互联的相关控制器
 * Class CollectController
 * @package apps\user\controllers
 */
class Qq extends Controller
{
    public function initialize()
    {
        parent::initialize(); // TODO: Change the autogenerated stub
        include_once WWW_DIR . '/extends/vendor/tc/qq_connect2_1/qqConnectAPI.php';
    }

    /**
     * zhu
     * @return \Phalcon\Http\Response|\Phalcon\Http\ResponseInterface
     */
    public function login()
    {
        $token = $this->request->get('token', 'string', 'a');
        $openid = $this->request->get('open_id', 'string', '0');
        include_once WWW_DIR . '/extends/vendor/tc/qq_connect2_1/qqConnectAPI.php';
        $qc = new \QC($token, $openid);
        $re = $qc->get_user_info();
        if ($re['ret'] == 0) {
            //
            $userService = new \logic\user\Qq();
            $re = $userService->login($openid);
        } else {
            return $this->restful_error($re['msg']);
        }
        return $this->restful_return($re);
    }

    /**
     * QQ关联
     */
    public function guanlian()
    {
        $token = $this->request->get('token', 'string', 'a');
        $openid = $this->request->get('open_id', 'string', '0');

        $qc = new \QC($token, $openid);
        $re = $qc->get_user_info();
        Trace::add('info', $re);
        if ($re['ret'] == 0) {
            $userService = new \logic\user\Qq();
            $re = $userService->relevance($openid, $this->user_id, $re);
            Trace::add('info', $re);
            return $this->restful_return($re);
        } else {
            return $this->restful_error($re['msg']);
        }
    }

    /**
     * 取消关联
     * @return \Phalcon\Http\Response|\Phalcon\Http\ResponseInterface
     */
    public function quxiao()
    {
        $userService = new \logic\user\Qq();
        $re = $userService->quxiao($this->user_id);
        Trace::add('info', $re);
        return $this->restful_return($re);
    }

    public function info()
    {
        $userService = new \logic\user\Qq();
        $re = $userService->info($this->user_id);
        Trace::add('info', $re);
        return $this->restful_return($re);

    }


    /**
     * 浏览器登陆
     * @return \core\type
     */
    public function web_login()
    {
        $qc = new \QC();
        $url = $qc->qq_login();
        $this->view->setVar('wait', 3);
        return $this->success(ReturnMsg::create(200, '正在跳转到登录', $url));
    }

    /**
     *
     */
    public function web_login_callback()
    {
        $qc = new \QC();
        $access_token = $qc->qq_callback();
        $openid = $qc->get_openid();
        $userService = new \logic\user\Qq();
        $re = $userService->login($openid);
        if (is_string($re)) {
            // 失败
            return $this->error(ReturnMsg::create(400, '登录是不该', $this->url->get('/')));
        } else {
            return $this->success(ReturnMsg::create(200, '正在跳转到登录', $this->url->get('/')));
        }


    }


}