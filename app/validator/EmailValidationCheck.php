<?php

namespace app\validator;

use app\model\email_code;
use app\model\user_email;
use pms\Validation\Validator;

/**
 * 邮箱验证码
 * Class EmailValidationCheck
 * @package app\validator
 */
class EmailValidationCheck extends Validator
{

    /**
     * 验证
     * @param \Phalcon\Validation $validation
     * @param string $attribute
     * @return bool|void
     */
    public function validate(\Phalcon\Validation $validation, $attribute)
    {

        $email=$validation->getValue('email');
        $type=$validation->getValue('type');
        $model = email_code::findFirst([
            'email = :email: and type =:type: and status = 0',
            'bind'=>[
                'email'=>$email,
                'type'=>$type
            ],
            'order'=>'id desc'
        ]);
        if ($model instanceof email_code) {

        } else {
            # 不存在数据
            $this->type = 'empty-error';
            return $this->appendMessage($validation, $attribute);
        }
        if (!(($model->create_time + (3600)) > time())) {
            $this->type = 'overtime';
            return $this->appendMessage($validation, $attribute);

        }
        if (!($model->code == $validation->getValue('security'))) {
            $this->type = 'code';
            return $this->appendMessage($validation, $attribute);
        }
        return true;
        # 验证通过
    }
}