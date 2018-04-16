<?php


namespace app\validator;

use app\model\user_realauth;

/**
 * 是否可提交验证,状态验证
 * Class Realauth_submit
 * @package app\validator
 */
class Realauth_submit extends \pms\Validation\Validator;
{
    public
    function validate(\Phalcon\Validation $validation, $attribute)
    {
        $info = $validation->getValue($attribute);
        $mode = user_realauth::findFirstByuser_id($info);
        if ($mode instanceof user_realauth) {
            if ($mode->status == 0 || $mode->status == -1) {
                return true;
            }
            return $this->appendMessage($validation, $attribute);
        }
        return true;
    }

}