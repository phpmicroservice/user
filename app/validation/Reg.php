<?php

namespace app\validation;

use app\model\user;

/**
 * Description of Reg
 *
 * @author Dongasai
 */
class Reg extends \pms\Validation
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


}
