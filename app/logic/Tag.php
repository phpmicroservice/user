<?php

namespace logic\user;

/**
 * 标签相关
 * Class Tag
 * @package logic\user
 */
class Tag
{
    /**
     * 进行关联
     * @param $data
     */
    public function relation($data)
    {
        $relation = new Tag\relation();
        return $relation->handle($data);
    }


    /**
     * 这个关联的标签列表
     * @param $user_id
     * @param $re_id
     * @param $type
     * @return \Phalcon\Mvc\Model\ResultsetInterface
     */
    public function re_tag($user_id, $re_id, $type)
    {
        $relation = new Tag\relation();
        return $relation->re_tag($user_id, $re_id, $type);
    }

    /**
     * 这个标签的关联信息
     * @param $user_id
     * @param $type
     */
    public function tag_re($user_id, $type, $tags)
    {
        $relation = new Tag\relation();
        return $relation->tag_re($user_id, $tags, $type);
    }


}