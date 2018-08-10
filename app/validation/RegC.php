<?php

namespace app\validation;

use app\model\user;

/**
 * Description of RegC
 * 带验证码的注册验证
 * @author Dongasai
 */
class RegC extends \pms\Validation
{
    # 定义验证规则
    protected $rules = [
        'username' => [
            'required' => [
                'cancelOnFail' => true,
                "message" => "username",
            ],
            'stringLength' => [
                'cancelOnFail' => true,
                "message" => "username",
                'min' => 3,
                'max' => 30
            ]
        ],
        'password' => [
            'required' => [
                'cancelOnFail' => true,
                "message" => "password",
            ],
            'stringLength' => [
                'cancelOnFail' => true,
                "message" => "password",
                'min' => 6,
                'max' => 20
            ],
            'confirmation' => [
                'cancelOnFail' => true,
                'message' => 'password',
                'with' => 'password2'
            ]
        ]
    ];

    /**
     * 初始化的时候进行 验证规则解析
     */
    protected function initialize()
    {

        # 唯一验证
        $this->add_exist('username', [
            'class_name_list' => user::class,
            'reverse' => true,
            'message' => 'exist',
            'cancelOnFail' => true,
            'function_name' => 'findFirstByUsername'
        ]);


        return parent::initialize();
    }


    # 注册验证码
    public function beforeValidation1($data)
    {
        $this->add_required(['captcha_value', 'captcha_identifying' ], [
            'message' => 'required'
        ]);
        # 验证码
        $this->add_Validator('captcha_value', [
            'name' => ServerAction::class,
            'server_action' => 'validation@/server/true_check',
            'data' => [
                "sn" => 'user',
                "operation" => 'reg',
                "value" => $data['captcha_value'],
                "identifying" => $data['captcha_identifying']
            ],
            'message' => 'captcha'
        ]);
    }
}
