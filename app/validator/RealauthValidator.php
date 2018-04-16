<?php

namespace app\validator;

use app\model\user_realauth;


/**
 * 实名认证判断
 * Class RealauthValidator
 * @package app\validator
 */
class RealauthValidator extends \pms\Validation\Validator
{

    public
    function validate(\Phalcon\Validation $validation, $attribute)
    {
        $user_id = $validation->getValue($attribute);
        $datamodel = user_realauth::findFirst([
            'user_id = :user_id: and status = 1',
            'bind' => [
                'user_id' => $user_id
            ]
        ]);
        if ($datamodel instanceof user_realauth) {
            return true;
        }
        $this->type = 'norealauth';
        return $this->appendMessage($validation, $attribute);
    }
}