<?php

namespace app\logic\tag;

/**
 * Created by PhpStorm.
 * User: saisai
 * Date: 17-6-5
 * Time: 下午2:58
 */
class relation extends \app\Base
{

    /**
     * 处理关联
     * @param $data
     */
    public function handle($data)
    {
        $this->transactionManager->get();
        $user_id = $data['user_id'];
        $type = $data['type'];
        $re_id = $data['re_id'];
        $tags = $data['title'];

        foreach ($tags as $tag) {
            # chuli
            $tag_id = $this->handel_tag($user_id, $re_id, $type, $tag);
            if (is_string($tag_id)) {
                $this->transactionManager->rollback();
                return $tag_id;
            }
        }
        $this->transactionManager->commit();
        return true;
    }

    /**
     * 处理单个标签
     * @param $re_id
     * @param $type
     * @param $tag
     */
    private function handel_tag($user_id, $re_id, $type, $tag)
    {
        $tag_id = $this->get_tag_id($user_id, $tag);
        if (is_string($tag_id)) {
            return $tag_id;
        }
        $data = [
            'user_id' => $user_id,
            're_id' => $re_id,
            'type' => $type,
            'tag_id' => $tag_id
        ];
        $old_model = model\tag_relation::findFirst([
            're_id = :re_id: and type=:type: and tag_id =:tag_id: and user_id=:user_id:',
            'bind' => $data
        ]);
        if ($old_model) {
            return true;
        }
        $model = new model\tag_relation();
        $model->setData($data);

        if ($model->save() === false) {
            return $model->getMessage();
        }
        return (int)$model->id;


    }

    /**
     * 获取标签id
     * @param $user_id
     * @param $tag
     */
    private function get_tag_id($user_id, $tag)
    {
        $old_model = model\Tag::findFirst([
            'user_id = :user_id: and title=:title:',
            'bind' => [
                'user_id' => $user_id,
                'title' => $tag
            ]
        ]);
        if ($old_model) {
            return (int)$old_model->id;
        }
        $model = new model\Tag();
        $model->setData([
            'user_id' => $user_id,
            'title' => $tag,
            'create_time' => time()
        ]);
        if ($model->save() === false) {
            return $model->getMessage();
        }
        return (int)$model->id;

    }

    /**
     * 这个关联的标签列表
     * @param $user_id
     * @param $re_id
     * @param $type
     */
    public function re_tag($user_id, $re_id, $type)
    {
        $tag_list = model\tag_relation::find4re($user_id, $re_id, $type);
        return tag::id_list2List($tag_list);
    }

    /**
     * 这些标签的关联信息
     * @param $user_id
     * @param $tags
     * @param $type
     */
    public function tag_re($user_id, $tags, $type)
    {

        foreach ($tags as $tag) {
            $re_list = model\tag_relation::id2list($user_id, $tag, $type);
            if (isset($re_list_old)) {
                $re_list = array_intersect($re_list, $re_list_old);
            }
            $re_list_old = $re_list;
        }
        return $re_list_old;
    }
}