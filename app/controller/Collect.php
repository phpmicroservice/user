<?php

namespace app\controller;

use app\Controller;


/**
 * 收藏控制器
 * Class CollectController
 * @package app\controller
 */
class Collect extends Controller
{
    private $p = [
        'type' => ['get', 'type', 'string', 'article'],
        'numerical' => ['get', 're_id', 'int', 0]
    ];

    /**
     * 取消收藏
     */
    public function clear()
    {
        $data = $this->getData($this->p);
        $data['user_id'] = $this->user_id;
        $CollectS = new \app\logic\collect();
        $re = $CollectS->clear($data);
        return $this->send($re);

    }

    /**
     * 增加收藏
     */
    public function add()
    {
        $data = $this->getData($this->p);
        $data['user_id'] = $this->user_id;
        $CollectS = new \app\logic\collect();
        $re = $CollectS->add($data);
        return $this->send($re);
    }
}