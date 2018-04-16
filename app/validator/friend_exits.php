<?php

namespace app\validator;

use app\model\user_friend;

/**
 * 判断是否为好友
 * Class friend_exits
 * @package app\validator
 */
class friend_exits extends \pms\Validation\Validator;
{
    protected
    $message = 'friend_exits';
    /**
     * 验证
     * @param \Phalcon\Validation $validation
     * @param string $attribute
     * @return bool|void
     */
    public
    function validate(\Phalcon\Validation $validation, $attribute)
    {
        $user_id = $validation->getValue($attribute);
        $user_id2 = $validation->getValue($this->getOption('user2'));

        $user_f_m = user_friend::findFirst([
            'user_id = :user_id: and user_id2 =:user_id2:',
            'bind' => [
                'user_id' => $user_id,
                'user_id2' => $user_id2
            ]
        ]);
        if (!($user_f_m instanceof user_friend)) {

            return $this->appendMessage($validation, $attribute);
        }
        return true;
    }

}