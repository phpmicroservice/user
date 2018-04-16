<?php

namespace logic\user;

use core\CoreValidation;

use logic\user\validator\add_authentication;

/**
 * 认证的服务层
 * Class Authentication
 * @package logic\user\service
 * @property service\Authentication\client $client
 * @property service\Authentication\facilitator $facilitator
 */
class Authentication extends \app\Base
{

    /**
     * 初始化
     * Authentication constructor.
     */
    public function __construct()
    {
        $this->di->set('client', function () {
            return new service\Authentication\client();
        });
        $this->di->set('facilitator', function () {
            return new service\Authentication\facilitator();
        });
    }

    /**
     * 服务商认证通过
     * @return mixed
     */
    public function facilitator_ok($user_id)
    {
        return $this->facilitator->facilitator_ok($user_id);
    }


    /**
     * 服务商认证状态重置
     * @return bool|string
     */
    public function facilitator_re($user_id)
    {
        return $this->facilitator->facilitator_re($user_id);
    }

    /**
     * 服务商认证 不通过
     * @return mixed
     */
    public function facilitator_no($user_id)
    {
        return $this->facilitator->facilitator_no($user_id);
    }

    public function facilitator_info($user_id)
    {
        return $this->facilitator->facilitator_info($user_id);
    }

    /**
     * 客户认证通过
     * @param $user_id
     */
    public function client_ok($user_id)
    {
        return $this->client->client_ok($user_id);
    }

    /**
     * @param $user_id
     * @return mixed
     */
    public function client_re($user_id)
    {
        return $this->client->client_re($user_id);
    }

    /**
     * 客户不通过
     * @param $user_id
     */
    public function client_no($user_id)
    {
        return $this->client->client_no($user_id);
    }


    /**
     * 客户认证列表
     * @param $page
     * @param int $row
     * @return \stdClass
     */
    public function client_list($page, $row = 10)
    {

        $page = $this->client->lists($page, $row);
        return $page;
    }

    /**
     * 客户认证信息
     * @param $user_id
     * @return array
     */
    public function client_info($user_id)
    {
        $info = $this->client->client_info($user_id);
        return $info;
    }

    public function facilitator_list($where, $page = 1, $row = 10)
    {
        $facilitator = new service\Authentication\facilitator();
        $page = $facilitator->lists($where, $page, $row);
        return $page;
    }


    /**
     * 进行服务商认证,第一步
     */
    public function facilitator_1($user_id, $data)
    {
        $facilitator = new service\Authentication\facilitator();
        $this->transactionManager->get();# 启动一个事务
        # 先增加记录
        $re21 = $facilitator->get_authentication_facilitator($user_id);
        if (is_string($re21)) {
            $this->transactionManager->rollback();
            return \core\ReturnMsg::create(400, $re21, $data);
        }

        # 再增加信息
        $re30 = $facilitator->add_authentication_facilitator_1($re21, $user_id, $data);

        if (is_string($re30)) {
            return \core\ReturnMsg::create(400, $re30, $data);
        }
        $this->transactionManager->commit();
        return true;
    }

    /**
     * 进行服务商认证,第二步
     * @param $uid
     * @param $data
     */
    public function facilitator_2($user_id, $data)
    {

        $facilitator = new service\Authentication\facilitator();
        $this->transactionManager->get();# 启动一个事务
        # 先获取认证记录 并且修改认证状态
        $re21 = $facilitator->get_authentication_facilitator($user_id);
        if (is_string($re21)) {
            $this->transactionManager->rollback();
            return $re21;
        }

        # 再增加信息
        $re30 = $facilitator->add_authentication_facilitator_2($re21, $user_id, $data);
        if (is_string($re30)) {
            return $re30;
        }

        $this->transactionManager->commit();
        return true;
    }

    /**
     * 服务商认证第三步
     * @param $user_id
     */
    public function facilitator_3($user_id)
    {
        $facilitator = new service\Authentication\facilitator();
        # 验证
        $validation = new \pms\Validation();
        $validation->add_Validator('user_id', [
            'name' => validator\facilitator_3::class,
            'message' => 'facilitator_3'
        ]);
        if (!$validation->validate(['user_id' => $user_id])) {
            return $validation->getMessage();
        }
        $this->transactionManager->get();# 启动一个事务
        # 获取当前认证信息
        $dataModel = $facilitator->get_auth_now_facilitator($user_id);
        $dataModel->status = 1;
        if ($dataModel->save() === false) {
            $this->transactionManager->rollback();
            return $dataModel->getMessage();
        }
        $this->transactionManager->commit();
        return true;
    }


    /**
     * 进行客户认证
     * @param $uid
     * @param $data
     */
    public function client($uid, $data)
    {
        $this->transactionManager->get();# 启动一个事务
        $data['user_id'] = $uid;
        # 增加信息
        $re30 = $this->add_authentication_client($data);
        if (is_string($re30)) {
            $this->transactionManager->rollback();
            return $re30;
        }
        $this->transactionManager->commit();
        return true;
    }

    /**
     * 增加申请信息
     * @param $id
     * @param $data
     */
    private function add_authentication_client($data)
    {
        $validation = new \logic\user\validation\authentication_info_client();
        $validation->validate($data);
        if ($validation->isError()) {
            return $validation->getMessage();
        }

        # 验证通过

        # 进行数据处理
        $attachment = new \logic\Common\attachment();


        $user_authentication_info_client = new \logic\user\model\user_authentication_client();
        $old_date = $user_authentication_info_client->findFirst(
            ['conditions' => 'user_id = :user_id:', 'bind' => ['user_id' => $data['user_id']]]
        );

        $data = $attachment->dispose_data_edit($data['user_id'], $data, 'headimg', $old_date->headimg, 'client_headimg', true);

        if (empty($old_date)) {
            # 不存在旧数据
            $data['status'] = 0;
            $user_authentication_info_client->setData($data);
            if ($user_authentication_info_client->save() === false) {
                return $user_authentication_info_client->getMessage();
            }
            return (int)$user_authentication_info_client->id;
        } else {
            # 存在申请
            if ($old_date->status != 0) {
                return '_prohibition';
            }

            $old_date->setData($data);
            if ($old_date->update() === false) {

                return $old_date->getMessage();
            }
            return (int)$old_date->id;
        }
    }

    /**
     * 客户申请认证
     * @param $user_id
     * @return bool
     */
    public function client_next($user_id)
    {
        $modelData = model\user_authentication_client::findFirst([
            'user_id = :user_id:',
            'bind' => ['user_id' => $user_id]
        ]);
        $modelData->status = 1;
        if ($modelData->save() === false) {
            return $modelData->getMessage();
        }
        return true;
    }

    /**
     * 增加一个申请记录
     * @param $user_id
     * @param $type
     */
    private function add_authentication($user_id, $type)
    {
        $data = [
            'user_id' => $user_id,
            'type' => $type
        ];
        # 进行验证 事务验证 是否可增加验证
        $va = new CoreValidation();
        $pa = [
            'name' => add_authentication::class,
            'message' => 'add_authentication',
        ];
        $va->add_transaction('user_id');
        $va->validate($data);
        if ($va->isError()) {
            return $va->getMessage();
        }
        $user_authentication = new \logic\user\model\user_authentication();
        $data['create_time'] = time();
        $data['update_time'] = 0;
        $data['status'] = 0;
        $user_authentication->setData($data);
        if ($user_authentication->save() === false) {
            return $user_authentication->getMessage();
        }
        return (int)$user_authentication->id;
    }

}