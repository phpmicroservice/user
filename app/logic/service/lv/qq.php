<?php


namespace logic\user\service\lv;
/**
 * Class qq 仿QQ的等级计算
 * @package logic\user\service\lv
 */
class qq implements arithmetic
{
    private $factor = 2;

    public function __construct($factor = 2)
    {
        $this->factor = $factor;
    }

    /**
     * 等级 转换 成长
     * @param int $grade 等级
     * @return int 天数
     */
    public function grade2grow(int $grade): int
    {
        return pow($grade, 2) + (4 * $grade);


    }

    /**
     * 成长值 -> 等级
     * @param int $grow
     * @return int
     */
    public function grow2grade(int $grow): int
    {
        $grow_0 = 0;
        for ($grade_0 = 1; $grade_0 < 9999; $grade_0++) {
            $grow_0 = pow($grade_0, 2) + 4 * $grade_0;
            if ($grow_0 > $grow) {
                return $grade_0;
            }
        }


    }

    /**
     * 升级 所需 天数
     * @param $now_lv 当前等级
     * @return int 天数
     */
    public function days_upgrade(int $now_lv): int
    {
        return 2 * $now_lv + 5;
    }
}