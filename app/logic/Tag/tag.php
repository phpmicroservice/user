<?php
/**
 * Created by PhpStorm.
 * User: saisai
 * Date: 17-6-5
 * Time: 下午4:17
 */

namespace logic\user\Tag;


class tag extends \app\Base
{
    /**
     * @param $tag_id_list
     */
    public static function id_list2List($tag_id_list)
    {
        return model\Tag::id_list2List($tag_id_list);
    }
}