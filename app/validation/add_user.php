<?php

namespace app\validation;

/**
 * 增加 用户
 * Class add_user
 * @package app\validation
 */
class add_user extends \pms\Validation
{
    //定义验证规则
    protected $rule = [
        'username' => [
            'required' => [
                "message" => "username",
            ],
            'stringLength' => [
                "message" => "username",
                'min' => 3,
                'max' => 15
            ],
            'uq' => [
                'message' => 'usernmae',
                'model' => 'app\model\user'
            ]
        ],
        'password' => [
            'required' => [
                "message" => "password",
            ],
            'stringLength' => [
                "message" => "password",
                'min' => 6,
                'max' => 20
            ],
            'confirmation' => [
                'message' => 'password',
                'with' => 'password2'
            ]
        ],
        'email' => [
            'email' => [
                "message" => "email",
                'allowEmpty' => true
            ],
        ]
    ];

    protected function initialize()
    {
        $this->add_tel('username', []);
        return parent::initialize();
    }

}