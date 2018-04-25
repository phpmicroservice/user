<?php
/**
 * Created by PhpStorm.
 * User: Dongasai
 * Date: 2018/1/24
 * Time: 15:32
 */

namespace app\validation;


use app\validator\user_exist;

class user_email extends \pms\Validation
{

    protected function initialize()
    {
        # 验证用户
        $this->add_Validator('user_id', [
            'name' => user_exist::class,
            'message' => 'user'
        ]);
        # 验证邮箱
        $this->add_email('email', [
            'message' => 'email'
        ]);
        return parent::initialize(); // TODO: Change the autogenerated stub
    }
}