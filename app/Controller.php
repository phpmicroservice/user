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
 * @property \Logger $logger
 * @package app\controller
 */
class Controller extends \pms\Controller\Tcp
{

    public $user_id;
    private $passing = false;

    /**
     * 初始化
     * @param $connect
     */
    public function initialize()
    {
        parent::initialize();
        
        $sid = $this->connect->getData('sid');
        $this->session = new \pms\Session($sid);
        if (is_object($this->session)) {
            $this->user_id = $this->session->get('user_id');
        } else {
            $this->user_id = 0;
        }
        $this->passing = $this->connect->getData('p');
    }

    /**
     * 发送一个请求
     * @param $router
     * @param $data
     * @return bool
     */
    public function send_ask($server, $router, $data)
    {
        return $this->send([
                    's' => $server,
                    'r' => $router,
                    'd' => $data
        ]);
    }

    /**
     * 发送一个成功
     * @param $m 消息
     * @param array $d 数据
     * @param int $t 类型/控制器
     */
    public function send_succee($d = [], $m = '成功', $t = '')
    {
        $data = [
            'm' => $m,
            'd' => $d,
            'e' => 0,
            't' => empty($t) ? $this->connect->getRouterString() : $t
        ];
        return $this->send($data);
    }

    /**
     * 发送一个错误的消息
     * @param $m 错误消息
     * @param array $d 错误数据
     * @param int $e 错误代码
     * @param int $t 类型,路由
     */
    public function send_error($m, $d = [], $e = 1, $t = '')
    {
        $data = [
            'm' => $m,
            'd' => $d,
            'e' => $e,
            't' => empty($t) ? $this->getRouter() : $t
        ];
        return $this->send($data);
    }

    /**
     * 发送数据
     * @param type $data
     */
    public function send($data)
    {
        if ($this->passing) {
            if(is_object($data)){
                $data->p = $this->passing;
            }else{
                $data['p'] = $this->passing;
            }
        }
        
        if(is_object($data)){
            $data->f = strtolower(SERVICE_NAME);
        }else{
            $data['f'] = strtolower(SERVICE_NAME);
        }
        
        \pms\Output::output($data,'send');
        $this->connect->send($data);
    }

}
