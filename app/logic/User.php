<?php

namespace app\logic;

use app\validation\edit_username;
use Phalcon\Di;

/**
 * Description of user
 * 当前用户的操作
 * @author Dongasai
 */
class User extends \app\Base
{


    /**
     * 根据user_id数组获取用户列表 用uid作为索引的
     * @param $uidarr
     * @return array
     */
    public static function get_userlist_uidarr($uidarr)
    {
        $list = \app\model\user::get_for_list($uidarr);
        $list2 = \funch\Arr::array_change_index($list->toArray(), 'id');
        return $list2;
    }

    /**
     * 用户信息
     * @param $user_id
     */
    public static function user_info($user_id): array
    {
        $model = \app\model\user::user_info($user_id);
        if (is_object($model)) {
            return $model->toArray();
        } else {
            return [];
        }

    }


    /**
     * 修改用户昵称
     * @param $user_id
     * @param $new_username
     */
    public function edit_username($user_id, $new_username)
    {
        $validation = new edit_username();
        if (!$validation->validate(['user_id' => $user_id, 'username' => $new_username, 'edit_username' => 0])) {
            return $validation->getMessage();
        }


        # 验证通过
        $userModel = \app\model\user::findFirstById($user_id);
        $userModel->username = $new_username;
        $userModel->edit_username = 1;
        if (!$userModel->save()) {
            return $userModel->getMessage();
        }
        return true;
    }

    /**
     *
     * 初始化用户
     */
    public function init_user()
    {
        # 读取等待初始化的用户
        $user_list = \app\model\user::find([
            'init = 0',
            'limit' => 10
        ]);

        foreach ($user_list as $user) {


            $event = Event::getInstance();
            $re = $event->notice('user_init', $user->toArray(), 1);
            if ($re === true) {
                $user->init = 1;
                $user->save();
            }
        }
    }

    /**
     * 查找用户
     * @param $username
     */
    public function find_user($username)
    {
        $service = new  service\User();
        return $service->user_list(['user_name' => $username], 1, 20);
    }

}
