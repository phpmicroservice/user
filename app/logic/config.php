<?php

namespace logic\user;


use core\Sundry\Trace;
use logic\user\model\user_config;
use logic\user\validation\set_config;

/**
 * 用户设置
 * Class config
 * @package logic\user
 */
class config extends \app\Base
{

    /**
     * 设置 用户的 配置
     * @param $user_id
     * @param $name
     * @param $value
     */
    public function set_config($user_id, $name, $value)
    {
        $data = [
            'user_id' => $user_id,
            'config_name' => $name,
            'config_value' => $value
        ];
        $validation = new set_config();
        if (!$validation->validate($data)) {
            Trace::add('info', '33!');
            return $validation->getMessage();
        }
        # 验证完成
        # 读取旧的信息
        $infoM = user_config::findFirst([
            'config_name =:config_name: and user_id =:user_id: ',
            'bind' => [
                'user_id' => $user_id,
                'config_name' => $name
            ]
        ]);
        if ($infoM instanceof user_config) {

        } else {
            $infoM = new user_config();
        }
        $infoM->setData($data);
        if (!$infoM->save()) {
            Trace::add('info', 'chucuo!');
            return $infoM->getMessage();
        }
        return true;
    }
}