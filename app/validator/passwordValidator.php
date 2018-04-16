<?php

namespace logic\user\validator;

use core\Sundry\Trace;

/**
 * passwordValidator
 * 密码验证
 * @author Dongasai
 */
class passwordValidator extends \Phalcon\Validation\Validator
{

    /**
     * 执行验证
     *
     * @param \Phalcon\Validation $validator
     * @param string $attribute
     * @return boolean
     */
    public function validate(\Phalcon\Validation $validator, $attribute)
    {

        $password = $validator->getValue('password');
        $username = $validator->getValue('username');
        $user_id = $validator->getValue('user_id');
        $Security = new \Phalcon\Security();
        if (empty($username)) {
            # 不存在用户名
            Trace::add('info30', $user_id);
            $passwordHashModel = \logic\user\model\user::findFirstById($user_id);
        } else {
            $passwordHashModel = \logic\user\model\user::findFirstByUsername($username);
        }

        if (!$passwordHashModel or !$Security->checkHash($password, $passwordHashModel->password)) {
            $message = $this->getOption("message");
            if (!$message) {
                $message = "The " . __CLASS__ . " is not valid";
            }
            $validator->appendMessage(
                new \Phalcon\Validation\Message($message, $attribute, $attribute)
            );
            return false;
        }
        return true;
    }

}
