<?php

namespace app\validation;

use app\validator\user_exist;

/**
 * 手机验证码
 * Class tel_captcha
 * @package app\validation
 */
class tel_captcha extends \pms\CoreValidation
{

    /**
     * 初始化
     * @return \Phalcon\Validation
     */
    protected function initialize()
    {
        # user_id
        $this->add_Validator('user_id', [
            'name' => user_exist::class,
            'cancelOnFail' => true
        ]);


        # 验证新手机的正确性
        $this->add_tel('tel', []);
        # 验证手机验证码 new_captcha
        $this->add_Validator('tel', [
            'name' => \logic\Common\Verification\TelValidator::class,
            'message' => 'Verification',
            'captcha_name' => 'captcha',
            'prefix' => 'ttc',
            'cancelOnFail' => true,
        ]);
        return parent::initialize();
    }
}