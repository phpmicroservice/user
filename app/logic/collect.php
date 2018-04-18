<?php

namespace app\logic;

/**
 *
 * Class collect
 * @package app\logic
 */
class collect extends \app\Base
{
    /**
     * 判断是否收藏
     * @param $user_id
     * @param $re_id
     * @param $type
     */
    public static function is_collect($re_id, $type, $user_id)
    {
        $info = \app\model\user_collect::findFirst([
            'user_id =:user_id: and type = :type: and numerical =:numerical: ', 'bind' => [
                'user_id' => $user_id,
                'type' => $type,
                'numerical' => $re_id
            ]
        ]);

        if ($info) {
            return true;
        } else {
            return false;
        }
    }

    /**
     * 取消收藏
     * @param $data
     */
    public function clear($data)
    {
        return service\Collect::cancel($data);
    }

    public function add($data)
    {
        return service\Collect::add($data);
    }

    /**
     * 列表
     * @param $user_id
     * @param $type
     * @param $page
     * @param int $now
     */
    public function lists($user_id, $type, $page, $now = 10, $info = false)
    {
        $builder = $this->modelsManager->createBuilder()
            ->from(model\user_collect::class)
            ->orderBy("create_time")
            ->where('user_id =:user_id: ', ['user_id' => $user_id])
            ->andWhere('type=:type:', ['type' => $type]);
        $paginator = new \core\Paginator\Adapter\QueryBuilder(
            [
                "builder" => $builder,
                "limit" => $now,
                "page" => $page,
            ]
        );
        $data = $paginator->getPaginate(function ($data) {
            $array = $data->items->toArray();
            $data->items = \tool\Arr::for_index($array, 'user_id', function ($array_list) {
                return \app\logic\User::get_userlist_uidarr($array_list);
            });
            return $data;
        });
        if ($info) {
            $this->call_list($data);
        }
        return $data;
    }


    /**
     *
     * @param $re
     */
    private function call_list($re)
    {
        $array = $re->items;
        if ($array) {
            if ($array[0]['type'] == 'bbs') {
                $array = \tool\Arr::for_index($array, ['numerical'], function ($id_list) {
                    return \logic\Bbs\BbsService::id2list($id_list);

                }, false, 're_info');
            } else {
                $array = \tool\Arr::for_index($array, ['numerical'], function ($id_list) {
                    return \logic\Article\ArticleService::ids2list($id_list);

                }, false, 're_info');
            }

        }
        $re->items = $array;
        return $re;

    }


}