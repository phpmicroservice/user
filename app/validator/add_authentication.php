<?php


namespace app\validator;

use app\model\user_authentication;


/**
 * 进行 增加验证
 * Class add_authentication
 * @package app\validator
 */
class add_authentication extends \Phalcon\Validation\Validator
{

    /**
     * 执行验证
     *
     * @param \Phalcon\Validation $validator
     * @param string $attribute
     * @return boolean
     */
    public function validate(\Phalcon\Validation $validation, $attribute)
    {

        //验证是否存在未审核的申请
        $where = [
            'conditions' => 'user_id = ' . $validation->getValue('user_id') . ' and status = 0 '
        ];
        $user_authenticationmodel = new user_authentication();
        $datamodel = $user_authenticationmodel->findFirst($where);

        if (!empty($datamodel)) {
            $validation->appendMessage(
                new \Phalcon\Validation\Message("add_authentication", $attribute, 'exist')
            );
            return false;
        }
        //验证是否已经审核通过
        $where = [
            'conditions' => 'user_id = ' . $validation->getValue('user_id') . ' and status = 1 '
        ];
        $user_authenticationmodel = new user_authentication();
        $datamodel = $user_authenticationmodel->findFirst($where);

        if (!empty($datamodel)) {
            $validation->appendMessage(
                new \Phalcon\Validation\Message("add_authentication", $attribute, 'become')
            );
            return false;
        }
        return true;
    }
}