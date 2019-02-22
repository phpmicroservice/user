<?php

namespace app\task;

use app\filterTool\Regs;
use app\logic\Reg;
use pms\Task\TaskInterface;

/**
 * 多服务协同注册的全局事务
 * Class RegsTx
 * @package app\task
 */
class RegsTx extends \pms\Task\TxTask implements TaskInterface
{
    public function end()
    {

    }

    /**
     * 在依赖处理之前执行,没有返回值
     */
    protected function b_dependenc()
    {
        $data = $this->getData();
        \pms\output($data, '272727');
    }

    /**
     * 事务逻辑内容,返回逻辑执行结果,
     * @return bool false失败,将不会再继续进行;true成功,事务继续进行
     */
    protected function logic()
    {

        $data = $this->getData();
        var_dump($data);
        $ft = new Regs();
        $ft->filter($data);
        # 进行过滤
        $logic = new Reg();
        $re = $logic->regAction($data);
        var_dump($re);
        if (is_string($re)) {
            # 失败
            return $re;
        }
        $txdata = $this->getData();
        $txdata['user_id'] = $re['user_id'];
        if(!is_array($txdata['server'])){
            return 'data-server-error';
        }
        foreach ($txdata['server'] as $server) {
            $this->add_dependenc($server, 'regs', $txdata);
        }
        \pms\output($data, '515151');
        # 得到结果,开始追加全局事务
        if (!$this->dependency()) {
            return "追加事务失败!";
        }
        return true;
    }
}