<?php

namespace apps\user\controllers;

use app\Controller;


/**
 * Class UserController 用户控制器
 * @package apps\home\controllers
 */
class User extends Controller
{

    public function rbac()
    {
        $info = \logic\rbac\Role::user($this->user_id);
        return $this->restful_return($info);
    }


    public function edit_username()
    {
        $new_username = $this->request->get('username', 'string', '');
        $User = new \logic\user\User();
        $re = $User->edit_username($this->user_id, $new_username);
        return $this->restful_return($re);
    }

    /**
     * 修改密码
     * @return \Phalcon\Http\Response|\Phalcon\Http\ResponseInterface
     */
    public function edit_password()
    {
        $parameter = [
            'old_password' => ['post', 'old_password', 'string', ''],
            'new_password' => ['post', 'new_password', 'string', ''],
            'new_password2' => ['post', 'new_password2', 'string', ''],
        ];
        $data = $this->getData($parameter);
        $User = new \logic\user\Password();
        $re = $User->edit_password($this->user_id, $data);
        return $this->restful_return($re);
    }


}