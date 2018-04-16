<?php

namespace app\model;

/**
 * Description of User
 *
 * @author Dongasai
 */
class user extends \pms\Mvc\Model
{
    public $username;

    public $init = 0;

    /**
     * 获取用户列表 根据user_id 列表
     * @param $array_list
     * @return \Phalcon\Mvc\Model\ResultsetInterface
     */
    public static function get_for_list($array_list)
    {

        $list = self::query()
            ->columns(['username', 'email', 'id'])
            ->inWhere('id', $array_list)
            ->execute();

        return $list;
    }

    /**
     * 单条用户信息
     * @param $user_id
     * @return \Phalcon\Mvc\Model
     */
    public static function user_info($user_id)
    {
        $info = self::findFirst(
            [
                'conditions' => 'id = :id:',
                'bind' => ['id' => $user_id],
                'columns' => ['username', 'email', 'id']
            ]
        );

        return $info;
    }
}
