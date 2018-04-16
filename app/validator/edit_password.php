<?php
/**
 * Created by PhpStorm.
 * User: saisai
 * Date: 17-5-24
 * Time: 下午3:37
 */

namespace logic\user\validator;


class edit_password extends \pms\Validation\Validator;
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
        $password = $validation->getValue('old_password');
        $user_id = $validation->getValue('user_id');
        $Security = new \Phalcon\Security();
        $passwordHash = \logic\user\model\user::findFirstById($user_id);
        if (!$passwordHash or !$Security->checkHash($password, $passwordHash->password)) {
            $this->type = 'old_password';
            return $this->appendMessage($validation, $attribute);
        }
        if ($validation->getValue('new_password') != $validation->getValue('new_password2')) {
            $this->type = 'new_password';
            return $this->appendMessage($validation, $attribute);
        }
    }
}