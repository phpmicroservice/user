<?php

namespace app\validation;

use app\model\user;

/**
 * Emailretpw
 * @author Dongasai
 */
class Emailretpw extends \pms\Validation
{
    # 定义验证规则
    protected $rules = [
        'new_password1' => [
            'required' => [
                'cancelOnFail' => true,
                "message" => "new_password1",
            ],
            'stringLength' => [
                'cancelOnFail' => true,
                "message" => "new_password1",
                'min' => 6,
                'max' => 20
            ],
            'confirmation' => [
                'cancelOnFail' => true,
                'message' => 'new_password1',
                'with' => 'new_password2'
            ]
        ]
    ];

    /**
     * 初始化的时候进行 验证规则解析
     */
    protected function initialize()
    {
        $this->add_email('email',[
            'message'=>'email'
        ]);
        return parent::initialize();
    }


}
