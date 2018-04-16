<?php

namespace app\controller;

use app\Controller;

/**
 *  Lv 等级的控制器
 * Class EmailController
 * @package apps\home\controllers
 */
class Lv extends Controller
{
    /**
     * 公共的等级信息
     * @return \Phalcon\Http\Response|\Phalcon\Http\ResponseInterface
     */
    public function p_info()
    {
        $user_id = $this->request->get('user_id', 'int', 0);
        $service = new \logic\user\Lv();
        $re = $service->info($user_id);
        return $this->restful_return($re);

    }

    /**
     * 信息
     * @return \Phalcon\Http\Response|\Phalcon\Http\ResponseInterface
     */
    public function info()
    {

        $service = new \logic\user\Lv();
        $re = $service->info($this->user_id);
        return $this->restful_return($re);
    }


}