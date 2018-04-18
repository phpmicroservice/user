<?php

namespace app\logic;

use app\model\user_realauth;

/**
 * 实名认证
 * Class realauth
 * @package logic\user
 */
class Realauth extends \app\Base
{

    private $user_id;

    public function __construct($user_id = 0)
    {
        $this->user_id = $user_id;
    }


    /**
     * 实名认证信息
     * @param $user_id
     * @return bool
     */
    public function p_info($user_id)
    {
        $mode = user_realauth::findFirstByuser_id($user_id);
        if (!($mode instanceof user_realauth)) {
            return false;
        } else {
            if ($mode->status == 1) {
                return true;
            } else {
                return false;
            }
        }

    }

    /**
     * 认证通过
     * @param $user_id
     */
    public function realauth_ok()
    {
        $mode = user_realauth::findFirstByuser_id($this->user_id);
        if (!($mode instanceof user_realauth)) {
            return '_empty-error';
        }
        if ($mode->status != 0) {
            return '_status-error';
        }
        $mode->status = 1;
        if (!$mode->save()) {
            return $mode->getMessage();
        }
        Message::send(1, $this->user_id, '实名认证审核进度', '实名认证通过!');
        return true;
    }

    /**
     * 认证不通过
     * @param $user_id
     * @return bool|string
     */
    public function realauth_no()
    {
        $mode = user_realauth::findFirstByuser_id($this->user_id);
        if (!($mode instanceof user_realauth)) {
            return '_empty-error';
        }
        if ($mode->status != 0) {
            return '_status-error';
        }

        $mode->status = -1;
        if (!$mode->save()) {
            return $mode->getMessage();
        }
        # 发送消息
        Message::send(1, $this->user_id, '实名认证审核进度', '实名认证不通过!');
        return true;
    }


    /**
     * 信息
     * @return mixed
     */
    public function info()
    {
        $mode = user_realauth::findFirstByuser_id($this->user_id);
        if (!($mode instanceof user_realauth)) {
            return '_empty-error';
        }
        $mode->img1 = \logic\Attachment\attachmentArray::list4id($mode->img1);
        $mode->img2 = \logic\Attachment\attachmentArray::list4id($mode->img2);
        $mode->img3 = \logic\Attachment\attachmentArray::list4id($mode->img3);
        return $mode;
    }

    /**
     * 认证列表
     */
    public function realauthlist($where, $page)
    {
        $builder = $this->modelsManager->createBuilder()
            ->from(\app\model\user_realauth::class);
        if (!empty($where['user_id'])) {
            $builder->andWhere('user_id = :user_id:', [
                'user_id' => $where['user_id']
            ]);
        }

        if (isset($where['status']) and $where['status'] > -1) {
            $builder->andWhere('status = :status:', [
                'status' => $where['status']
            ]);
        }
        $paginator = new \Phalcon\Paginator\Adapter\QueryBuilder(
            [
                "builder" => $builder,
                "limit" => 20,
                "page" => $page,
            ]
        );
        return $paginator->getPaginate(function ($dataats) {
            return $dataats;
        });
    }

    /**
     *
     * @param $data
     */
    public function submit($data)
    {
        $data['user_id'] = $this->user_id;
        $validation = new validation\Realauth();
        if ($validation->validate($data) === false) {
            return $validation->getMessage();
        }
        # 读取数据并进行修改
        $mode = user_realauth::findFirstByuser_id($data['user_id']);

        if ($mode instanceof user_realauth) {
            # 存在数据
        } else {
            $mode = new  user_realauth();
        }

        # 处理附件数据
        $this->transactionManager->get();
        $attachmentArray = new \logic\Attachment\attachmentArray();
        $data['status'] = 0;
        $data['img1'] = $attachmentArray->one($data['user_id'], 'realauth', $mode->img1, $data['img1']);
        $data['img2'] = $attachmentArray->one($data['user_id'], 'realauth', $mode->img2, $data['img2']);
        $data['img3'] = $attachmentArray->one($data['user_id'], 'realauth', $mode->img3, $data['img3']);

        $mode->setData($data);
        if ($mode->save() === false) {
            $this->transactionManager->rollback();
            return $mode->getMessage();
        }
        $this->transactionManager->commit();
        return true;


    }

}