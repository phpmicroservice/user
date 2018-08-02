<?php

namespace app\logic;

/**
 * 消息
 * Class Message
 * @package logic\user
 * @property service\Message $Message
 */
class Message extends \app\Base
{
    public function __construct()
    {
        $this->di->setShared('Message', function () {
            return new service\Message();
        });
    }

    /**
     * 发送消息
     * @param $user_id
     * @param $to_id
     * @param $title
     * @param $content
     * @return bool|string
     */
    public static function send($user_id, $to_id, $title, $content, $type = 'text')
    {
        return service\Message::sendMsg($user_id, $to_id, $title, $content, $type);
    }

    /**
     * @param $user_id
     * @param $id
     */
    public function info4user($user_id, $id)
    {
        $model = \app\model\user_message::findFirstByid($id);
        if (!($model instanceof \app\model\user_message)) {
            return '_empty-error';
        }
        if ($model->to_uid == $user_id || $model->from_uid == $user_id) {

        } else {
            return '_empty-error';
        }
        $data = $model->toArray();
        $info = new \app\logic\Info();
        $data['from_info'] = $info->p_info($data['from_uid']);
        $data['to_info'] = $info->p_info($data['to_uid']);
        return $data;
    }

    /**
     * 用户的消息列表
     * @param $user_id
     * @param string $type
     * @param int $page
     * @param int $row
     * @return mixed
     */
    public function list4user($user_id, $type = 'to', $is_read = -1, $page = 1, $row = 10)
    {
        return $this->Message->messages($user_id, $type, $is_read, $page, $row);
    }

    /**
     * 将消息设置已读
     * @param $data
     * @return bool|\Phalcon\Mvc\Model\MessageInterface[]|string
     */
    public function read($data)
    {
        return $this->Message->readMsg($data['user_id'], $data['id']);
    }


}
