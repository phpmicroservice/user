<?php

namespace app;


/**
 * 主控制器
 * Class Controller
 * @property \Phalcon\Cache\BackendInterface $gCache
 * @property \Phalcon\Config $dConfig
 * @property \pms\Validation\Message\Group $message
 * @property \pms\bear\Client $clientSync
 * @property \pms\bear\ClientSync $proxyCS
 * @package app\controller
 */
class Controller extends \pms\Controller
{
    public $user_id;

    /**
     * 初始化
     * @param $connect
     */
    public function initialize()
    {
        if (is_object($this->session)) {
            $this->user_id = $this->session->get('user_id');
        } else {
            $this->user_id = 0;
        }
    }




    /**
     * 发送消息
     * @param $re
     */
    public function send($re)
    {
        if ($re instanceof \pms\Validation\Message\Group) {
            # 错误消息
            $d = $re->toArray();
            $this->connect->send_error($d['message'], $d['data'], 424);
        } else {
            if(is_string($re)){
                $this->connect->send_error($re, '失败');
            }else{
                $this->connect->send_succee($re, '成功');
            }
        }
    }


}