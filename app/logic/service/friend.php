<?php
/**
 * Created by PhpStorm.
 * User: Dongasai
 * Date: 2018/1/8
 * Time: 18:40
 */

namespace logic\user\service;


use app\model\user_friend;
use funch\date;

class friend extends \app\Base
{
    /**
     * 增加好友关系,双向的
     */
    public function add_friend($user_id, $user_id2)
    {
        $this->transactionManager->get();
        $re1 = $this->add_friend2($user_id, $user_id2);
        if (is_string($re1)) {
            $this->transactionManager->rollback();
            return $re1;
        }

        $re2 = $this->add_friend2($user_id2, $user_id);
        if (is_string($re2)) {
            $this->transactionManager->rollback();
            return $re2;
        }

        $this->transactionManager->commit();
        return true;
    }

    private function add_friend2($user_id, $user_id2)
    {
        # 判断好友关系
        $infomodel = user_friend::findFirst([
            'user_id=:user_id: and user_id2 =:user_id2:',
            'bind' => [
                'user_id' => $user_id,
                'user_id2' => $user_id2
            ]
        ]);
        if ($infomodel instanceof user_friend) {
            # 已经是好友了
        } else {
            $modelF = new user_friend();
            $modelF->setData([
                'user_id' => $user_id,
                'user_id2' => $user_id2,
                'create_time' => date::mysql()
            ]);
            if (!$modelF->save()) {
                return $modelF->getMessage();
            }
        }
        return true;
    }

    public function del_friend($user_id, $user_id2)
    {

        $this->transactionManager->get();
        $re1 = $this->del_friend2($user_id, $user_id2);
        if (is_string($re1)) {
            $this->transactionManager->rollback();
            return $re1;
        }

        $re2 = $this->del_friend2($user_id2, $user_id);
        if (is_string($re2)) {
            $this->transactionManager->rollback();
            return $re2;
        }

        $this->transactionManager->commit();
        return true;

    }

    /**
     * 删除好友动作
     * @param $user_id
     * @param $user_id2
     */
    private function del_friend2($user_id, $user_id2)
    {

        # 判断好友关系
        $infomodel = user_friend::findFirst([
            'user_id=:user_id: and user_id2 =:user_id2:',
            'bind' => [
                'user_id' => $user_id,
                'user_id2' => $user_id2
            ]
        ]);
        if ($infomodel instanceof user_friend) {
            # 已经是好友了
            if (!$infomodel->delete()) {
                # 删除好友失败!

                return $infomodel->getMessage();
            }
        }
        # 不是好友 返回true
        return true;
    }

}