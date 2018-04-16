<?php


namespace logic\user\validation;

use logic\user\model\user_tel;
use logic\user\validator\user_exist;

/**
 * 手机修改验证 验证集
 * Class edit_tel
 * @package logic\user\validation
 */
class edit_tel extends \pms\CoreValidation
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
        # 验证原手机号码
        $this->add_where('old_tel', [
            'model' => user_tel::class,
            'cancelOnFail' => true,
            'wheres' => [
                'user_id' => 'user_id',
                'tel' => 'old_tel'
            ]
        ]);
        # 验证手机验证码
        $this->add_Validator('old_tel', [
            'name' => \logic\Common\Verification\TelValidator::class,
            'message' => 'Verification',
            'captcha_name' => 'captcha',
            'prefix' => 'tet',
            'cancelOnFail' => true,
        ]);

        # 验证新手机的正确性
        $this->add_tel('new_tel', []);
        # 验证手机验证码 new_captcha
        $this->add_Validator('new_tel', [
            'name' => \logic\Common\Verification\TelValidator::class,
            'message' => 'Verification',
            'captcha_name' => 'new_captcha',
            'prefix' => 'ttc',
            'cancelOnFail' => true,
        ]);
        return parent::initialize();
    }
}