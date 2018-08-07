<?php

namespace app\filterTool;

use pms\FilterTool\FilterTool;

/**
 * 邮件找回密码
 * Class Emailretpw
 * @package app\filterTool
 */
class Emailretpw extends FilterTool
{
    protected function initialize()
    {
        $this->_Rules[]=['email','email'];
        $this->_Rules[]=['code','string'];
        $this->_Rules[]=['new_password1','string'];
        $this->_Rules[]=['new_password2','string'];
        parent::initialize();
    }

}