<?php

namespace app\logic;

/**
 * Created by PhpStorm.
 * User: saisai
 * Date: 17-7-12
 * Time: 下午7:49
 */
class EventConfig extends \core\Event\EventConfig
{

    public $name = [];

    # home 分组
    public $home = [
        'timing' => [
            Task::class => 'timing'
        ]
    ];
}