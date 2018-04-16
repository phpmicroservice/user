<?php

namespace logic\user\validation;

use logic\user\model\user;

/**
 * Description of Reg
 *
 * @author Dongasai
 */
class Reg extends \pms\CoreValidation
{

//定义验证规则
    protected $rule = [
        'username' => [
            'required' => [
                'cancelOnFail' => true,
                "message" => "username",
            ],
            'stringLength' => [
                'cancelOnFail' => true,
                "message" => "username",
                'min' => 3,
                'max' => 15
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
        # nickname 验证
        $this->add_required('nickname', [
            'cancelOnFail' => true,
        ]);
        $this->add_stringLength('nickname', [
            'cancelOnFail' => true,
            'max' => 20,
            'min' => 2
        ]);
        # 唯一验证
        $this->add_exist('username', [
            'class_name_list' => user::class,
            'reverse' => true,
            'message' => 'username',
            'cancelOnFail' => true,
            'function_name' => 'findFirstByUsername'
        ]);
        # 手机验证码
        $this->add_Validator('tel', [
            'name' => \logic\Common\Verification\TelValidator::class,
            'message' => 'Verification',
            'captcha_name' => 'phone_captcha',
            'prefix' => 'reg',
            'cancelOnFail' => true,
        ]);
        $this->add_tel('tel');
        return parent::initialize();
    }


}
