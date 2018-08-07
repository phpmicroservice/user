<?php

namespace app\task;

use app\model\user;
use pms\Task\Task;
use Phalcon\Mvc\Model\Transaction\Failed as TxFailed;
use Phalcon\Mvc\Model\Transaction\Manager as TxManager;

class Inituser extends Task
{

    public function run()
    {
        $logger=\Phalcon\Di::getDefault()->get('logger');
        $logger->info(__CLASS__);
        # 初始化用户,读取需要初始化的用户
        for ($i = 1; $i <= 10; $i++) {
            $i = $this->updateuser();
        }
    }
    public function end(){
        
    }

    private function updateuser()
    {
        $usermodel = user::findFirst([
            'init =0'
        ]);
        if (!($usermodel instanceof user)) {
            # 没有需要更新的用户
            return 11;
        }
        # 查看其是否已经初始化
        if ($this->is_init($usermodel)) {
            # 已经初始化完成
            $usermodel->init=1;
            $usermodel->save();
        }else{
            # 没有初始化完成
            $this->init($usermodel);
        }

    }

    private function is_init(user $usermodel)
    {
        # 查询是否已经存在个人角色
        $proxyCS = $this->getProxyCS();
        var_dump($usermodel->id);
        $re  =$proxyCS->request_return('rbac','/server/in_role',[
            'user_id'=>$usermodel->id,
            'role_id'=>102
        ]);
        if($re['e']){
            return false;
        }
        return $re['d'];
    }

    /**
     * 初始化
     * @param user $usermodel
     */
    private function init(user $usermodel)
    {
        $proxyCS = $this->getProxyCS();
        $re  =$proxyCS->request_return('rbac','/server/add_role',[
            'user_id'=>$usermodel->id,
            'role_id'=>102
        ]);
        var_dump($re);
    }


    /**
     *
     * @return \pms\bear\ClientSync
     */
    protected function getProxyCS(): \pms\bear\ClientSync
    {
        return \Phalcon\Di::getDefault()->get('proxyCS');
    }


}