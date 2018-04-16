<?php

namespace app\model;

/**
 * 用户收藏表模型
 * @author Dongasai
 */
class user_collect extends \pms\Mvc\Model
{

    public $type = ''; #类型

    /**
     * 创建数据之前
     */
    public function beforeCreate()
    {
        $this->type = strtolower($this->type);
    }

}
