<?php

namespace logic\user;

use logic\user\model\user_lv;

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
     */
    public function info($user_id)
    {
        $modelList = model\user_lv::findByuser_id($user_id);
        $data = $modelList->toArray();

        return array_column($data, null, 'identifying');
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