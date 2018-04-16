<?php

namespace app\validation;

use core\Validator\RequiredPro;
use app\validator\user_forbid;

/**
 * 登录数据验证
 * @author Dongasai
 */
class Login extends \pms\CoreValidation
{

//定义验证规则
    protected $rules = [
        'username' => [

        ],
        'password' => [
            'required' => [
                'cancelOnFail' => true,
                "message" => "password"
            ],
            'notempty' => [
                'cancelOnFail' => true,
                "message" => "password"
            ],
            'Validator' => [
                'cancelOnFail' => true,
                'name' => 'app\validator\passwordValidator',
                "message" => "password"
            ],
        ]
    ];

    /**
     * 初始化的时候进行 验证规则解析
     */
    protected function initialize()
    {

        # 用户名,用户id必填一个
        $this->add_Validator('user_id', [
            'message' => 'RequiredPro',
            'name' => RequiredPro::class,
            'attrs' => [
                'username', 'user_id'
            ]
        ]);

        # 账户可用验证
        $this->add_Validator('user_id', [
            'name' => user_forbid::class,
            'message' => 'user_forbid'
        ]);

        # 图形验证码验证
        if ($this->Config->get('login_verification_code')) {
            # 增加图形验证码验证
            $this->add_Validator('captcha', [
                'cancelOnFail' => true,
                'name' => \logic\Common\validator\verify::class,
                'message' => 'verify',
                'config_index' => 'verify'
            ]);
        }


        return parent::initialize();
    }

}
