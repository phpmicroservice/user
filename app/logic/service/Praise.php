<?php

namespace logic\user\service;

use core\Paginator\Adapter\QueryBuilder;
use logic\user\model as thisModel;

/**
 * 用户点赞 (速评) 的服务层
 *
 * @author Dongasai
 */
class Praise extends \app\Base
{
    /**
     * 取消
     * @param $data
     * @return bool|string
     */
    public static function clear2($data)
    {
        $oldModel = thisModel\user_praise::findFirst(
            [
                'user_id=:user_id: and to_numerical=:to_numerical: and to_type =:to_type:',
                'bind' => [
                    'user_id' => $data['user_id'],
                    'to_numerical' => $data['to_numerical'],
                    'to_type' => $data['to_type']
                ]
            ]
        );
        if (!$oldModel) {
            return '_empty-error';
        }
        if ($oldModel->delete() === false) {
            return $oldModel->getMessage();
        }
        return true;
    }

    /**
     * 增加速评
     * @param type $user_id 评价人
     * @param type $data 评价数据
     */
    public static function add($data)
    {
        $user_praiseModel = new \app\model\user_praise();
        # 进行验证
        $validation = new \app\validation\Praise();
        $validation->setRepetition($user_praiseModel, $data); # 设置重复验证
        $validation->validate($data);
        if ($validation->getMessage()) {
            return $validation->getMessage();
        }
        $data['create_time'] = time();
        $data['update_time'] = 0;
        $re = $user_praiseModel->save($data);
        if ($re === false) {
            return $user_praiseModel->getMessage();
        }
        return TRUE;
    }

    /**
     * 速评列表
     * @param $where
     * @param $page
     * @param $row
     */
    public function praise_list($where, $page, $row = 15)
    {


        $builder = $this->modelsManager->createBuilder()
            ->from(thisModel\user_praise::class);
        #  速评对象的类型
        if (isset($where['to_type'])) {
            $builder->andWhere('to_type=:to_type:', [
                'to_type' => $where['to_type']
            ]);
        }

        // 速评的对象的编号
        if (isset($where['to_numerical'])) {
            $builder->andWhere('to_numerical=:to_numerical:', [
                'to_numerical' => $where['to_numerical']
            ]);
        }
        // 速评类型
        if (isset($where['praise_type'])) {
            $builder->andWhere('praise_type=:praise_type:', [
                'praise_type' => $where['praise_type']
            ]);
        }

        $paginator = new QueryBuilder(
            [
                "builder" => $builder,
                "limit" => $row,
                "page" => $page,
            ]
        );
        return $paginator->getPaginate(function ($datastd) {

            $data = $datastd->items->toArray();
            $data = \tool\Arr::for_index($data, ['user_id'], function ($user_id_list) {
                return \logic\user\User::get_userlist_uidarr($user_id_list);
            }, true, 'user_info');
            $datastd->items = $data;
            return $datastd;
        });
    }

    /**
     * 用户取消 速评
     * @param type $user_id
     * @param type $Praise_id
     */
    public function clear($user_id, $Praise_id)
    {
        $oldModel = thisModel\user_praise::findFirst(
            [
                'user_id=:user_id: and id=:id:',
                'bind' => [
                    'user_id' => $user_id,
                    'id' => $Praise_id
                ]
            ]
        );
        if (!$oldModel) {
            return '_empty-error';
        }
        if ($oldModel->delete() === false) {
            return $oldModel->getMessage();
        }
        return true;
    }

}
