<?php

namespace app\controller;

use app\Controller;


/**
 * 管理员控制器
 * Class Admin
 * @package app\controller
 */
class Admin extends Controller
{

    /**
     * 重置密码
     */
    public function reset_password()
    {
        $user_id = $this->getData('user_id');
        $ser = new \app\logic\service\User();
        $re = $ser->reset_password($user_id);
        $this->send($re);
    }


    /**
     * id列表读取信息列表
     */
    public function idl2infol()
    {
        $list = $this->getData('id_list');
        $ser = new \app\logic\User();
        $infolist = $ser->get_userlist_uidarr($list);
        $this->send($infolist);

    }

    /**
     * 查找用户
     * @return \Phalcon\Http\Response|\Phalcon\Http\ResponseInterface
     */
    public function user_list()
    {
        $where = [];
        $username = $this->getData('user_name');
        if ($username) {
            $where['user_name'] = $username;
        }
        $page = $this->getData('p');
        $service = new \app\logic\User();
        $re = $service->user_list($where, $page);
        return $this->send($re);
    }


    public function add_user()
    {
        $data = $this->getData();
        $Reg = new \app\logic\Reg();
        $re = $Reg->add($data);
        return $this->send($re);
    }
}