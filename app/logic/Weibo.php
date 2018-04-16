<?php

namespace app\logic;

use app\model\user_weibo;

/**
 * 新浪微博
 * Class Weibo
 * @package logic\user
 */
class Weibo extends \app\Base
{
    /**
     * 编辑
     * @param $user_id
     * @param $data
     */
    public function edit($user_id, $data)
    {
        $data['user_id'] = $user_id;
        $model = user_weibo::findFirstByuser_id($user_id);
        if (!($model instanceof user_weibo)) {
            $model = new user_weibo();
        }
        $model->setData($data);
        if ($model->save() === false) {
            return $model->getMessage();
        }
        return true;
    }

    /**
     * 信息
     * @param $user_id
     * @return mixed
     */
    public function info_user($user_id)
    {
        $model = user_weibo::findFirstByuser_id($user_id);
        return $model;
    }

}