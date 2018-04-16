<?php

namespace logic\user\validator;

use logic\user\model\user;


/**
 * 账户可用验证 禁用则不通过
 * Class user_forbid
 * @package logic\user\validator
 */
class user_forbid extends \pms\Validation\Validator;
{
    public
    function validate(\Phalcon\Validation $validation, $attribute)
    {
        $user_id = $validation->getValue('user_id');
        $user_name = $validation->getValue('username');

        if (empty($user_id)) {
            $userInfo = user::findFirst([
                'username =:username:', 'bind' => [
                    'username' => $user_name
                ]
            ]);
        } else {
            $userInfo = user::findFirst([
                'id =:user_id:', 'bind' => [
                    'user_id' => $user_id
                ]
            ]);

        }

        if ($userInfo instanceof user) {
            if ($user_id->forbid == 1) {
                # 已经禁用
                $this->type = 'forbid';
                return $this->appendMessage($validation, $attribute);
            }
        } else {
            $this->type = 'user_empty';
            return $this->appendMessage($validation, $attribute);
        }
        return true;

    }

}