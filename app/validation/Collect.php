<?php

namespace app\validation;

/**
 * 收藏的数据验证
 * @author Dongasai
 */
class Collect extends \pms\Validation
{

//定义验证规则
    protected $rules = [
        'type' => [
            'required' => [
                "message" => "required",
            ],
            'in' => [
                "message" => "in",
                'domain' => ['article', 'bbs'],
            ]
        ],
        'numerical' => [
            'required' => [
                "message" => "required"
            ],
            'Validator' => [
                'name' => 'app\validator\CollectValidator',
                "message" => "userCollect"
            ],
        ]
    ];

}
