<?php
/**
 * Created by PhpStorm.
 * User: saisai
 * Date: 17-7-13
 * Time: 上午11:20
 */

namespace logic\user;


use core\Sundry\Trace;

class role extends \Phalcon\Di\Injectable
{
    /**
     * 服务商 审核不通过 不进行操作
     * @param $data
     * @return bool
     */
    public function facilitator_no($data)
    {

        return true;
    }

    /**
     * 服务商状态重置
     * @param $data
     * @return bool
     */
    public function facilitator_re($data)
    {

        $re = \logic\user\service\Role::user_del_role(4, $data['user_id']);
        if (is_string($re)) {
            Trace::add('error_info', [$re, $data]);

            return false;
        }
    }

    /**
     * 服务商 审核成功
     * @param $data
     * @return bool|string
     */
    public function facilitator_ok($data)
    {
        # 通过之后给予服务商用户组
        $re = \logic\user\service\Role::add_user(4, $data['user_id']);
        if (is_string($re)) {
            \core\Sundry\Trace::add('error-info', $re);
            return false;
        }
        return true;
    }

    public function client_re($data)
    {

        # 不通过删除用户组
        $re = \logic\user\service\Role::user_del_role(3, $data['user_id']);
        if (is_string($re)) {
            \core\Sundry\Trace::add('error-info', $re);
            return false;
        }
        return true;

    }
}