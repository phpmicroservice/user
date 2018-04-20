<?php

namespace app\logic;

use app\model\user_email;
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
     * 发送邮件
     * @param $email
     * @param $title
     * @param $content
     * @return bool
     */
    private function send_email($email, $title, $content)
    {
        $client = new ClientSync(get_env('PROXY_HOST'), get_env('PROXY_PROT'), 10);
        $d = [
            'email' => $email,
            'title' => $title,
            'content' => $content,
            'time' => time(),
            'uqid' => uniqid()
        ];
        output([get_env('email_access_key', 'vE6ByYnnP3V3lvcJ1FBwN'), $d, SERVICE_NAME], 'get_access');
        $data = [
            's' => 'email',
            'r' => '/sende/send2',
            'accessKey' => \get_access(get_env('email_access_key', 'vE6ByYnnP3V3lvcJ1FBwN'), $d, SERVICE_NAME),
            'd' => $d,
        ];
        $client->send($data);
        $re = $client->recv();
        if ($re['e']) {
            # 出错
            $this->message->appendMessage(new \Phalcon\Validation\Message('邮件发送失败', 'email', 'type'));
            return $this->message;
        } else {
            # 成功
            return $re['d'];
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