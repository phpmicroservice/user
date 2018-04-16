<?php

namespace logic\user\validation;


use logic\user\validator\user_exist;

class set_config extends \pms\CoreValidation
{

    protected function initialize()
    {
        # 用户
        $this->add_Validator('user_id', [
            'name' => user_exist::class,
            'message' => 'user_exist'
        ]);

        # 名字
        $this->add_in('config_name', [
            'domain' => [
                'add_friend_type',
                'wocao'
            ],
            'message' => 'config_name_in'
        ]);

        # 值
        $this->add_digit('config_value', [
            'message' => 'digit'
        ]);

    }

}