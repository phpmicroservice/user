<?php

namespace logic\user;

use core\Sundry\Trace;
use app\model\user_tel;

/**
 * 用户密码
 * Class Password
 * @package logic\user
 */
class Password extends \app\Base
{
    /**
     * 找回密码
     * @param $data
     * @return bool|string
     */
    public function retrieve_password($data)
    {

        $validation = new \pms\Validation();
        $validation->add_Validator('username', [
            'message' => 'retrieve_password',
            'name' => validator\retrieve_password::class,
        ]);
        if (!$validation->validate($data)) {
            Trace::add('info', 27);
            return $validation->getMessage();
        }

        #
        $tel = $data['username'];
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
        $UserModel = \app\model\user::findFirstById($user_id);
        if (!$UserModel) {
            Trace::add('info', '找不到用户!');
            return '_empty-info';
        }
        $Security = new \Phalcon\Security();

        $new_password = $Security->hash($data['new_password'], 2);
        $UserModel->password = $new_password;
        if ($UserModel->update() === false) {
            Trace::add('info', 27);
            return $UserModel->getMessage();
        }
        return true;

    }

    /**
     * 修改密码
     * @param $user_id
     * @param $data
     */
    public function edit_password($user_id, $data)
    {
        $data['user_id'] = $user_id;
        $validation = new \pms\Validation();
        $validation->add_Validator('user_id', [
            'message' => 'password',
            'name' => validator\edit_password::class
        ]);
        if (!$validation->validate($data)) {
            return $validation->getMessage();
        }
        $UserModel = \app\model\user::findFirstById($user_id);
        $Security = new \Phalcon\Security();
        $new_password = $Security->hash($data['new_password'] . $UserModel->username, 2);

        $UserModel->password = $new_password;
        if ($UserModel->update() === false) {
            return $UserModel->getMessage();
        }
        return true;

    }
}