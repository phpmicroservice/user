<?php

namespace app\validation;


use app\model\user;

class edit_username extends \pms\Validation
{
    protected function initialize()
    {
        # 长度
        $this->add_stringLength('username', [
            'min' => 5,
            'max' => 20
        ]);
        # 字母和数字
        $this->add_alnum('username', [
            'message' => 'alnum'
        ]);
        # 必须包含字母
        $this->add_musten('username', [
            'message' => 'musten'
        ]);

        # 可编辑
        $this->add_where('user_id', [
            'model' => user::class,
            'wheres' => [
                'id' => 'user_id',
                'edit_username' => 'edit_username'
            ],
            'message' => 'edit_username'
        ]);
    }

}