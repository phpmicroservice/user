<?php

namespace app\validator;


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

            $passwordHashModel = \app\model\user::findFirstById($user_id);
        } else {
            $passwordHashModel = \app\model\user::findFirstByUsername($username);
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
