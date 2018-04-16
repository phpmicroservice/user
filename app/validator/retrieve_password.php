<?php
/**
 * Created by PhpStorm.
 * User: saisai
 * Date: 17-5-25
 * Time: 下午3:02
 */

namespace app\validator;


class retrieve_password extends \pms\Validation\Validator
{
    /**
     * 执行验证
     * @param \Phalcon\Validation $validation 这个验证器
     * @param string $attribute 要验证的字段名字
     * @return boolean
     */
    public
    function validate(\Phalcon\Validation $validation, $attribute)
    {
        # 验证手机验证码
        $username = $validation->getValue($attribute);
        $captcha = $validation->getValue('captcha');

        $Verification = new \logic\Common\Verification();
        $re = $Verification->check('retrieve_password', $username, $captcha);
        if (is_string($re)) {
            $this->type = $re;
            return $this->appendMessage($validation, $attribute);
        }
        # 验证码新密码
        if ($validation->getValue('new_password') != $validation->getValue('new_password2')) {
            $this->type = 'new_password';
            return $this->appendMessage($validation, $attribute);
        }
        return true;
    }


}