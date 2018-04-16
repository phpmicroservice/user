<?php

namespace logic\user;

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
        $list = model\user::get_for_list($uidarr);
        $list2 = \tool\Arr::array_change_index($list->toArray(), 'id');
        return $list2;
    }

    /**
     * 用户信息
     * @param $user_id
     */
    public static function user_info($user_id): array
    {
        $model = model\user::user_info($user_id);
        if (is_object($model)) {
            return $model->toArray();
        } else {
            return [];
        }

    }

    public static function in_role($roles)
    {
        $roles_user = self::role();

        if (is_array($roles)) {
            foreach ($roles as $role) {
                if (array_key_exists($role, $roles_user)) {
                    return $role;
                }
            }

        } else {
            if (array_key_exists($roles, $roles_user)) {
                return $roles;
            }
        }
        return false;
    }

    /**
     * 读取当前用户的权限列表
     * @return array
     */
    public static function role()
    {
        $session = Di::getDefault()->getShared('session');
        $roles = $session->get('roles' . self::is_login());
        if (1) {
            # 没能读取用户的 角色信息
            # 读取一下
            $roles = \logic\rbac\Role::user(self::is_login());
            # 增加一个游客权限
            $roles['visitor'] = 0;
            $session = Di::getDefault()->getShared('session');
            $session->set('roles' . self::is_login(), $roles);
        }
        return $roles;
    }

    /**
     * 判断是否登录 返回uid
     * @return number
     */
    public static function is_login(): int
    {
        $session = Di::getDefault()->getShared('session');
        $re = $session->get('user_id');
        if ($re) {
            return $re;
        }
        return 0;
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
