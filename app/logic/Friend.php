<?php

namespace logic\user;

use core\Sundry\Trace;
use core\Validator\whereValidator;
use app\model\user_config;
use app\model\user_friend;
use app\model\user_friend_apply;
use app\model\user_friend_config;
use app\validation\add_apply;
use app\validation\add_friend;
use app\validation\friend_set;
use tool\Arr;

class Friend extends \app\Base
{


    /**
     * 增加好友
     * @param $user_id
     * @param $user_id2
     */
    public function add_friend($user_id, $user_id2)
    {
        $validation = new add_friend();
        if (!$validation->validate([
            'user_id' => $user_id,
            'user_id2' => $user_id2,
            'config_name' => 'add_friend_type',
            'config_value' => '0'
        ])) {
            return $validation->getMessage();
        }
        # 验证通过

        # 增加好友
        $service = new service\friend();
        $re = $service->add_friend($user_id2, $user_id);
        if (is_string($re)) {
            return $re;
        }

        return true;
    }

    /**
     * 用户加好友验证的类型
     *
     */
    public function get_add_friend_type($user_id, $user_id2)
    {
        $info = user_config::findFirst([
            "config_name =:config_name: and user_id =:user_id: ",
            'bind' => [
                'config_name' => 'add_friend_type',
                'user_id' => $user_id2
            ]
        ]);
        if ($info instanceof user_config) {
            return (int)$info->config_value;
        }

        $info = new user_config();
        $info->setData([
            'config_name' => 'add_friend_type',
            'user_id' => $user_id2,
            'config_value' => '1'
        ]);
        if (!$info->save()) {
            return $info->getMessage();
        }
        return (int)$info->config_value;
    }


    /**
     * 好友设置信息
     * @param $user_id
     * @param $fr_user_id
     * @param null $field
     */
    public function friend_set_info($user_id, $fr_user_id)
    {

        $user_f_m_list = user_friend_config::find([
            'user_id = :user_id: and user_id2 =:user_id2:',
            'bind' => [
                'user_id' => $user_id,
                'user_id2' => $fr_user_id
            ]
        ]);

        return array_column($user_f_m_list->toArray(), 'config_value', 'config_name');
    }


    /**
     * 进行好友设置
     * @param $user_id
     * @param $fr_user_id
     * @param $field
     * @param $value
     */
    public function friend_set(int $user_id, int $fr_user_id, string $field, int $value)
    {


        $validation = new friend_set();
        if (!$validation->validate([
            'user_id' => $user_id,
            'user_id2' => $fr_user_id,
            'field' => $field,
            'value' => $value
        ])) {
            return $validation->getMessage();

        }
        # 读取用户
        # 查找好友
        $user_f_m = user_friend_config::findFirst([
            'user_id = :user_id: and user_id2 =:user_id2: and config_name=:field:',
            'bind' => [
                'user_id' => $user_id,
                'user_id2' => $fr_user_id,
                'field' => $field
            ]
        ]);

        if (!($user_f_m instanceof user_friend_config)) {
            $user_f_m = new user_friend_config();
            $user_f_m->user_id = $user_id;
            $user_f_m->user_id2 = $fr_user_id;
            $user_f_m->config_name = $field;
        }

        $user_f_m->config_value = $value;
        if (!$user_f_m->save()) {
            return $user_f_m->getMessage();
        }
        return true;
    }

    /**
     * 删除好友
     * @param $user_id
     * @param $fr_user_id
     */
    public function del_friend($user_id, $fr_user_id)
    {
        # 查找好友 # 增加好友
        $service = new service\friend();
        $re = $service->del_friend($fr_user_id, $user_id);
        if (is_string($re)) {
            return $re;
        }
        return true;
    }

    /**
     * 好友列表
     * @param $where
     * @param $page
     * @return \stdClass
     */
    public function listf($where, $page)
    {

        $builder = $this->modelsManager->createBuilder()
            ->from(user_friend::class);
        if (isset($where['user_id'])) {
            $builder->andWhere('user_id = :user_id:', [
                'user_id' => $where['user_id']
            ]);
        }
        if (isset($where['user_id2'])) {
            $builder->andWhere('user_id2 = :user_id2:', [
                'user_id2' => $where['user_id2']
            ]);
        }
        $paginator = new \core\Paginator\Adapter\QueryBuilder(
            [
                "builder" => $builder,
                "limit" => 100,
                "page" => $page,
            ]
        );

        return $paginator->getPaginate(function ($data) {
            $array = $data->items->toArray();
            $array = \tool\Arr::for_index($array, ['user_id', 'user_id2'], function ($id_list) {
                return \logic\user\User::get_userlist_uidarr($id_list);
            });
            $data->items = $array;
            return $data;
        });
    }


    /**
     * @param $user_id
     * @param string $exclude
     * @return array
     */
    public function list_id($user_id, $ex = [])
    {
        $Builder = $this->modelsManager->createBuilder()
            ->from(user_friend::class)
            ->where('user_id =:user_id:', ['user_id' => $user_id]);
        if (!empty($ex)) {
            $Builder->notInWhere('user_id2', $ex);
        }
        $list = $Builder->getQuery()->execute();
        return \tool\Arr::column($list->toArray(), 'user_id2');

    }


