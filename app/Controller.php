<?php

namespace app;

use Phalcon\Validation\Message;


/**
 * 主控制器
 * Class Controller
 * @property \Phalcon\Cache\BackendInterface $gCache
 * @property \Phalcon\Config $dConfig
 * @property \Phalcon\Validation\Message\Group $message
 * @package app\controller
 */
class Controller extends \pms\Controller
{
    /**
     * 初始化
     * @param $connect
     */
    protected function onInitialize($connect)
    {
        $this->di->setShared('message', function () {
            return new Message\Group();
        });

    }

    /**
     * 获取数据
     * @param $pa
     */
    public function getData()
    {
        return $this->connect->getData();
    }
}