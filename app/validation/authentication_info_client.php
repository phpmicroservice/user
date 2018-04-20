<?php
/**
 * Created by PhpStorm.
 * User: saisai
 * Date: 17-5-10
 * Time: 下午1:54
 */

namespace app\validation;


/**
 * 申请客户认证的信息
 * Class authentication_info_client
 * @package app\validation
 */
class authentication_info_client extends \pms\Validation
{

    protected $lang = 'admin/model/user.php';
    protected $lang_field_prefix = 'model-authentication_info_client-field';
    protected $rules = [
        'realname' => [
            'required' => [
                "message" => "required",
            ],
            'stringLength' => [
                "message" => "stringLength",
                'min' => 2,
                'max' => 15
            ]
        ],
        'corporation' => [
            'required' => [
                "message" => "required",
            ],
            'stringLength' => [
                "message" => "is stringLength",
                'min' => 2,
                'max' => 100
            ]
        ],
        'address' => [
            'required' => [
                "message" => "required",
            ],
            'stringLength' => [
                "message" => "stringLength",
                'min' => 2,
                'max' => 100
            ]
        ],
        'tel' => [
            'required' => [
                "message" => "required",
            ],
            'stringLength' => [
                "message" => "stringLength",
                'min' => 10,
                'max' => 15
            ]
        ],
        'position' => [
            'required' => [
                "message" => "required",
            ],
            'stringLength' => [
                "message" => "stringLength",
                'min' => 2,
                'max' => 30
            ]
        ]
    ];
}