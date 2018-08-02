<?php

namespace app\logic;

use app\Base;


use app\filterTool\Editinfo;
use app\model\user_info;
use app\validation\edit_info;
use PayPal\Api\FileAttachment;
use funch\Arr;

/**
 * 信息
 * Class Info
 * @package logic\user
 */
class Info extends Base
{
    /**
     * 昵称模糊搜索用户名列表
     */
    public static function nick_link2user_idlist($nickname)
    {
        $RCache = \Phalcon\Di::getDefault()->getShared('RCache');
        return $RCache->getc([__FUNCTION__, $nickname], function () use ($nickname) {
            $list = user_info::find([
                'nickname LIKE :nickname:',
                'bind' => [
                    'nickname' => '%' . $nickname . '%'
                ],
                'columns' => 'user_id'
            ]);
            return Arr::column($list->toArray(), 'user_id');
        }, 10);
    }

    /**
     * 编辑信息
     * @param $user_id
     * @param $data
     */
    public function  edit($user_id, $data)
    {
        $data['user_id'] = $user_id;
        #过滤
        $ft=new  Editinfo();
        $ft->filter($data);
        # 验证
        $validation = new edit_info();
        if (!$validation->validate($data)) {
            return $validation->getErrorMessages();
        }

        # 验证完成,
        $mode = user_info::findFirstByuser_id($user_id);
        if (!($mode instanceof user_info)) {
            $mode = new user_info();
        }

        #进行增加或者编辑
        $mode->setData($data);
        if ($mode->save($data) === false) {
            return $mode->getMessage();
        }
        return true;
    }

    /**
     * 用户信息
     * @param $user_id
     * @return mixed
     */
    public function info_user($user_id)
    {
        # 读取基本信息
       $info=\app\model\user::findFirst($user_id);

       if($info instanceof \app\model\user){
           $info2=user_info::findFirstByuser_id($user_id);
           if($info2 instanceof  user_info){
               return array_merge($info->toArray(),$info2->toArray());
           }else{
               return $info->toArray();
           }

       }else{
           return [];
       }
    }

    /**
     * 获取用户的公共信息
     * @param $user_id
     */
    public function p_info($user_id)
    {
        # 读取基本信息
        $Builde = $this->modelsManager->createBuilder();
        $mode = $Builde->from(['user_info' => user_info::class])
            ->join(\app\model\user::class, 'user_info.user_id = user.id', 'user')
            ->where('user_info.user_id=:user_id:', ['user_id' => $user_id])
            ->columns('user_info.gender as gender,            user_info.user_id as user_id,  user_info.birthday as birthday,user_info.personalized as personalized,user_info.area as area,user_info.headimg as headimg,user_info.nickname as nickname,user.username as username')
            ->getQuery()->execute();

        if (empty($mode->toArray())) {
            return [];
        }
        $info = $mode->toArray();
        $info2 = $info[0];


        return $info2;
    }

}