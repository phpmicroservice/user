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
}