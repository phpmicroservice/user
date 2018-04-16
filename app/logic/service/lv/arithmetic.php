<?php

namespace logic\user\service\lv;

interface arithmetic
{
    /**
     * 等级转换成长
     * @param int $grade 等级
     * @return int 天数
     */
    public function grade2grow(int $grade): int;

    /**
     * 成长值所对应的等级
     * @param int $grow
     * @return int
     */
    public function grow2grade(int $grow): int;

    /**
     * 升级所需天数
     * @param $now_lv 当前等级
     * @return int 天数
     */
    public function days_upgrade(int $now_lv): int;

}