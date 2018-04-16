<?php

namespace app\logic\tag\model;

/**
 * Class tag_relation
 * @package logic\user\Tag\model
 */
class tag_relation extends \pms\Mvc\Model
{
    /**
     * 获取这个对象的关联标签id
     * @param $user_id
     * @param $re_id
     * @param $type
     */
    public static function find4re($user_id, $re_id, $type)
    {
        return self::query()->where('user_id= :user_id:', ['user_id' => $user_id])
            ->andWhere('re_id=:re_id:', ['re_id' => $re_id])
            ->andWhere('type=:type:', ['type' => $type])->columns('tag_id');
    }

    /**
     * 根据标签获取关联id
     * @param $user_id
     * @param $tag
     * @param $type
     */
    public static function id2list($user_id, $tag, $type)
    {
        $list = self::query()
            ->where('user_id= :user_id:', ['user_id' => $user_id])
            ->andWhere('tag_id=:tag_id:', ['tag_id' => $tag])
            ->andWhere('type=:type:', ['type' => $type])
            ->columns('re_id')
            ->execute();

        return array_column($list->toArray(), 're_id');
    }

}