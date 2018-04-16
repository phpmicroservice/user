<?php

namespace logic\user\Tag\model;
/**
 * Created by PhpStorm.
 * User: saisai
 * Date: 17-6-5
 * Time: 下午2:44
 */
class Tag extends \core\CoreModel
{
    /**
     * @param $tag_id_list
     */
    public function id_list2List($tag_id_list)
    {
        return self::query()->inWhere('id', $tag_id_list)->execute();

    }
}