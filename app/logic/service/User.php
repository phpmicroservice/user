<?php

namespace app\logic\service;

use app\Base;
use logic\user\Info;

class User extends Base
{
    /**
     * 用户信息
     * @param $user_id
     * @param array $config
     * @return mixed
     */
    public static function info($user_id, $config = []): array
    {


        $info = \app\model\user::findFirst(
            [
                'conditions' => 'id = :id:',
                'bind' => ['id' => $user_id],
                'columns' => ['username', 'email', 'id']
            ]
        );
        return $info->toArray();
    }

    /**
     * 判断用户是否存在
     * @param $user_id 用户ID
     *
     * @return bool
     */
    public static function is_exist($user_id)
    {
        //$re1= \app\model\User::findFirst();
        $re1 = \app\model\user::findFirstById($user_id);
        if ($re1) {
            return true;
        } else {
            return false;
        }

    }

    /**
     * 用户列表
     * @param $user_name
     * @param $page
     * @param $row
     * @return array
     */
    public function user_list($where, $page, $row = 10)
    {

        $modelsManager = $this->modelsManager;
        $builder = $modelsManager->createBuilder()
            ->from(\app\model\user::class)
            ->columns('id,username,email,forbid,init,edit_username,create_time')->orderBy("id");

        $builder = $this->call_where($builder, $where);
        $paginator = new \pms\Paginator\Adapter\QueryBuilder(
            [
                "builder" => $builder,
                "limit" => $row,
                "page" => $page,
            ]
        );
        $list = $paginator->getPaginate();
        $list->items = $list->items->toArray();
        return $list;
    }

    /**
     * 处理where条件
     * @param $builder
     * @param $where
     */
    private function call_where(\Phalcon\Mvc\Model\Query\Builder $builder, $where)
    {
        if (isset($where['user_name'])) {
            $builder->where('username like :username:', ['username' => "%" . $where['user_name'] . '%']);
        }
        if (isset($where['user_id']) && $where['user_id'] > 0) {
            $builder->where('id =:user_id:', ['user_id' => $where['user_id']]);
        }
        if (isset($where['nickname']) && !empty($where['nickname'])) {
            $user_id_list = Info::nick_link2user_idlist($where['nickname']);
            $builder->inWhere('id', $user_id_list);


        }
        return $builder;
    }

    /**
     * 重置用户密码
     * @param $user_id
     */
    public function reset_password($user_id)
    {
        $userModel = new \app\model\user();
        $data = $userModel->findFirstById($user_id);
        if ($data === false) {
            return "_user-inexistence";
        }
        $security = new \Phalcon\Security();
        //密码加密
        $data->password = $security->hash('123456', 2);
        $data->update_time = time();
        if ($data->save() === false) {
            return "_save-error";
        }
        return true;
    }

    /**
     * 禁用会员
     * @param $user_id
     */
    public function forbid($user_id)
    {
        $userModel = new \app\model\user();
        $data = $userModel->findFirstById($user_id);
        if ($data === false) {
            return "_user-inexistence";
        }
        $data->update_time = time();
        $data->forbid = 1;
        if ($data->save() === false) {
            return "_save-error";
        }
        return true;
    }

    /**
     * 取消禁用
     * @param $user_id
     * @return bool|string
     */
    public function clear_forbid($user_id)
    {
        $userModel = new \app\model\user();
        $data = $userModel->findFirstById($user_id);
        if ($data === false) {
            return "_user-inexistence";
        }
        $data->update_time = time();
        $data->forbid = 0;
        if ($data->save() === false) {
            return "_save-error";
        }
        return true;
    }

    public function edit_password()
    {

    }
}