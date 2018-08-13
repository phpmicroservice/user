<?php

namespace app\controller;

use app\Controller;


/**
 * Class UserController 用户控制器
 * @package apps\home\controllers
 */
class User extends Controller
{

    /**
     * 修改用户名
     */
    public function edit_username()
    {
        $new_username = $this->getData('username', '');
        $User = new \app\logic\User();
        $re = $User->edit_username($this->user_id, $new_username);
        return $this->send($re);
    }

    /**
     * 修改密码
     * @return mixed
     */
    public function edit_password()
    {
        $old_password = $this->getData('old_password');
        $new_password = $this->getData('new_password');
        $new_password2 = $this->getData('new_password2');
        $data = [
            'old_password' => $old_password,
            'new_password' => $new_password,
            'new_password2' => $new_password2,
        ];

        $User = new \app\logic\Password();
        $re = $User->edit_password($this->user_id, $data);
        return $this->send($re);
    }




}