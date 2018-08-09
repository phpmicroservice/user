<?php

namespace app\validation;

use pms\Validation\Validator\RequiredPro;
use app\validator\user_forbid;
use pms\Validation\Validator\ServerAction;

/**
 * 登录数据验证
 * @author Dongasai
 */
class Login extends \pms\Validation
{

//定义验证规则
    protected $rules = [
        'username' => [
            'required' => [
                'cancelOnFail' => true,
                "message" => "username"
            ],
        ],
        'captcha_value' => [
            'required' => [
                'cancelOnFail' => true,
                "message" => "captcha_value"
            ],
        ],
        'captcha_identifying' => [
            'required' => [
                'cancelOnFail' => true,
                "message" => "captcha_identifying"
            ],
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



        return parent::initialize();
    }

    public function beforeValidation1($data)
    {
        # 验证码
        $this->add_Validator('captcha_value',[
            'name'=>ServerAction::class,
            'data'=>[
                "sn"=>'user',
                "operation"=>'login',
                "value"=>$data['captcha_value'],
                "identifying"=>$data['captcha_identifying']
            ],
            'message'=>'captcha'
        ]);

    }

}
