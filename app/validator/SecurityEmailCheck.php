<?php

namespace app\validator;

use app\model\user_email;
use pms\Validation\Validator;

/**
 * Class SecurityEmailCheck
 * @package app\validator
 */
class SecurityEmailCheck extends Validator
{

    /**
     * 验证
     * @param \Phalcon\Validation $validation
     * @param string $attribute
     * @return bool|void
     */
    public function validate(\Phalcon\Validation $validation, $attribute)
    {

        $model = user_email::findFirstByuser_id($validation->getValue('user_id'));
        if ($model instanceof user_email) {
            if ($model->status == $validation->getValue('nostatus')) {
                $this->type = 'status-error';
                return $this->appendMessage($validation, $attribute);
            }
        } else {
            # 不存在数据
            $this->type = 'empty-error';
            return $this->appendMessage($validation, $attribute);
        }
        if (!(($model->create_time + (3600 * 72)) > time())) {
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