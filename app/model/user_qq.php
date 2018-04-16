<?php
/**
 * Created by PhpStorm.
 * User: Dongasai
 * Date: 2017/11/24
 * Time: 18:25
 */

namespace app\model;


class user_qq extends \pms\Mvc\Model
{

    public function initialize()
    {
        $this->hasOne('openid',
            user_qq_info::class
            , 'openid', [
                'alias' => 'info']);

    }
}