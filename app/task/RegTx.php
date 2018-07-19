<?php

namespace app\task;

use pms\Task\TaskInterface;

class RegTx extends \pms\Task\TxTask implements TaskInterface
{
    public function end()
    {

    }

    /**
     * 在依赖处理之前执行,没有返回值
     */
    protected function b_dependenc()
    {

    }

    /**
     * 事务逻辑内容,返回逻辑执行结果,
     * @return bool false失败,将不会再继续进行;true成功,事务继续进行
     */
    protected function logic(): bool
    {
        $data = $this->getData();
        # 进行验证
        $validation = new \app\validation\add_user();
        if (!$validation->validate($data)) {
            return $validation->getMessage();
        }
        # 验证完成
        $userModel = new \app\model\user();
        $security = new \Phalcon\Security();
        //密码加密
        $data['password'] = $security->hash($data['password'], 2);
        //进行注册 增加用户信息
        $data['create_time'] = time();
        $data['update_time'] = 0;
        $re33 = $userModel->save($data);
        if ($re33 === false) {
            return $userModel->getMessage();
        }
        # 进行注册后的操作
        $re42 = $this->initReg($userModel->id, $data);
        if (is_string($re33)) {
            return $re42;
        }
        return ['id' => $userModel->id];
    }
}