    /**
     * 根据配置选好友id
     * @param $user_id
     * @param $name
     * @param $value
     */
    public function list_id_config($user_id, $name, $value)
    {
        $list = user_friend_config::find([
            'user_id =:user_id: and config_name=:name: and config_value =:value:',
            'bind' => [
                'user_id' => $user_id,
                'name' => $name,
                'value' => $value
            ],
            'column' => 'user_id2'
        ]);
        if ($list) {

            return \tool\Arr::column($list->toArray(), 'user_id2');
        }
        return [];

    }

    /**
     * 好友验证列表
     * @param $where
     * @param $page
     * @return \stdClass
     */
    public function apply_list($where, $page)
    {
        $builder = $this->modelsManager->createBuilder()
            ->from(user_friend_apply::class);
        if (isset($where['user_id'])) {
            $builder->andWhere('user_id = :user_id:', [
                'user_id' => $where['user_id']
            ]);
        }
        if (isset($where['user_id2'])) {
            $builder->andWhere('user_id2 = :user_id2:', [
                'user_id2' => $where['user_id2']
            ]);
        }
        if (isset($where['status'])) {
            $builder->andWhere('status = :status:', [
                'status' => $where['status']
            ]);
        }
        $paginator = new \Phalcon\Paginator\Adapter\QueryBuilder(
            [
                "builder" => $builder,
                "limit" => 20,
                "page" => $page,
            ]
        );
        return $paginator->getPaginate(function ($data) {
            $array = $data->items->toArray();
            $array = \tool\Arr::for_index($array, ['user_id', 'user_id2'], function ($id_list) {
                return \logic\user\User::get_userlist_uidarr($id_list);
            });
            $data->items = $array;
            return $data;
        });
    }

    /**
     * 增加申请
     * @param $user_id
     * @param $data
     */
    public function add_apply($user_id, $data)
    {

        $data['user_id'] = $user_id;
        $data['status'] = 0;
        Trace::add('info', $data);

        $validation = new add_apply();
        if (!$validation->validate($data)) {
            return $validation->getMessage();
        }
        # 验证完成
        $model = new user_friend_apply();
        $data['create_time'] = time();
        $data['handling_time'] = 0;
        $model->setData($data);
        if ($model->save() === false) {
            return $model->getMessage();
        }
        return true;
    }

    /**
     * 拒绝申请
     * @param $user_id
     * @param $id
     * @return bool|string
     * @throws \Phalcon\Validation\Exception
     */
    public function apply_no($user_id, $id)
    {
        $where = [
            'user_id2' => $user_id,
            'id' => $id,
            'status' => 0
        ];
        Trace::add('info', $where);
        $validation = new \pms\Validation();
        # 是否存在未审核的数据
        $validation->add_Validator('user_id', [
            'name' => whereValidator::class,
            'model' => user_friend_apply::class,
            'wheres' => [
                'user_id2' => 'user_id2',
                'id' => 'id',
                'status' => 'status'
            ]
        ]);
        if ($validation->validate($where) === false) {
            return $validation->getMessage();
        }
        $model = user_friend_apply::findFirstByid($id);
        $this->transactionManager->get();
        $model->status = -1;
        $model->handling_time = time();
        if ($model->save() === false) {
            $this->transactionManager->rollback();
            $model->getMessage();
        }
        $this->transactionManager->commit();
        return true;

    }

    /**
     * 验证通过
     * @param $user_id
     * @param $id
     */
    public function apply_ok($user_id, $id, $from_user_id)
    {
        $where = [
            'user_id2' => $user_id,
            'id' => $id,
            'status' => 0
        ];
        Trace::add('info', func_get_args());
        $validation = new \pms\Validation();
        # 是否存在未审核的数据
        $validation->add_Validator('user_id', [
            'name' => whereValidator::class,
            'model' => user_friend_apply::class,
            'wheres' => [
                'user_id2' => 'user_id2',
                'id' => 'id',
                'status' => 'status'
            ],
            'message' => 'audit'
        ]);

        # 是否已经是好友了?
        $validation->add_Validator('user_id', [
            'name' => whereValidator::class,
            'model' => user_friend::class,
            'wheres' => [
                'user_id' => $user_id,
                'user_id2' => $from_user_id
            ],
            'negation' => true,
            'message' => 'ex'
        ]);
        if ($validation->validate($where) === false) {
            return $validation->getMessage();
        }
        # 验证通过
        $model = user_friend_apply::findFirstByid($id);
        $this->transactionManager->get();
        $model->status = 1;
        $model->handling_time = time();
        if ($model->save() === false) {
            $this->transactionManager->rollback();
            $model->getMessage();
        }
        # 增加好友
        $service = new service\friend();
        $re = $service->add_friend($user_id, $from_user_id);
        if (is_string($re)) {
            $this->transactionManager->rollback();
            return $re;
        }
        $this->transactionManager->commit();
        return true;
    }
}