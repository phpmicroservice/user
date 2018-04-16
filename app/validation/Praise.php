<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

namespace logic\user\validation;

/**
 * Description of Praise
 *
 * @author Dongasai
 */
class Praise extends \pms\CoreValidation
{

//定义验证规则
    protected $rules = [
        'to_type' => [
            'required' => [
                "message" => "required",
            ],
            'in' => [
                "message" => "domain",
                'domain' => [
                    'article',
                    'bbs',
                    'bbs_reply'
                ],
            ]
        ],
        'to_numerical' => [
            'required' => [
                "message" => "required"
            ],
        ]
    ];

    /**
     * 初始化
     */
    public function initialize()
    {
        $this->rules['to_numerical']['exist'] = [
            'object_name' => 'to_type',
            'class_name_list' => [
                'article' => \logic\Article\model\article::class,
                'bbs' => \logic\Bbs\model\forum::class,
                'bbs_reply' => \logic\Bbs\model\forum_reply::class
            ],
            "message" => "error"
        ];
        parent::initialize();
    }

    /**
     * 设置重复验证
     * @param type $obj 用于验证的对象
     */
    public function setRepetition($obj, $data)
    {
        $parameter = [
            "message" => "repetition",
            'class_name' => $obj,
            'where' => [
                'conditions' => ' to_type = :to_type: and to_numerical= :to_numerical: and user_id  =:user_id:  ',
                'bind' => [
                    "to_type" => $data['to_type'],
                    'to_numerical' => $data['to_numerical'],
                    'user_id' => $data['user_id']
                ]
            ]
        ];
        $this->add_repetition('to_numerical', $parameter);
    }

}
