<?php

namespace app\controller;

use app\Controller;

/**
 * 消息 message
 * Class MessageController
 * @package app\controller
 * @property \app\logic\Message $MessageService
 */
class Message extends Controller
{
    public function initialize()
    {
        parent::initialize();

    }

    /**
     * 设置消息为已读
     */
    public function read()
    {
        $data=[];
        $data['id'] = $this->getData('id',0);
        $data['user_id'] = $this->user_id;
        $messageService=new \app\logic\Message();
        $re = $messageService->read($data);
        return $this->send($re);

    }

    /**
     * 站内信列表
     * @return \Phalcon\Http\Response|\Phalcon\Http\ResponseInterface
     */
    public function list4user()
    {
        $type = $this->getData('type', 'to');
        $page = $this->getData('p', 1);
        $is_read = $this->getData('is_read', -1);
        $messageService=new \app\logic\Message();
        $re = $messageService->list4user($this->user_id, $type, $is_read, $page);
        return $this->send($re);
    }

    /**
     * 站内信详情
     * @return \Phalcon\Http\Response|\Phalcon\Http\ResponseInterface
     */
    public function info()
    {
        $id = $this->getData('id', 'int', 0);
        $messageService=new \app\logic\Message();
        $re = $messageService->info4user($this->user_id, $id);
        return $this->send($re);
    }

    /**
     * 发送站内信
     */
    public function send_mes()
    {
        $to_user_id = $this->getData('to_user_id');
        $title = $this->getData('title');
        $content = $this->getData('content');
        $data['user_id'] = $this->user_id;
        $messageService=new \app\logic\Message();
        $re = $messageService->send($this->user_id, $to_user_id, $title, $content);
        return $this->send($re);
    }
}