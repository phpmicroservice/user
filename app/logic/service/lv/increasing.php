<?php


namespace logic\user\service\lv;

/**
 * 递增 的计算方式
 * Class increasing
 * @package logic\user\service\lv
 */
class increasing implements arithmetic
{

    private $factor;

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
        $grow = 0;
        for ($grade_0 = 1; $grade_0 > $grade; $grade_0++) {
            $grow = $grow + bcadd($this->factor, $grade_0);
        }
        return $grow;

    }

    /**
     * 成长值所对应的等级
     * @param int $grow
     * @return int
     */
    public function grow2grade(int $grow): int
    {
        $grow_0 = 0;
        for ($grade_0 = 1; $grade_0 < 9999; $grade_0++) {
            $grow_0 = $grow_0 + bcadd($this->factor, $grade_0);
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
        return bcadd($this->factor, $now_lv + 1) - bcadd($this->factor, $now_lv);
    }

}