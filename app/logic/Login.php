<?php

namespace app\logic;

use core\ReturnMsg;
use core\Sundry\Trace;
use logic\user\Event;
use app\model\user_tel;
use app\validator\user_exist;
use app\validator\user_forbid;

/**
 * Description of Login
 *
 * @author Dongasai
 */
class Login extends \app\Base
{

    /**
     * uid 登陆
     * @param $user_id
     * @return bool
     */
    public function s_login($user_id)
    {
        $validation = new  \pms\Validation();
        $validation->add_Validator('user_id', [
            'name' => user_exist::class,
        ]);
        $validation->add_Validator('username', [
            'name' => user_forbid::class,
            'message' => 'aa'
        ]);
        if (!$validation->validate(['user_id' => $user_id])) {
            return $validation->getMessage();
        }

        return $this->loginLater($user_id);

    }

    /**
     * 登录之后的操作
     */
    private function loginLater($user_id)
    {
        $this->session->set('user_id', $user_id);
        $session_id = $this->session->getId();
        $cachekey = \tool\Arr::array_md5([
            $session_id, 'userid'
        ]);
        //写
        $this->RCache->save($cachekey, $user_id, 604800);
        //进行登陆后操作
        $Event = Event::getInstance();
        $re = $Event->notice('loginLater', ['user_id' => $user_id], true);
        if ($re === false) {
            //执行 钩子 出错!
            return '_error-notice';
        }
    }

    /**
     * 手机登录
     */
    public function tel_login($data)
    {
        # 根据手机读取用户名
        $tel = $data['tel'];
        $telInfo = user_tel::findFirst([
            'tel = :tel:',
            'bind' => [
                'tel' => $tel
            ]
        ]);
        if (!($telInfo instanceof user_tel)) {
            return '_tel-empty';
        }
        $user_id = $telInfo->user_id;
        $data['user_id'] = $user_id;
        return $this->loginAction($data);
    }

    /**
     * 登录账号
     * @param type $data
     */
    public function loginAction($data)
    {
        Trace::add('loginAction', $data);

        $validation = new \app\validation\Login();
        //进行验证
        if (!$validation->validate($data)) {
            return ReturnMsg::create(406, $validation->getMessage());
        }
        if ($data['user_id']) {
            return $this->loginLater($data['user_id']);
        } else {
            $info = \app\model\user::findFirstByUsername($data['username']);
            //登录验证通过
            return $this->loginLater($info->id);
        }

    }
}
