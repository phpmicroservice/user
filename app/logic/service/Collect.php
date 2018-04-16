<?php

namespace logic\user\service;

use logic\user\model as thisModel;

/**
 * 用户收藏的服务层
 * @author Dongasai <1514582970@qq.com>
 */
class Collect extends \app\Base
{

    /**
     * 获取收藏列表
     * @param type $uid
     * @param type $type
     * @param type $pageData
     */
    public static function lists($user_id, $type, $pageData)
    {
        $type = strtolower($type);
        $modelsManager = \Phalcon\Di::getDefault()->get('modelsManager');
        $builder = $modelsManager->createBuilder()
            ->where('user_id =' . $user_id)
            ->andwhere(' type = "' . strtolower($type) . '"')
            ->from(\app\model\user_collect::class)
            ->groupBy("id")
            ->orderBy("id");
        $paginator = new \Phalcon\Paginator\Adapter\QueryBuilder(
            [
                "builder" => $builder,
                "limit" => $pageData['row'],
                "page" => $pageData['now_page'],
            ]
        );
        $data = $paginator->getPaginate();
        #查询到了数据
        if ($data->total_items == 0) {
            #没有查询到数据
            return $data;
        } else {
            # 存在数据就对数据进行处理
            $class_name_list = [
                'article' => '\logic\Article\model\article'
            ];
            $id_list = array_column($data->items->toarray(), 'numerical');
            $re = $class_name_list[$type]::query()->inWhere('id', $id_list)->execute();
            $re = \tool\Arr::array_change_index($re->toArray(), 'id');
            $data->items = $data->items->toarray();
            foreach ($data->items as &$val) {
                $val[$type . '_info'] = $re[$val['numerical']];
            }
        }
        return $data;
    }

    /**
     * 取消收藏
     * @param type $user_id
     * @param type $Collect_id
     */
    public static function cancel($data)
    {
        $oldModel = thisModel\user_collect::findFirst([
            'type= :type: and user_id=:user_id: and numerical=:numerical: ',
            'bind' => [
                'type' => $data['type'],
                'user_id' => $data['user_id'],
                'numerical' => $data['numerical']
            ]
        ]);
        if (!$oldModel) {
            return '_empty-error';
        }
        if ($oldModel->delete() === false) {
            return $oldModel->getMessage();

        }
        return true;


    }

    /**
     * 增加收藏
     * @param type $user_id
     * @param type $type
     * @param type $numerical
     */
    public static function add($data)
    {
        $data['create_time'] = time();
        # 进行验证
        $validation = new \app\validation\Collect();
        $validation->validate($data);
        if ($validation->getMessage()) {
            return $validation->getMessage();
        }
        $user_collectModel = new \app\model\user_collect();
        $user_collectModel->setData($data);
        $re = $user_collectModel->save();
        if ($re === false) {
            return $user_collectModel->getMessage();
        } else {
            return true;
        }
    }

}
