<?php

namespace app\logic;

use app\model\user_tel;
use app\validation\edit_tel;
use app\validation\tel_captcha;

class Tel extends \app\Base
{

    public function tel_is_reg($tel)
    {
        $info = user_tel::findFirst([
            'tel=:tel:', 'bind' => [
                'tel' => $tel
            ]
        ]);
        if ($info instanceof user_tel) {
            return true;
        }
        return false;
    }

    /**
     * 发送消息
     * @param $user_id
     * @param $mes_id
     * @param $data
     */
    public function send_message($user_id, $mes_type, $data)
    {

        $telMOdel = user_tel::findFirstByUser_id($user_id);
        if (!($telMOdel instanceof user_tel)) {
            pre(func_get_args());
            return '_empty-error';
        }
        $tel = $telMOdel->tel;
        if ($mes_type == 'fund_password') {
            #发送支付密码
            $re = \core\message\SMS\facade::send($tel,
                '',
                [
                    'tmp' => 'fund_password',
                    'code' => $data['password']
                ], 'yunpiand'
            );
            if (is_string($re)) {
                pre($re, func_get_args());
                return $re;
            }
        }
        return true;

    }

    /**
     * 手机信息
     * @param $user_id
     */
    public function info4user($user_id)
    {
        $model = user_tel::findFirstByUser_id($user_id);
        return $model;
    }

    /**
     * 更换手机
     * @param $user_id 用户
     * @param $data 数据 old_tel  captcha new_tel
     */
    public function edit_tel($user_id, $data)
    {
        $data['user_id'] = $user_id;
        $validation = new edit_tel();

        if (!$validation->validate($data)) {
            return $validation->getMessage();
        }
        # 验证通过
        $model = user_tel::findFirstByUser_id($user_id);
        $model->tel = $data['new_tel'];
        if (!$model->save()) {
            return $model->getMessage();
        }
        return true;
    }

    /**
     * 手机验证
     * @param $user_id
     * @param $tel
     * @param $captcha
     */
    public function tel_captcha($user_id, $tel, $captcha)
    {

        $validation = new tel_captcha();
        if (!$validation->validate(['user_id' => $user_id,
            'tel' => $tel, 'captcha' => $captcha])) {
            return $validation->getMessage();
        }

        $model = new user_tel();
        $model->user_id = $user_id;
        $model->tel = $tel;
        $model->status = 1;
        if (!$model->save()) {
            return $model->getMessage();
        }
        return true;
    }
}