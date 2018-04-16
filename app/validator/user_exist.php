<?php

namespace app\validator;

use app\model\user;

/**
 * 判断用户是否存在
 * Class user_exist
 * @package app\validator
 */
class user_exist extends \pms\Validation\Validator
{
    protected $message = 'user_exist';
    /**
     * @param \Phalcon\Validation $validation
     * @param string $attribute
     */
    public
    function validate(\Phalcon\Validation $validation, $attribute)
    {
        $user_id = $validation->getValue($attribute);
        $model = user::findFirstById($user_id);
        if (!($model instanceof user)) {
            return $this->appendMessage($validation, $attribute);
        }
        return true;
    }
}