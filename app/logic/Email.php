<?php

namespace app\logic;

use app\model\email_code;
use app\model\user_email;
use app\validator\EmailValidationCheck;
use app\validator\SecurityEmailCheck;
use pms\bear\ClientSync;
use pms\Validation;

/**
 * 邮件相关
 * Class Email
 * @package logic\user
 */
class Email extends \app\Base
{
    private $user_id;

    public function __construct($user_id)
    {

        $this->user_id = $user_id;
    }

    /**
     * 发送解绑验证码
     */
    public function send_relieve()
    {
        # 读取信息
        $model = user_email::findFirstByuser_id($this->user_id);
        if ($model instanceof user_email) {
            if ($model->status != 1) {
                return '_status-error';
            }
        } else {
            # 不存在数据
            return "_empty-error";

        }
        $code = mt_rand(10000, 999999);
        $email = $model->email;
        $model->setData([
            'validation_time' => 0,
            'code' => $code
        ]);
        if ($model->save() === false) {
            return $model->getMessage();
        }

        # 发送邮箱验证码
        return $this->send_email($email, '邮箱解绑验证码', "您的邮箱验证码为:$code, 该验证码有效期限为3天,请在3天内完成验证.");

    }

    /**
     * 发送激活码
     * @param $email
     */
    public function send_security($email)
    {
        $code = mt_rand(1000000, 9999999);
        $data = [
            'user_id' => $this->user_id,
            'email' => $email,
            'status' => 0,
            'create_time' => time(),
            'validation_time' => 0,
            'code' => $code
        ];
        # 进行数据验证
        $validation = new \app\validation\user_email();
        if (!$validation->validate($data)) {
            return $validation->getMessages();
        }
        # 创建信息
        $model = user_email::findFirstByuser_id($this->user_id);
        if ($model instanceof user_email) {
        } else {
            # 不存在数据
            $model = new user_email();
        }

        $model->setData($data);
        if ($model->save() === false) {
            return $model->getMessage();
        }
        # 发送邮箱验证码
        return $this->send_email($email, '邮箱激活验证码', "您的邮箱验证码为:$code,该验证码有效期限为3天,请在三天内完成验证.");
    }


    /**
     * 发送激活码
     * @param $email
     */
    public function send_validation($email, $type)
    {
        var_dump(func_get_args());
        $code = mt_rand(1000000, 9999999);
        $data = [
            'email' => $email,
            'status' => 0,
            'status1'=>1,
            'create_time' => time(),
            'validation_time' => 0,
            'code' => $code,
            'type' => $type
        ];
        # 进行数据验证
        $validation = new \app\validation\EmailCaptcha();
        if (!$validation->validate($data)) {
            return $validation->getErrorMessages();
        }
        # 创建信息
        $model = new email_code();
        $model->setData($data);
        if ($model->save() === false) {
            return $model->getMessage();
        }
        # 发送邮箱验证码
        return $this->send_email($email, '邮箱验证码', "您的邮箱验证码为: $code ,该验证码有效期限为1小时,请在三天内完成验证.");
    }

    /**
     * 验证码验证
     * @param $email
     * @param $type
     * @param $security
     * @return bool|\Phalcon\Validation\Message\Group
     */
    public function validation_check($email, $type, $security, $status = true)
    {

        $va = new Validation();
        $va->add_Validator('security', [
            'name' => EmailValidationCheck::class,
            'message' => 'security_email_check'
        ]);
        if (!$va->validate([
            'security' => $security,
            'email' => $email,
            'type' => $type
        ])
        ) {
            return $va->getErrorMessages();
        }
        # 验证通过
        if ($status) {
            # 修改验证状态
            $model = email_code::findFirst([
                'email = :email: and type =:type:',
                'bind' => [
                    'email' => $email,
                    'type' => $type
                ],
                'order' => 'id desc'
            ]);
            $model->status = 1;
            $model->validation_time = time();
            if ($model->save() === false) {
                return $model->getMessage();
            }
        }
        return true;
    }


    /**
     * 发送邮件
     * @param $email
     * @param $title
     * @param $content
     * @return bool
     */
    private function send_email($email, $title, $content)
    {
        $d = [
            'email' => $email,
            'title' => $title,
            'content' => $content,
            'time' => time(),
            'uqid' => uniqid()
        ];
        $re = $this->proxyCS->request_return('email', '/sende/send2', $d);
        if ($re['e']) {
            # 出错
            return 'sys-error';
        } else {
            # 成功
            return true;
        }
    }

    /**
     * 验证并绑定
     * @param $security
     */
    public function security_check($security)
    {
        $va = new Validation();
        $va->add_Validator('security', [
            'name' => SecurityEmailCheck::class,
            'message' => 'security_email_check'
        ]);
        if (!$va->validate(['security' => $security, 'nostatus' => 1, 'user_id' => $this->user_id])) {
            return $va->getMessages();
        }
        # 验证通过
        # 创建信息
        $model = user_email::findFirstByuser_id($this->user_id);
        $model->status = 1;
        $model->validation_time = time();
        if ($model->save() === false) {
            return $model->getMessage();
        }
        return true;

    }

    /**
     * 验证并解绑
     */
    public function security_relieve($security)
    {
        $va = new Validation();
        $va->add_Validator('security', [
            'name' => SecurityEmailCheck::class,
            'message' => 'security_email_check'
        ]);
        if (!$va->validate(['security' => $security, 'nostatus' => 0, 'user_id' => $this->user_id])) {
            return $va->getMessages();
        }

        # 验证通过
        # 验证通过
        $model = user_email::findFirstByuser_id($this->user_id);
        $model->status = 0;
        $model->validation_time = time();
        if ($model->save() === false) {
            return $model->getMessage();
        }
        return true;


    }

    public function info()
    {
        # 读取信息
        $model = user_email::findFirstByuser_id($this->user_id);
        if (empty($model)) {
            return [];
        }
        return $model->toArray();
    }
}