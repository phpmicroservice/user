<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

namespace app\model;

/**
 * Description of user_praise
 *
 * @author Dongasai
 */
class user_praise extends \pms\Mvc\Model
{

    public function beforeSave()
    {
        # 设置小写
        if (!empty($this->to_type)) {
            $this->to_type = strtolower($this->to_type);
        }
    }

}
