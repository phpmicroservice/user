<?php

namespace app\logic\service;

use core\CoreService;
use app\model\user_message;

/**
 * Class Message 用户消息的服务层
 * @package logic\user\service
 */
class Message extends CoreService
{
    /**
     * 发送消息
     * @param $user_id 发送人
     * @param $to_id 接收人
     * @param $title 标题
     * @param $content 内容
     */
    public static function sendMsg($user_id, $to_id, $title, $content, $type = 'text')
    {
        $data = [
            'type' => $type,
            'title' => $title,
            'content' => $content,
            'from_uid' => $user_id,
            'to_uid' => $to_id,
            'create_time' => time(),
            'is_read' => 0,
            'is_del' => 0
        ];
        $validation = new \app\validation\user_message();
        if ($validation->validate($data) === false) {
            return $validation->getMessage();
        }
        $model = new user_message();
        $model->setData($data);
        if ($model->save() === false) {
            return $model->getMessage();
        }
        return true;
    }

    /**
     * 用户消息列表
     * @param $user_id 用户id
     * @param string $type 类型 to from
     * @param int $page
     * @param int $row
     */
    public function messages($user_id, $type = 'to', $is_read = -1, $page = 1, $row = 10)
    {
        $where = [];
        if ($type == 'to') {
            $where['from_uid'] = $user_id;

        } else {
            $where['to_uid'] = $user_id;
        }
        if ($is_read > -1) {
            $where['is_read'] = $is_read;
        }

        $modelsManager = \Phalcon\Di::getDefault()->get('modelsManager');
        $builder = $modelsManager->createBuilder()
            ->from(\app\model\user_message::class)
            ->orderBy("is_read asc,id DESC");
        $builder = $this->call_where($builder, $where);
        $paginator = new \Phalcon\Paginator\Adapter\QueryBuilder(
            [
                "builder" => $builder,
                "limit" => $row,
                "page" => $page,
            ]
        );
        $data = $paginator->getPaginate();
        return $this->call_list($data);
    }

    /**
     * 处理where条件
     * @param $builder
     * @param $where
     */
    public function call_where(\Phalcon\Mvc\Model\Query\BuilderInterface $builder, $where)
    {
        if (isset($where['to_uid'])) {
            $builder->andWhere('to_uid=:to_uid:', ['to_uid' => $where['to_uid']]);
        }
        if (isset($where['from_uid'])) {
            $builder->andWhere('from_uid=:from_uid:', ['from_uid' => $where['from_uid']]);
        }
        if (isset($where['is_read'])) {
            $builder->andWhere('is_read=:is_read:', ['is_read' => $where['is_read']]);
        }
        return $builder;

    }

    /**
     * 处理列表
     * @param $data
     * @return mixed
     */
    private function call_list($data)
    {
        $array = $data->items->toArray();
        $array = \tool\Arr::for_index($array, ['from_uid', 'to_uid'], function ($id_list) {
            return \app\logic\User::get_userlist_uidarr($id_list);
        });


        $data->items = $array;
        return $data;
    }

    /**
     * 将消息设置为已读
     * @param $user_id
     * @param $id
     */
    public function readMsg($user_id, $id)
    {
        $where = [
            'conditions' => 'to_uid = :user_id: and id= :id:',
            'bind' => [
                "user_id" => $user_id,
                'id' => $id
            ]
        ];
        $model = user_message::findFirst($where);
        if ($model === false) {
            return '_Information that doesn~t exist';
        }
        $model->is_read = 1;
        if ($model->update() === false) {
            return $model->getMessage();
        }
        return true;

    }
}