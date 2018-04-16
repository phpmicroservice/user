<?php

namespace logic\user;

use Phalcon\Paginator\Adapter\QueryBuilder;

/**
 * Class praise
 * 速评
 * @package logic\user
 */
class praise extends \app\Base
{
    /**
     * 获取速评信息
     * @param $id 对象的id
     * @param $type 对象的类型
     * @param $user_id 用户id
     */
    public static function info($id, $type, $user_id)
    {

        $info = model\user_praise::findFirst([
            'to_type= :to_type: and to_numerical =:to_numerical: and user_id=:user_id:',
            'bind' => [
                'to_type' => $type,
                'to_numerical' => $id,
                'user_id' => $user_id
            ]
        ]);

        if ($info === false) {
            return [];
        }
        return $info->toArray();
    }

    /**
     * 增加
     * @param $re_id
     * @param $type
     * @param $user_id
     */
    public function add($data)
    {
        return service\Praise::add($data);
    }

    /**
     * 取消
     * @param $user_id
     * @param $p_id
     * @return bool|string
     */
    public function clear($user_id, $p_id)
    {
        return service\Praise::clear($user_id, $p_id);
    }

    /**
     * @return mixed
     */
    public function clear2($data)
    {
        return service\Praise::clear2($data);
    }

    /**
     * @param $where
     * @param $page
     * @param int $row
     */
    public function praise_list($where, $page, $row = 10)
    {
        $service = new service\Praise();
        return $service->praise_list($where, $page, $row);
    }

}