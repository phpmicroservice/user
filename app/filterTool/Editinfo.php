<?php

namespace app\filterTool;

use pms\FilterTool\FilterTool;

/**
 * 修改信息的过滤器
 * Class Editinfo
 * @package app\filterTool
 */
class Editinfo extends FilterTool
{
    protected $_Rules = [
        ['nickname','string'],
        ['headimg','int'],
        ['gender','int'],
        ['birthday','string'],
        ['personalized','string'],
        ['area','string']
    ];

}