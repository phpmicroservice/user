<?php


namespace app\validation;


class add_authentication_facilitator_1 extends \pms\Validation
{

    //定义验证规则
    protected $rules = [
        'corporation' => [
            'required' => [
                "message" => "required",
            ],
            'stringLength' => [
                "message" => "stringLength",
                'min' => 2,
                'max' => 10
            ]
        ],
        'address' => [
            'required' => [
                "message" => "required",
            ],
            'stringLength' => [
                "message" => "stringLength",
                'min' => 5,
                'max' => 10
            ]
        ],
        'tel' => [
            'required' => [
                "message" => "required",
            ],
            'stringLength' => [
                "message" => "stringLength",
                'min' => 11,
                'max' => 12
            ]
        ],
        'logo' => [
            'required' => [
                "message" => "required",
            ],
            'digit' => [
                "message" => "digit",
            ]
        ],
        'position' => [
            'required' => [
                "message" => "required",
            ],
        ],
        'authentication_id' => [
            'required' => [
                "message" => "required",
            ],
        ],
        'introduced' => [
            'required' => [
                "message" => "required",
            ],
            'stringLength' => [
                "message" => "stringLength",
                'min' => 11,
                'max' => 1200
            ],
        ],

        'province' => [
            'required' => [
                "message" => "required",
            ],
        ],
        'city' => [
            'required' => [
                "message" => "required",
            ],
        ],
        'serve_province' => [
            'required' => [
                "message" => "required",
            ],
        ],
        'serve_city' => [
            'required' => [
                "message" => "required",
            ],
        ],
        'type' => [
            'required' => [
                "message" => "required",
            ],
        ],
        'serve_type' => [
            'required' => [
                "message" => "required",
            ],
        ]
    ];
}