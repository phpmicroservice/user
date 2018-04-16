<?php

namespace logic\user;

use core\Sundry\Trace;
use app\model\user_qq;
use app\model\user_qq_info;
use logic\user\service\Login;
use app\validation\relevance_qq;

class Qq extends \app\Base
{

    public function info($user_id)
    {
        $model = user_qq::findFirstByuser_id($user_id);
        if (!($model instanceof user_qq)) {
            return '_empty-error';
        }
        $model->set_relation_data(['info']);
        $model->set_append_field(['info']);
        return $model;

    }


    /**
     * 取消关联
     * @param $user_id
     * @return bool|string
     */
    public function quxiao($user_id)
    {
        $model = user_qq::findFirstByuser_id($user_id);
        if (!($model instanceof user_qq)) {
            return '_empty-error';
        }
        if (!$model->delete()) {
            return $model->getMessage();
        }

        return true;

    }

    /**
     * QQ登陆
     * @param $openid
     * @return bool|string
     */
    public function login($openid)
    {

        # 读取关联信息
        $model = user_qq::findFirst([
            'openid= :openid:', 'bind' => [
                'openid' => $openid
            ]
        ]);
        if (!($model instanceof user_qq)) {
            return '_empty-qq-user';
        }
        $Login = new Login();
        return $Login->s_login($model->user_id);
    }

    /**
     * 进行QQ关联
     * @param $openid
     * @param $user_id
     */
    public function relevance($openid, $user_id, $info)
    {
        Trace::add('info39', [$openid, $user_id, $info]);
        $info['openid'] = $openid;
        $data = [
            'user_id' => $user_id,
            'openid' => $openid
        ];
        $validation = new  relevance_qq();
        if (!$validation->validate($data)) {
            Trace::add('error', $validation->getMessage());
            return $validation->getMessage();
        }
        $model = new user_qq();
        $model->setData($data);
        if (!$model->save()) {
            Trace::add('error', $model->getMessage());
            return $model->getMessage();
        }
        # 增加附加信息
        $user_qq_info = user_qq_info::findFirstByOpenid($openid);

        if ($user_qq_info instanceof user_qq_info) {
            Trace::add('error', '出错位置');
        } else {
            Trace::add('error', '出错位置');
            $user_qq_info = new user_qq_info();
        }

        $user_qq_info->setData($info);
        $re = $user_qq_info->save();
        if (!$re) {
            Trace::add('error', $user_qq_info->getMessage());
            return '什么错误!';
        }
        return true;

    }

    public function del()
    {

    }
}