<?php

namespace logic\user\validation;

/**
 * 收藏的数据验证
 * @author Dongasai
 */
class Collect extends \pms\CoreValidation
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
                'name' => 'logic\user\validator\CollectValidator',
                "message" => "userCollect"
            ],
        ]
    ];

}
