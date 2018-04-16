<?php


namespace app\controller;

use app\Controller;

/**
 * 速评的控制器 praise
 * Class Praise
 * @package app\controller
 */
class Praise extends Controller
{
    /**
     * 增加速评
     */
    public function add()
    {
        $re_id = $this->request->get('re_id', 'int', 0);
        $type = $this->request->get('type', 'string', 'article');
        $Praise = new \logic\user\praise();
        $data = [
            'user_id' => $this->user_id,
            'to_type' => $type,
            'to_numerical' => $re_id
        ];
        $re = $Praise->add($data);
        return $this->send($re);
    }

    /**
     * 速评 取消
     * @return \Phalcon\Http\Response|\Phalcon\Http\ResponseInterface
     */
    public function clear()
    {
        $re_id = $this->request->get('re_id', 'int', 0);
        $type = $this->request->get('type', 'string', 'article');
        $Praise = new \logic\user\praise();
        $data = [
            'user_id' => $this->user_id,
            'to_type' => $type,
            'to_numerical' => $re_id
        ];
        $re = $Praise->clear2($data);
        return $this->send($re);
    }

    /**
     * 对象的速评信息列表
     */
    public function praise_list()
    {
        $re_id = $this->request->get('re_id', 'int', 0);
        $type = $this->request->get('type', 'string', 'article');
        $praise_type = $this->request->get('p_t', 'int', 1);
        $page = $this->request->get('p', 'int', 0);
        $Praise = new \logic\user\praise();
        $data = [
            'to_type' => $type,
            'to_numerical' => $re_id,
            'praise_type' => $praise_type
        ];
        $re = $Praise->praise_list($data, $page, 10);
        return $this->send($re);
    }
}