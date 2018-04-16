<?php

namespace logic\user;

use app\Base;

use core\Sundry\Trace;
use app\model\user_info;
use app\validation\edit_info;
use PayPal\Api\FileAttachment;
use tool\Arr;

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
    public function edit($user_id, $data)
    {
        $data['user_id'] = $user_id;
        Trace::add('info', $data);
        $validation = new edit_info();
        if (!$validation->validate($data)) {
            return $validation->getMessage();
        }

        # 验证完成,
        $mode = user_info::findFirstByuser_id($user_id);
        if (!($mode instanceof user_info)) {
            $mode = new user_info();
        }
        # 数据处理
        $attachmentArray = new \logic\Attachment\attachmentArray();
        $data['lock'] = 0;
        $data['headimg'] = $attachmentArray->one($data['user_id'], 'headimg', $mode->headimg, $data['headimg']);

        Trace::add('info', [$data, $mode->toArray()]);
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
        $Builde = $this->modelsManager->createBuilder();
        $mode = $Builde->from(['user_info' => user_info::class])
            ->join(\app\model\user::class, 'user_info.user_id = user.id', 'user')
            ->columns('user_info.*,user.*')
            ->where('user_id=:user_id:', ['user_id' => $user_id])
            ->getQuery()->execute();

        if (empty($mode->toArray())) {
            return [];
        }
        $info = $mode->toArray();
        $info2 = $info[0];
        $info2['user_info']->headimg = \logic\Attachment\attachmentArray::list4id($info2['user_info']->headimg);
        return $info2;

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

        $info2['headimg'] = \logic\Attachment\attachmentArray::list4id($info2['headimg']);
        return $info2;
    }

}