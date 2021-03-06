<?php
/**
 * Created by PhpStorm.
 * User: Dongasai
 * Date: 2017/12/4
 * Time: 14:56
 */

namespace app\validation;


use app\validator\friend_exits;

class friend_set extends \pms\Validation
{

    protected function initialize()
    {
        #验证好友
        $this->add_Validator('user_id', [
            'name' => friend_exits::class,
            'user2' => 'user_id2'
        ]);
        # 可以设置的字段
        $this->add_in('field', [
            'domain' => [
                'no_see_circle',
                'no_see_my_resolution'
            ]
        ]);

        return parent::initialize(); // TODO: Change the autogenerated stub
    }
}