<?php

namespace app\validator;

use app\model\user_tel;

/**
 * 验证用户手机
 * Class TelValidator
 * @package app\validator
 */
class TelValidator extends \pms\Validation\Validator
{
    public
    function validate(\Phalcon\Validation $validation, $attribute)
    {
        $user_id = $validation->getValue($this->getOption('user_id'));
        $tel = $validation->getValue($attribute);
        $model = user_tel::findFirst([
            'user_id =:user_id: and tel=:tel:',
            'bind' => [
                'user_id' => $user_id,
                'tel' => $tel
            ]
        ]);

        if (!($model instanceof user_tel)) {
            $this->type = 'usertel';
            return $this->appendMessage($validation, $attribute);
        }
        return true;
    }


}