<?php

namespace logic\user\service;

use core\Sundry\Trace;
use logic\rbac\Role;
use logic\user\model\user_info;
use logic\user\model\user_tel;
use tool\Str;

/**
 * Description of Reg
 *
 * @author Dongasai
 */
class Reg extends \app\Base
{

    /**
     * 账户注册
     * @param type $username 用户名
     * @param type $password 密码
     */
    public function regAction($data)
    {
        Trace::add('info', $data);
        # 进行验证
        $validation = new \logic\user\validation\Reg();
        if (!$validation->validate($data)) {
            return $validation->getMessage();
        }

        # 验证完成
        $userModel = new \logic\user\model\user();
        $security = new \Phalcon\Security();

        $data['username'] = $data['username'] ? $data['username'] : uniqid();
        //密码加密
        $data['password'] = $security->hash($data['password'], 2);
        //进行注册 增加用户信息
        $this->transactionManager->get();
        $data['nickname'] = $data['nickname'] ? $data['nickname'] : $data['username'];
        $data['create_time'] = time();
        $data['update_time'] = 0;
        $data['edit_username'] = 0;
        $re33 = $userModel->save($data);
        if ($re33 === false) {
            $this->transactionManager->rollback();
            return $userModel->getMessage();
        }

        # 进行注册后的操作
        $re42 = $this->initReg($userModel->id, $data);
        if (is_string($re33)) {
            $this->transactionManager->rollback();
            return $re42;
        }
        $this->transactionManager->commit();
        $Login = new Login();
        $Login->s_login($userModel->id);
        return ['id' => $userModel->id];
    }

    /**
     * 注册完成之后的操作
     * @param $user_id
     */
    private function initReg($user_id, $data)
    {
        # 增加普通用户角色

        Role::add_user($user_id, 2);
        # 初始化 用户信息表
        $model = new user_info();
        $model->setData([
            'user_id' => $user_id,
            'gender' => -1,
            'birthday' => '1990-10-10',
            'personalized' => '',
            'area' => '653221',
            'headimg' => 0,
            'lock' => 0,
            'nickname' => $data['nickname']
        ]);
        if (!$model->save()) {
            return $model->getMessage();
        }
        # 增加绑定手机
        $data_tel = [
            'user_id' => $user_id,
            'tel' => $data['tel'],
            'status' => 1
        ];
        $modeltel = new user_tel();
        $modeltel->setData($data_tel);
        if (!$modeltel->save()) {
            return $model->getMessage();
        }
        return true;

    }

    /**
     * 增加用户
     * @param $data username password  password2
     * @return array|bool|string
     */
    public function add($data)
    {
        # 进行验证
        $validation = new \logic\user\validation\add_user();
        if (!$validation->validate($data)) {
            return $validation->getMessage();
        }
        # 验证完成
        $userModel = new \logic\user\model\user();
        $security = new \Phalcon\Security();
        //密码加密
        $data['password'] = $security->hash($data['password'], 2);
        //进行注册 增加用户信息
        $this->transactionManager->get();

        $data['create_time'] = time();
        $data['update_time'] = 0;
        $re33 = $userModel->save($data);
        if ($re33 === false) {
            $this->transactionManager->rollback();
            return $userModel->getMessage();
        }
        # 进行注册后的操作
        $re42 = $this->initReg($userModel->id, $data);
        if (is_string($re33)) {
            $this->transactionManager->rollback();
            return $re42;
        }
        $this->transactionManager->commit();
        return ['id' => $userModel->id];
    }

}
