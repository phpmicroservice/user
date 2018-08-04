<?php

namespace app\logic;

use app\model\user_info;
use app\model\user_tel;

/**
 * Description of Reg
 *
 * @author Dongasai
 */
class Reg extends \app\Base
{


    /**
     *
     * 多服务协同注册流程
     */
    public function reg_s($data)
    {
        # 涉及多服务同时同时更新采用全局事务
        $task_data = [
            'name' => 'RegsTx',
            'data' => $data
        ];

        $result = $this->swooleServer->taskwait($task_data, 20, -1);
        var_dump($result);
        if ($result === false) {
            # 超时!
            return "服务内部处理超时!";
        }
        if (is_string($result['re'])) {
            # 失败
            if(is_string($result['message'])){
                return $result['message'];
            }
            return $result['re'];
        }
        return [true, $result['xid']];
    }


    /**
     * 账户注册
     * @param type $username 用户名
     * @param type $password 密码
     * @return array|string 成功返回数据,失败返回失败信息
     */
    public function regAction($data)
    {
        # 进行验证
        $validation = new \app\validation\Reg();
        var_dump($validation->validate($data));
        if (!$validation->validate($data)) {
            return $validation->getErrorMessages();
        }

        $this->reg();
    }

    /**
     * 注册完成之后的操作
     * @param $user_id
     */
    private function initReg($user_id, $data)
    {
        # 初始化 用户信息表
        $model = new user_info();
        $model->setData([
            'user_id' => $user_id,
            'gender' => $data['gender']??-1,
            'birthday' => $data['birthday']??'1990-10-10',
            'personalized' => $data['personalized']??'这个家伙没有填写个性签名!!',
            'area' => $data['area']??'653221',
            'headimg' => $data['headimg']?? 0,
            'lock' => 0,
            'nickname' => $data['nickname']
        ]);
        if (!$model->save()) {
            return $model->getMessage();
        }
        if (isset($data['tel'])) {
            # 增加绑定手机
            $data_tel = [
                'user_id' => $user_id,
                'tel' => $data['tel'],
                'status' => 1
            ];
            $modeltel = new user_tel();
            $modeltel->setData($data_tel);
            if (!$modeltel->save()) {
                return $model->getMessage();
            }
        }
        return true;
    }

    /**
     * 增加用户
     * @param $data username password  password2
     * @return array|bool|string
     */
    public function add($data)
    {
        # 进行验证
        $validation = new \app\validation\Reg();
        if (!$validation->validate($data)) {
            return $validation->getErrorMessages();
        }
        return $this->reg($data);
    }

    /**
     * 注册用户
     * @param $data
     * @return array|bool|string
     */
    private function reg($data)
    {

        # 验证完成
        $userModel = new \app\model\user();
        $security = new \Phalcon\Security();
        $data['username'] = $data['username'] ? $data['username'] : uniqid();
        //密码加密
        $data['password'] = $security->hash($data['password'], 2);
        //进行注册 增加用户信息

        $data['nickname'] = $data['nickname'] ? $data['nickname'] : $data['username'];
        $data['create_time'] = time();
        $data['update_time'] = 0;
        $data['edit_username'] = 0;
        
        $re33 = $userModel->save($data);
        if ($re33 === false) {
            return $userModel->getMessage();
        }
        # 进行注册后的操作
        $re42 = $this->initReg($userModel->id, $data);
        if (is_string($re33)) {
            return $re42;
        }
        return ['user_id' => $userModel->id];
    }


    /**
     * 验证是否可以注册
     * @param $data
     */
    public function validation($data)
    {
        # 进行验证
        $validation = new \app\validation\add_user();
        if (!$validation->validate($data)) {
            return false;
        }
        return true;
    }
}
