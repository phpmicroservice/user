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
     * id列表读取信息列表
     */
    public function idl2infol()
    {
        $list =$this->getData('id_list');
        $ser =new \app\logic\User();
        $infolist=$ser->get_userlist_uidarr($list);
        $this->send($infolist);

    }

    /**
     * 查找用户
     * @return \Phalcon\Http\Response|\Phalcon\Http\ResponseInterface
     */
    public function user_list()
    {
        $where=[];
        $username = $this->getData('user_name');
        if($username){
            $where['user_name']=$username;
        }
        $page = $this->getData('p');
        $service = new \app\logic\User();
        $re = $service->user_list($where, $page);
        return $this->send($re);
    }
}