<?php

namespace logic\user\service;


use app\model\user_lv;
use app\model\user_lv_config;
use app\model\user_lv_log;

/**
 * 等级服务
 * Class lv
 * @package logic\user\service
 */
class lv extends \app\Base
{
    /**
     * 增加成长值
     * @param $user_id
     * @param $identifying
     * @param $grow_number
     */
    public function add_grow($user_id, $identifying, $grow_number, $beizhu)
    {

        # 先获取等级成长账户
        $accountmodel = $this->get_account($user_id, $identifying);
        if (is_string($accountmodel)) {
            $this->transactionManager->rollback();
            return $accountmodel;
        }
        $this->transactionManager->get();
        # 计算成长和等级关系
        $re = $this->calculate_grow($accountmodel, $grow_number);
        if (is_string($re)) {
            $this->transactionManager->rollback();
            return $re;
        }
        # 保存数据
        if (!$accountmodel->save()) {
            $this->transactionManager->rollback();
            return $accountmodel->getMessage();
        }
        # 增加日志
        $user_lv_log = new user_lv_log();
        $user_lv_log->setData([
            'user_id' => $user_id,
            'identifying' => $identifying,
            'grow_number' => $grow_number,
            'beizhu' => $beizhu
        ]);
        if (!$user_lv_log->save()) {
            $this->transactionManager->rollback();
            return $user_lv_log->getMessage();
        }
        $this->transactionManager->commit();
        return $accountmodel;
    }

    /**
     * 获取成长值账户
     * @param $user_id
     * @param $identifying
     */
    private function get_account($user_id, $identifying)
    {
        $lvModel = user_lv::findFirst([
            'user_id= :user_id: and identifying =:identifying: ',
            'bind' => [
                'user_id' => $user_id,
                'identifying' => $identifying
            ]
        ]);
        if (!($lvModel instanceof user_lv)) {
            # 账户不存在,尝试舰船
            return $this->add_account($user_id, $identifying);
        }
        return $lvModel;
    }

    /**
     * 创建账户
     * @param $user_id
     * @param $identifying
     */
    private function add_account($user_id, $identifying)
    {
        $data = [
            'user_id' => $user_id,
            'identifying' => $identifying,
            'grow' => 0,
            'grade' => 1
        ];
        $validation = new \pms\Validation();
        $validation->add_exist('identifying', [
            'class_name_list' => user_lv_config::class,
            'function_name' => 'findFirstByidentifying'
        ]);
        if (!$validation->validate($data)) {
            return $validation->getMessage();
        }
        # 验证完成,数据写入
        $model = new user_lv();
        if ($model->save($data) === false) {
            return $model->getMessage();
        }
        return $model;
    }

    /**
     * 计算成长等级
     * @param $accountmodel 账户
     * @param $grow_number 成长值
     */
    private function calculate_grow(&$accountmodel, int $grow_number)
    {
        # 读取这个等级模式的计算方式 arithmetic
        $model = user_lv_config::findFirstByidentifying($accountmodel->identifying);
        if ($model->arithmetic == 'increasing') {
            $service = new lv\increasing();
        }
        if ($model->arithmetic == 'qq') {
            $service = new lv\qq();
        }
        if ($model->arithmetic == 'power') {
            $service = new lv\power();
        }

        $accountmodel->grow = bcadd($accountmodel->grow, $grow_number);
        if ($accountmodel->grow < 0) {
            return '_growlt0';
        }
        $accountmodel->grade = $service->grow2grade($accountmodel->grow);


    }


}