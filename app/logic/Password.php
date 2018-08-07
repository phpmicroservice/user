<?php

namespace app\logic;

use app\filterTool\Emailretpw;
use app\model\user_email;
use app\model\user_tel;
use app\validator\edit_password;
use app\validator\retrieve_password;

/**
 * 用户密码
 * Class Password
 * @package logic\user
 */
class Password extends \app\Base
{
    public function email_repassword($email, $username)
    {
        $usermail = user_email::findFirst([
            'email =:email: and status = 1',
            'bind'=>[
                'email'=>$email
            ]
        ]);
        if ($usermail instanceof user_email) {
            $usermodel = \app\model\user::findFirstByusername($username);
            if (!($usermodel instanceof \app\model\user)) {
                return 'empty-error1';
            }
            var_dump([$usermodel->id , $usermail->user_id]);
            if ($usermodel->id == $usermail->user_id) {

            } else {
                return 'empty-error2';
            }
        } else {
            return 'empty-error3';
        }
        # 验证完成
        $emailser = new Email(1);
        return $emailser->send_validation($email, 'repassword');
    }

    /**
     * 邮件找回密码
     * @param $data
     */
    public function retrieve_epassword($data)
    {
        $ft = new Emailretpw();
        $ft->filter($data);
        # 验证
        $va = new  \app\validation\Emailretpw();
        if (!$va->validate($data)) {
            $va->getErrorMessages();
        }
        #验证码
        $emailser = new Email(0);
        $re = $emailser->validation_check($data['email'], 'repassword', $data['code'], 1);
        if (is_string($re)) {
            return $re;
        }
        # 验证通过
        $user_email = user_email::findFirstByemail($data['email']);
        if (!($user_email instanceof user_email)) {
            return 'empty-error';
        }
        return $this->retrieve_password_call($user_email->user_id, $data['new_password1']);


    }

    private function retrieve_password_call($user_id, $password)
    {
        $UserModel = \app\model\user::findFirstById($user_id);
        if (!$UserModel) {

            return '_empty-info';
        }
        $Security = new \Phalcon\Security();

        $new_password = $Security->hash($password, 2);
        $UserModel->password = $new_password;
        if ($UserModel->update() === false) {

            return $UserModel->getMessage();
        }
        return true;
    }

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
            'name' => retrieve_password::class,
        ]);
        if (!$validation->validate($data)) {

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
        return $this->retrieve_password_call($user_id, $data['new_password']);
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
            'name' => edit_password::class
        ]);
        if (!$validation->validate($data)) {
            return $validation->getErrorMessages();
        }
        $UserModel = \app\model\user::findFirstById($user_id);
        $Security = new \Phalcon\Security();
        $new_password = $Security->hash($data['new_password'], 2);

        $UserModel->password = $new_password;
        if ($UserModel->update() === false) {
            return $UserModel->getMessage();
        }
        return true;

    }
}