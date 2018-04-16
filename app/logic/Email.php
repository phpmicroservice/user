<?php

namespace logic\user;

use core\Sundry\Trace;
use app\model\user_email;

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
        Trace::add('info', $code);
        # 发送邮箱验证码
        \core\message\Email\facade::send($email, '邮箱解绑验证码', "您的邮箱验证码为:$code, 该验证码有效期限为3天,请在3天内完成验证.");
        return true;
    }

    /**
     * 发送激活码
     * @param $email
     */
    public function send_security($email)
    {
        $code = mt_rand(100000, 999999);
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
            return $validation->getMessage();
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
        \core\message\Email\facade::send($email, '邮箱激活验证码', "您的邮箱验证码为:$code,该验证码有效期限为3天,请在三天内完成验证.");
        return true;
    }

    /**
     * 验证并绑定
     * @param $security
     */
    public function security_check($security)
    {
        # 创建信息
        $model = user_email::findFirstByuser_id($this->user_id);
        if ($model instanceof user_email) {
            if ($model->status == 1) {
                return '_status-error';
            }
        } else {
            # 不存在数据
            return '_empty-error';
        }

        Trace::add('info', [$security, $model->code]);
        Trace::add('info', [$model->create_time + (3600 * 72), time()]);
        if ($model->code === $security && ($model->create_time + (3600 * 72)) > time()) {
            # 验证通过
            $model->status = 1;
            $model->validation_time = time();
            if ($model->save() === false) {
                return $model->getMessage();
            }
            return true;
        }
        Trace::add('info', 'error');
        return '_error-timeorcode';
    }

    /**
     * 验证并解绑
     */
    public function security_relieve($security)
    {
        # 创建信息
        $model = user_email::findFirstByuser_id($this->user_id);
        if ($model instanceof user_email) {

        } else {
            # 不存在数据
            return '_empty-error';
        }

        Trace::add('info', [$security, $model->code]);
        Trace::add('info', [$model->create_time + (3600 * 72), time()]);
        if ($model->code === $security && ($model->create_time + (3600 * 72)) > time()) {
            # 验证通过
            $model->status = 0;
            $model->validation_time = time();
            if ($model->save() === false) {
                return $model->getMessage();
            }
            return true;
        }
        Trace::add('info', 'error');
        return '_error-timeorcode';

    }

    public function info()
    {
        # 读取信息
        $model = user_email::findFirstByuser_id($this->user_id);
        return $model;
    }
}