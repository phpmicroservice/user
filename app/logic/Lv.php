<?php

namespace app\logic;

use app\model\user_lv;
use app\model\user_lv_config;
use app\validator\user_exist;
use pms\Validation;

/**
 * 等级服务层
 * Class Lv
 * @package logic\user
 */
class Lv extends \app\Base
{
    /**
     * 用户+标识 =等级
     * @param $identifying 标识
     * @param $user_id 用户id
     * @return int 等级
     */
    public static function identifying_lv(string $identifying, int $user_id): int
    {
        $infoModel = user_lv::findFirst([
            'identifying=:identifying: and user_id =:user_id:',
            'bind' => [
                'user_id' => $user_id,
                'identifying' => $identifying
            ]
        ]);
        if ($infoModel instanceof user_lv) {
            return (int)$infoModel->grade;
        }
        return 1;
    }

    /**
     * 等级信息
     * @param $user_id
     * @throws \Phalcon\Validation\Exception
     */
    public function info(int $user_id)
    {
        \pms\output($user_id, 'user_id44');
        # 验证用户是否存在
        $va = new Validation();
        $va->add_Validator('user_id', [
            'name' => user_exist::class,
            'message' => 'user_exist'
        ]);
        if (!$va->validate(['user_id' => $user_id])) {
            return $va->getMessages();
        }
        $modelList = \app\model\user_lv::findByuser_id($user_id);
        $data = $modelList->toArray();
        return $this->list2arr(array_column($data, null, 'identifying'));
    }

    /**
     * 初始化等级信息
     * @param $user_id
     */
    private function list2arr($list)
    {
        # 读取等级类型
        $lv_config = $this->gCache->getc('lv_config_array', function () {
            $list = user_lv_config::find();
            \pms\output($list->toArray(), 'llvconfig');
            return array_column($list->toArray(), null, 'identifying');
        }, 600);
        $arr = [];
        foreach ($lv_config as $key => $value) {
            $arr[$key] = $list[$key] ?? 1;
        }
        return $arr;
    }

    /**
     * 增加成长
     * @param $user_id 用户id
     * @param $identifying 等级类型标示
     * @param $grow_number 成长值
     */
    public function add_grow($user_id, $identifying, $grow_number, $beizhu)
    {
        $service = new service\lv();
        return $service->add_grow($user_id, $identifying, $grow_number, $beizhu);
    }
}