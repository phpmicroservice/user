<?php

namespace app\controller;

use app\Controller;
use logic\user\config;


/**
 * 设置控制器
 * Class ConfigController
 * @package app\controller
 */
class Options extends Controller
{
    private $p = [
        'type' => ['get', 'type', 'string', 'article'],
        'numerical' => ['get', 're_id', 'int', 0]
    ];

    public function set_config()
    {
        $name = $this->request->get('name', 'string', '');
        $value = $this->request->get('value', 'int', 0);
        $config = new Options();
        $re = $config->set_config($this->user_id, $name, $value);
        return $this->restful_return($re);

    }

}